@extends('layout')

@section('content')

<div class="panel-head">
    <p class="eyebrow">Quiz Arena</p>
    <h1 class="heading-title">Pick a category and start the challenge</h1>
    <p class="heading-sub">You will get 15 questions with 30 seconds for each answer.</p>
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
                    <span>{{ ucwords(str_replace('_', ' ', $cat)) }}</span>
                    <small>Start</small>
                </button>
            </form>
        @endforeach
    </div>
@endif

@endsection
