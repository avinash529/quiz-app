@extends('layout')

@section('content')

@php
    $resultClass = strtolower($result);
    $resultMessage = $result === 'Winner'
        ? 'Excellent run. You stayed sharp from start to finish.'
        : ($result === 'Better'
            ? 'Good effort. A stronger finish will push you to the top tier.'
            : 'Solid attempt. Review mistakes and run another round.');
@endphp

<div class="panel-head">
    <p class="eyebrow">Round Complete</p>
    <h1 class="heading-title">Your <span class="title-accent">Results</span></h1>
    <p class="heading-sub">Review your score and check the correct answers.</p>
</div>

<div class="result-banner {{ $resultClass }}">
    <p>Performance Snapshot</p>
    <h2>{{ $resultMessage }}</h2>
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
            <p class="question-label">
                <span>Question {{ $loop->iteration }}</span>
                <span class="answer-state {{ $isCorrect ? 'good' : 'bad' }}">{{ $isCorrect ? 'Correct' : 'Missed' }}</span>
            </p>
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
