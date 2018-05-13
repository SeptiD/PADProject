<?php include('server.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login and Registration SportDataUPT</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-page">
    <div class="form">
        <form method="post" action="register.php" class="register-form">
            <?php include('errors.php'); ?>
            <input type="text" placeholder="user name" name="username" value="<?php echo $username; ?>">
            <input type="text" placeholder="password" name="password_1">
            <input type="text" placeholder="email" name="email" value="<?php echo $email; ?>">
            <button>Create</button>
            <p class="message">Already Registered? <a href="#">Login</a>
            </p>
        </form>
        <form class="login-form">
            <input type="text" placeholder="user name" >
            <input type="text" placeholder="password">
            <button>Login</button>
            <p class="message">Not Registered? <a href="#">Register</a>
            </p>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
                $('.message a').click(function () {
                    $('form').animate({height:"toggle", opacity:"toggle"},"slow");

                });
    </script>
</body>
</html>