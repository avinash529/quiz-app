<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
    <div class="page-bg" aria-hidden="true">
        <span class="liquid-blob blob-one"></span>
        <span class="liquid-blob blob-two"></span>
        <span class="liquid-blob blob-three"></span>
        <span class="liquid-ring ring-one"></span>
        <span class="liquid-ring ring-two"></span>
        <span class="liquid-grid"></span>
    </div>

    <main class="app-shell">
        <section class="main-card">
            <header class="app-topbar">
                <div class="brand-wrap">
                    <span class="brand-orb" aria-hidden="true"></span>
                    <div>
                        <p class="brand-title">Quiz App</p>
                        <p class="brand-sub">@auth Live Challenge Mode @else Account Access @endauth</p>
                    </div>
                </div>
                <div class="topbar-actions">
                    @auth
                        <span class="status-pill">Signed In</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="ghost-btn" type="submit" data-loading-text="Signing out...">Logout</button>
                        </form>
                    @else
                        <span class="status-pill guest">Guest</span>
                    @endauth
                </div>
            </header>

            @if(session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="flash success">{{ session('success') }}</div>
            @endif

            @yield('content')
        </section>
    </main>

    <div class="page-loader" id="pageLoader" aria-hidden="true" aria-live="polite">
        <div class="loader-box">
            <span class="loader-spinner" aria-hidden="true"></span>
            <span>Processing request...</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pageLoader = document.getElementById('pageLoader');
            const forms = document.querySelectorAll('form');

            forms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.submitting === '1') {
                        event.preventDefault();
                        return;
                    }

                    form.dataset.submitting = '1';

                    const submitter = event.submitter || form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');
                    const submitButtons = form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]');

                    if (submitter && submitter.name) {
                        const hasSubmitterCopy = form.querySelector('input[data-submitter-copy="1"]');

                        if (!hasSubmitterCopy) {
                            const submitterCopy = document.createElement('input');
                            submitterCopy.type = 'hidden';
                            submitterCopy.name = submitter.name;
                            submitterCopy.value = submitter.value;
                            submitterCopy.setAttribute('data-submitter-copy', '1');
                            form.appendChild(submitterCopy);
                        }
                    }

                    submitButtons.forEach((button) => {
                        button.disabled = true;
                        button.classList.add('is-loading');
                    });

                    if (submitter) {
                        const loadingText = submitter.dataset.loadingText;
                        if (loadingText) {
                            if (submitter.tagName === 'INPUT') {
                                submitter.value = loadingText;
                            } else {
                                submitter.textContent = loadingText;
                            }
                        }
                    }

                    if (pageLoader) {
                        pageLoader.classList.add('active');
                        pageLoader.setAttribute('aria-hidden', 'false');
                    }
                });
            });
        });
    </script>
</body>
</html>
