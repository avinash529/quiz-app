<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    public function home()
    {
        // 1) Try API first. If it fails, use last saved categories from session.
        $allCategories = $this->getCategories();
        if (!empty($allCategories)) {
            session(['available_categories' => $allCategories]);
        } else {
            $allCategories = session('available_categories', []);
        }

        if (empty($allCategories)) {
            return view('home', ['allCategories' => []])
                ->with('error', 'Unable to load categories right now. Please refresh and try again.');
        }

        return view('home', compact('allCategories'));
    }

    public function startQuiz(Request $request)
    {
        // 1) Validate selected category.
        $availableCategories = session('available_categories', []);
        if (empty($availableCategories)) {
            $availableCategories = $this->getCategories();
            session(['available_categories' => $availableCategories]);
        }

        if (empty($availableCategories)) {
            return redirect()->route('home')
                ->with('error', 'Categories are temporarily unavailable. Please try again shortly.');
        }

        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in($availableCategories)],
        ]);

        // 2) Load questions for selected category.
        $questions = $this->getQuestions($validated['category']);
        if (empty($questions)) {
            return redirect()->route('home')
                ->with('error', 'Unable to load quiz questions for this category. Please try another one.');
        }

        // 3) Start fresh quiz session.
        session([
            'questions' => $questions,
            'index' => 0,
            'score' => 0,
            'user_answers' => [],
            'current_options' => [],
        ]);

        return redirect()->route('quiz');
    }

    public function quiz()
    {
        // 1) Read quiz state.
        $questions = session('questions', []);
        $index = (int) session('index', 0);

        if (!isset($questions[$index])) {
            return redirect()->route('home')->with('error', 'Start a new quiz to continue.');
        }

        // 2) Build options for current question.
        $question = $questions[$index];
        $options = $question['incorrectAnswers'];
        $options[] = $question['correctAnswer'];
        shuffle($options);

        // Save options so /answer can validate submitted value.
        session(['current_options' => $options]);

        return view('quiz', [
            'question' => $question,
            'options' => $options,
            'index' => $index,
            'total' => count($questions),
        ]);
    }

    public function answer(Request $request)
    {
        // 1) Read quiz state.
        $questions = session('questions', []);
        $index = (int) session('index', 0);
        $score = (int) session('score', 0);

        if (!isset($questions[$index])) {
            return redirect()->route('home')->with('error', 'Your quiz session has expired. Please start again.');
        }

        // 2) Validate submitted answer (or null if timer auto-submits).
        $currentOptions = session('current_options', []);
        $answerRules = ['nullable', 'string'];
        if (!empty($currentOptions)) {
            $answerRules[] = Rule::in($currentOptions);
        }

        $validated = $request->validate([
            'answer' => $answerRules,
        ]);

        $answer = $validated['answer'] ?? null;
        $correctAnswer = $questions[$index]['correctAnswer'];

        if ($answer !== null && $answer === $correctAnswer) {
            $score++;
        }

        // 3) Store answer, move to next question.
        $userAnswers = session('user_answers', []);
        $userAnswers[$index] = $answer;

        $nextIndex = $index + 1;

        session([
            'index' => $nextIndex,
            'score' => $score,
            'user_answers' => $userAnswers,
            'current_options' => [],
        ]);

        if ($nextIndex >= count($questions)) {
            return redirect()->route('result');
        }

        return redirect()->route('quiz');
    }

    public function result()
    {
        $questions = session('questions', []);
        if (empty($questions)) {
            return redirect()->route('home')->with('error', 'No completed quiz found. Please start a new one.');
        }

        $score = (int) session('score', 0);
        $total = count($questions);
        $percent = $total > 0 ? ($score / $total) * 100 : 0;

        if ($percent > 60) {
            $result = 'Winner';
        } elseif ($percent >= 40) {
            $result = 'Better';
        } else {
            $result = 'Failed';
        }

        return view('result', [
            'questions' => $questions,
            'score' => $score,
            'total' => $total,
            'percent' => $percent,
            'result' => $result,
            'userAnswers' => session('user_answers', []),
        ]);
    }

    private function getCategories(): array
    {
        $response = Http::timeout(10)->get('https://the-trivia-api.com/api/categories');
        if (!$response->ok()) {
            return [];
        }

        $groups = $response->json();
        if (!is_array($groups)) {
            return [];
        }

        $allCategories = [];

        foreach ($groups as $group) {
            if (!is_array($group)) {
                continue;
            }

            foreach ($group as $category) {
                if (is_string($category) && trim($category) !== '') {
                    $allCategories[] = trim($category);
                }
            }
        }

        $allCategories = array_values(array_unique($allCategories));
        sort($allCategories);

        return $allCategories;
    }

    private function getQuestions(string $category): array
    {
        $response = Http::timeout(10)->get('https://the-trivia-api.com/api/questions', [
            'categories' => $category,
            'limit' => 15,
        ]);

        if (!$response->ok()) {
            return [];
        }

        $rawQuestions = $response->json();
        if (!is_array($rawQuestions)) {
            return [];
        }

        $questions = [];

        foreach ($rawQuestions as $q) {
            if (
                !is_array($q)
                || !isset($q['question'], $q['correctAnswer'], $q['incorrectAnswers'])
                || !is_array($q['incorrectAnswers'])
                || !is_string($q['question'])
                || !is_string($q['correctAnswer'])
            ) {
                continue;
            }

            $incorrectAnswers = array_values(array_filter(
                $q['incorrectAnswers'],
                fn ($answer) => is_string($answer) && trim($answer) !== ''
            ));

            if (empty($incorrectAnswers)) {
                continue;
            }

            $questions[] = [
                'question' => html_entity_decode($q['question'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'correctAnswer' => html_entity_decode($q['correctAnswer'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'incorrectAnswers' => array_map(
                    fn ($answer) => html_entity_decode($answer, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    $incorrectAnswers
                ),
            ];
        }

        return $questions;
    }
}
