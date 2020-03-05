<?php
    include("includes/config.php");
    include("includes/clases/Account.php");
    include("includes/clases/Constants.php");
    
    $account = new Account($con);
    
    include("includes/handlers/register-handler.php");
    include("includes/handlers/login-handler.php");

    if(isset($_SESSION['userLoggedIn'])){
        header("Location: index.php");
    }

    function getInputValue($name){
        if(isset($_POST[$name])){
            echo $_POST[$name];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Slotify</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
    <?php 

    if(isset($_POST['registerButton'])){
        echo '<script>
             $(document).ready(function(){
                 $("#loginForm").hide();
                 $("#registerForm").show(); 
            })
            </script>';
    }else {
        echo '<script>
             $(document).ready(function(){
                 $("#loginForm").show();
                 $("#registerForm").hide(); 
            })
            </script>';
    }
    ?>

    <div id="background">
        <div id="loginContainer">
            <div id="inputContainer">
                <form id="loginForm" action="register.php" method="POST">
                    <h2>Login to your account</h2>
                    <p>
                        <?php echo $account->getError(Constants::$loginFailed); ?>
                        <label for="loginUsername">Username</label>
                        <input type="text" name="loginUsername" id="loginUsername" placeholder="e.g. bartSimpson" value="<?php getInputValue('loginUsername'); ?>" required>
                    </p>
                    <p>
                        <label for="loginPassword">Password</label>
                        <input type="password" name="loginPassword" id="loginPassword" placeholder="Your password" required>
                    </p>
                    <button type="submit" name="loginButton">LOG IN</button>
                    
                    <div class="hasAccountText">
                        <span id="hideLogin">Don't have an account yet? Signup here.</span>
                    </div>
                </form>

                <!-------------------------------------------------------------->

                <form id="registerForm" action="register.php" method="POST">
                    <h2>Create your free account</h2>
                    <p></p>
                        <?php echo $account->getError(Constants::$usernameCharacters); ?>
                        <?php echo $account->getError(Constants::$usernameTaken); ?>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="e.g. bartSimpson" value="<?php getInputValue('username'); ?>" required>
                    </p>
                    <p>
                        <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                        <label for="firstName">First name</label>
                        <input type="text" name="firstName" id="firstName" placeholder="e.g. bart" value="<?php getInputValue('firstName'); ?>" required>
                    </p>
                    <p>
                        <?php echo $account->getError(Constants::$lastNameCharacters); ?>
                        <label for="lastName">Last name</label>
                        <input type="text" name="lastName" id="lastName" placeholder="e.g. Simpson" value="<?php getInputValue('lastName'); ?>" required>
                    </p>
                    <p>
                        <?php echo $account->getError(Constants::$emailInvalid); ?>
                        <?php echo $account->getError(Constants::$emailDoNotMatch); ?>
                        <?php echo $account->getError(Constants::$emailTaken); ?>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email'); ?>" required>
                    </p>
                    <p>
                        <label for="email2">Confirm email</label>
                        <input type="email" name="email2" id="email2" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email2'); ?>" required>
                    </p>
                    <p>
                        <?php echo $account->getError(Constants::$passwordCharacters); ?>
                        <?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
                        <?php echo $account->getError(Constants::$passwordsDoNotMatch); ?>
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Your password" required>
                    </p>
                    <p>
                        <label for="password2">Confirm password</label>
                        <input type="password" name="password2" id="password2" placeholder="Your password" required>
                    </p>
                    <button type="submit" name="registerButton">SIGN UP</button>

                    <div class="hasAccountText">
                        <span id="hideRegister">Already have an account? Log in here.</span>
                    </div>
                </form>
            </div>

            <div id="loginText">
                <h1>Get great music, right now</h1>
                <h2>Listen to loads of songs for free</h2>
                <ul>
                    <li>Discover music you'll fall in love with</li>
                    <li>Create your own playlists</li>
                    <li>Follow artists to keep up to date</li>
                </ul>
            </div>
        </div>
    </div>
    <script src="assets/js/register.js"></script>
</body>
</html>