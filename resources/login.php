<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nexus - <?= resourceData('name') ?></title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/static/style.css" type="text/css">
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">

            <div class="auth-logo">
                <img src="/favicon.svg" width="60" alt="Nexus Logo">
            </div>
            <h1>Login to Nexus</h1>

            <form id="loginForm" class="auth-form">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>

        </div>
    </div>

    <div id="notification-container"></div>

</body>

<script src="/static/extra/js/jquery-3.6.0.min.js"></script>
<script src="/static/js/service.js"></script>
<script src="/static/js/auth.js"></script>
<script src="/static/js/app.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', handleLogin);
        }
    });
</script>

</html>

