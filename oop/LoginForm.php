<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>"Notes Login Page"</title>
        <link rel="stylesheet" href="LoginForm.css">
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

        if (Input::isExist()) { 
            
            $validate = new Validate();     
            $validation = $validate->isUserLoggedIn();
            
            if ($validation) {
                $username = Input::get('username');
                $password = md5(Input::get('password'));
                $query = "SELECT * FROM `users` WHERE  Username = '$username' AND Password = '$password'";
                $result = DBConnection::getInstance()->prepare($query);
                $result->execute();
                $_SESSION["numberAttemptsToDeleteAccount"] = 0;
                $_SESSION["userID"] = $result->fetchColumn(0);
                header("location:home.php");
            } 
            
            $errorForm = $validate->getFormErrorMessage();
            $userValue = $validate->getUsernameValue();
            $passValue = $validate->getPasswordValue();
            $userError = $validate->getUsernameErrorMessage();
            $passError = $validate->getPasswordErrorMessage();
            
           // echo $passValue;
          }
          
            
            ?>
                            
        <h2 class="h2"> Login Form </h2>
            
            <div>
                <form class= "registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

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

                <div>
                    <input class="button" type="submit" value="Log In"></input>
                </div>   

                <div>
                    <label class="labelSignUp">Don't have an account? <a href="RegistrationForm.php" target="_self"> Sign Up </a> right now</label>
                </div>  

            </form>
        </div>

    </body>   
</html>