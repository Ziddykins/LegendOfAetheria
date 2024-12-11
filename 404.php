<?php include 'html/opener.html'; ?>
<head>
    <?php include 'html/headers.html'; ?>
    <link rel="stylesheet" href="css/spooky.css">
    <script src="js/tweenmax.min.js"></script>
    <script src="js/timelinemax.min.js"></script>
    <style>
        .return-btn {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 20px;
        }
        .return-btn:hover {
            background-color: #5a6268;
            color: white;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .logo-container {
            margin-bottom: 2rem;
        }
        .error-message {
            color: #6c757d;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glitch__container">
            <div class="error-container">
                <div class="logo-container">
                    <img src="./img/logos/logo-banner-no-bg.webp" alt="Logo" style="max-width: 400px; width: 100%;">
                </div>
                <div class="glitch__text">
                    <h1 class="glitch__text__title glitch-effect">
                        404
                        <span class="glitch-effect-text-clr1">404</span>
                        <span class="glitch-effect-text-clr2">404</span>
                    </h1>
                    <p class="error-message">The page you are looking for has vanished into the void.</p>
                    <a href="/" class="return-btn">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'html/footers.html'; ?>
    <script src="js/404.js"></script>
</body>
</html>
