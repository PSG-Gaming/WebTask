<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>"Notes Login Page"</title>
        <link rel="stylesheet" href="RegistrationForm.css">
    </head>
    <body>

        <?php
        require_once 'Input.php';
        require_once 'ValidateInput.php';

        session_start();
        if (isset($_SESSION["userID"])) {
            header("location:home.php");
        }

        $errorForm = "";
        $userValue = "";
        $passValue = "";
        $userError = "";
        $passError = "";
        $passConfirmValue = "";
        $passConfirmError = "";

        if (Input::isExist()) {
            $validate = new Validate();
            $validation = $validate->isUserSignedup();
            if ($validation) {
                $username = Input::get('username');
                $password = md5(Input::get('password'));
                $query = "INSERT INTO `users` (`Username`, `Password`) VALUES ('$username', '$password')";
                $result = DBConnection::getInstance()->prepare($query);
                $result->execute();

                header("location:LoginForm.php");
            }
            $errorForm = $validate->getFormErrorMessage();
            $userValue = $validate->getUsernameValue();
            $passValue = $validate->getPasswordValue();
            $userError = $validate->getUsernameErrorMessage();
            $passError = $validate->getPasswordErrorMessage();
            $passConfirmValue = $validate->getConfirmPasswordValue();
            $passConfirmError = $validate->getPasswordConfirmErrorMessage();
        }
        ?>

                        <h2 class="h2"> Create Account Form </h2>

        <div>
            <form class= "registrationForm" action="" method="post">

                <div class="field">
                    <p>
                        <label class="error"><?php echo $errorForm ?></label>
                    </p>
                </div> 

                <div class="field">
                    <label class="labelUsername" for="username">Username:</label>
                    <input type="tetx" name="username" id="username" value="<?php echo $userValue ?>" autocomplete="off">
                    <span class="error">* <?php echo $userError ?></span> 

                </div> 

                <div class="field">
                    <label class="labelpassword" for="password">Password:</label>
                    <input type="password" name="password" id="password" value="<?php echo $passValue ?>">
                    <span class="error">* <?php echo $passError ?></span> 
                </div> 

                <div class="field">
                    <label class="labelConfirmPassword" for="confirmPassword">Confirm password:</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" value="<?php echo $passConfirmValue ?>">
                    <span class="error">* <?php echo $passConfirmError ?></span> 
                </div> 

                <div>
                    <input class="button" type="submit" value="Log In"></input>
                </div>   

                <div>
                    <label class="labelSignUp">If you already have an account? <a href="LoginForm.php"  target="_self"> Log In </a> right now</label>
                </div>  

            </form>
        </div>

</body>   
</html>