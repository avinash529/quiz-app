<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="app-body">
    <div class="page-bg" aria-hidden="true">
        <span class="glow glow-one"></span>
        <span class="glow glow-two"></span>
    </div>

    <main class="app-shell">
        <section class="main-card">
            @auth
                <div class="card-toolbar">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="ghost-btn" type="submit" data-loading-text="Signing out...">Logout</button>
                    </form>
                </div>
            @endauth

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
