@extends('layout')

@section('content')

<div class="panel-head compact-head">
    <p class="eyebrow">Live Round</p>
    <h1 class="heading-title">Choose the <span class="title-accent">correct answer</span></h1>
</div>

<div class="quiz-meta">
    <div class="meta-pill">
        <span>Question</span>
        <strong>{{ $index + 1 }} / {{ $total }}</strong>
    </div>
    <div class="meta-pill timer-pill" id="timerWrap">
        <span>Time Left</span>
        <strong id="timer" aria-live="polite">0:30</strong>
    </div>
</div>

<div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $index + 1 }}">
    <span style="width: {{ (($index + 1) / $total) * 100 }}%"></span>
</div>

@if($errors->any())
    <div class="flash error">
        <ul class="error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="question-card">
    <p class="question-kicker">Question prompt</p>
    <h2 class="question-title">{{ $question['question'] }}</h2>

    <form method="POST" action="{{ route('quiz.answer') }}" id="quizForm" class="answers-grid">
        @csrf

        @foreach($options as $opt)
            @php($optionKey = chr(64 + $loop->iteration))
            <button type="submit" name="answer" value="{{ $opt }}" class="answer-btn" data-loading-text="Submitting...">
                <span class="answer-key">{{ $optionKey }}</span>
                <span class="answer-copy">{{ $opt }}</span>
            </button>
        @endforeach
    </form>
</div>

<p class="hint-text">If the timer reaches zero, this question is auto-submitted.</p>

<script>
let time = 30;
const timer = document.getElementById('timer');
const timerWrap = document.getElementById('timerWrap');
const quizForm = document.getElementById('quizForm');

const intervalId = setInterval(() => {
    time--;
    timer.textContent = '0:' + (time < 10 ? '0' + time : time);

    if (time <= 10) {
        timerWrap.classList.add('urgent');
    }

    if (time <= 0) {
        clearInterval(intervalId);
        if (typeof quizForm.requestSubmit === 'function') {
            quizForm.requestSubmit();
        } else {
            quizForm.submit();
        }
    }
}, 1000);

quizForm.addEventListener('submit', () => {
    clearInterval(intervalId);
});
</script>

@endsection
