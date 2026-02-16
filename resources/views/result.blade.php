@extends('layout')

@section('content')

<div class="panel-head">
    <p class="eyebrow">Round Complete</p>
    <h1 class="heading-title">Your Results</h1>
    <p class="heading-sub">Review your score and check the correct answers.</p>
</div>

<div class="score-strip">
    <div class="score-block">
        <span>Score</span>
        <strong>{{ $score }}/{{ $total }}</strong>
    </div>
    <div class="score-block">
        <span>Accuracy</span>
        <strong>{{ round($percent) }}%</strong>
    </div>
    <div class="score-block">
        <span>Status</span>
        <strong class="result-tag {{ strtolower($result) }}">{{ $result }}</strong>
    </div>
</div>

<div class="answers-list">
    @foreach($questions as $q)
        @php
            $userAnswer = $userAnswers[$loop->index] ?? null;
            $isCorrect = $userAnswer !== null && $userAnswer === $q['correctAnswer'];
        @endphp
        <article class="question-box">
            <p class="question-label">Question {{ $loop->iteration }}</p>
            <h3 class="question-text">{{ $q['question'] }}</h3>
            <p class="answer-text">
                Your answer:
                <span class="{{ $isCorrect ? 'answer-good' : 'answer-bad' }}">
                    {{ $userAnswer ?? 'No answer' }}
                </span>
            </p>
            <p class="answer-text">
                Correct answer:
                <span>{{ $q['correctAnswer'] }}</span>
            </p>
        </article>
    @endforeach
</div>

<div class="action-row">
    <a href="{{ route('home') }}" class="btn-style">Play Again</a>
</div>

@endsection
