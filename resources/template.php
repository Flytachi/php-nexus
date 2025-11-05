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

    <div class="wrap">
        <header class="top">
            <div class="logo">
                <img src="/favicon.svg" width="35" alt="Nexus Logo">
            </div>
            <div class="title">
                <h1>Nexus</h1>
                <p class="muted">lightweight event broker</p>
            </div>

            <div class="header-meta">
                <div class="chip">v<?= resourceData('version') ?></div>
                <div class="tag"><?= resourceData('name') ?></div>
            </div>

            <button id="btnLogout" class="btn ghost" style="width: 50px" title="Logout">&#8594;</button>
        </header>

        <?php resourceContent(); ?>

    </div>

    <div id="notificationContainer"></div>

</body>

<script src="/static/extra/js/jquery-3.6.0.min.js"></script>
<script src="/static/js/service.js"></script>
<script src="/static/js/auth.js"></script>
<script src="/static/js/app.js"></script>

</html>

