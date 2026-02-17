@extends('layout')

@section('content')

<div class="panel-head">
    <p class="eyebrow">Quiz Arena</p>
    <h1 class="heading-title">Pick a category and start the <span class="title-accent">Liquid Challenge</span></h1>
    <p class="heading-sub">Modern timed trivia: 15 questions, 30 seconds each, instant scoring.</p>
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

<section class="quick-stats" aria-label="Quiz overview">
    <article class="quick-stat">
        <span>Categories</span>
        <strong>{{ count($allCategories) }}</strong>
    </article>
    <article class="quick-stat">
        <span>Questions</span>
        <strong>15 / round</strong>
    </article>
    <article class="quick-stat">
        <span>Timer</span>
        <strong>30 sec each</strong>
    </article>
</section>

@if(empty($allCategories))
    <div class="empty-state">
        <h2>Categories unavailable</h2>
        <p>We could not load quiz categories from the trivia provider right now.</p>
        <a href="{{ route('home') }}" class="btn-style">Retry</a>
    </div>
@else
    <div class="category-grid">
        @foreach($allCategories as $cat)
            <form method="POST" action="{{ route('quiz.start') }}" class="category-form">
                @csrf
                <input type="hidden" name="category" value="{{ $cat }}">
                <button class="category-btn" type="submit" data-loading-text="Loading quiz...">
                    <span class="category-title">{{ ucwords(str_replace('_', ' ', $cat)) }}</span>
                    <span class="category-meta">
                        <small>15 questions</small>
                        <small>30 sec each</small>
                    </span>
                </button>
            </form>
        @endforeach
    </div>
@endif

@endsection
