<?php
        session_start();
        $sup = 1;

        if (isset($_POST['login-submit'])) {
                
        }
?>

<?php include('html/opener.html'); ?>

        <head>
                <?php include('html/headers.html'); ?><!-- :D -->
                <style>
                    body {
                        min-height: 100vh;
                        min-height: -webkit-fill-available;
                    }

                    html {
                        height: -webkit-fill-available;
                    }

                    main {
                        height: 100vh;
                        height: -webkit-fill-available;
                        max-height: 100vh;
                        overflow-x: auto;
                        overflow-y: hidden;
                    }
                </style>
        </head>
        
        <body class=""> 
                <?php include('html/nav-game.html'); ?><!-- :D -->
        </body>
</html>