<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nexus — Web</title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/static/style.css" type="text/css">
</head>
<script src="/static/extra/js/jquery-3.6.0.min.js"></script>
<script src="/static/main.js"></script>
<script src="/static/service.js"></script>
<body>
    <div class="overlay" id="overlay"></div>

    <?php resourceContent(); ?>

    <div id="notification-container"></div>
</body>
</html>

