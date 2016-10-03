<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>"Notes Delete Account Page"</title>
        <link rel="stylesheet" href="RegistrationForm.css">
    </head>
    <body>

        <?php
        require_once 'Input.php';
        require_once 'ValidateINput.php';

        session_start();
        if (!isset($_SESSION["userID"])) {
            header("location:LoginForm.php");
        }
        $numberOfAllowedAttempts = 3;
        $passError = '';
        $errorForm = '';
        $passValue = '';


        if (Input::isExist()) {
           
            $deleteAccount = Input::get('deleteAccount');
            $reconsidered = Input::get('reconsidered');
            $password = Input::get('password');
            $userID = $_SESSION["userID"];


            if ($reconsidered === 'Reconsidered') {
                header("location:home.php");
            } else if ($deleteAccount === 'Delete Account') {

                if (!empty($password)) {

                    $query1 = 'SELECT * FROM `users` WHERE `ID` = ? ';
                    $result1 = DBConnection::getInstance()->prepare($query1);
                    $result1->execute(array($userID));
                    $userPassword = $result1->fetchColumn(2);

                    if ($userPassword == $password) {
                        print_r("same passs");
                        // usernoted
                        $query = 'SELECT `ID` FROM `usernotes` WHERE `OwnerID` = ?';
                        $result = DBConnection::getInstance()->prepare($query);
                        $result->execute(array($userID));
                        $array = $result->fetchAll();

                        $count = count($array);
                        $arrayValues = NULL;
                        for ($i = 0; $i < $count; $i++) {
                            $arrayNoteID[$i] = $array[$i][0];
                        }

                        if (!empty($arrayNoteID)) {
                            // permissions
                            $query1 = 'DELETE FROM `permission` WHERE FIND_IN_SET(`NoteID`, :nid)';
                            $result = DBConnection::getInstance()->prepare($query1);
                            $result->execute(array(':nid' => implode(',', $arrayNoteID)));
                        }

                        ;
                        // usernotes
                        $query = 'DELETE FROM `usernotes` WHERE `OwnerID` = ?';
                        $result = DBConnection::getInstance()->prepare($query);
                        $result->execute(array($userID));

                        // users
                        $query = 'DELETE FROM `users` WHERE `ID` = ?';
                        $result = DBConnection::getInstance()->prepare($query);
                        $result->execute(array($userID));

                        session_destroy();
                        header("location:LoginForm.php");
                    } else {
                        $_SESSION["numberAttemptsToDeleteAccount"] ++;
                        //header("location:DeleteAccountForm.php");                   
                    }
                } else {
                    $passError = 'Password is required field';
                }
            }

            if ($_SESSION["numberAttemptsToDeleteAccount"] == ($numberOfAllowedAttempts - 1)) {
                $errorForm = 'You have one more attempt before Log-out';
            } else if ($_SESSION["numberAttemptsToDeleteAccount"] == ($numberOfAllowedAttempts)) {
                session_destroy();
                header("location:LoginForm.php");
            }
        }
        ?>
        <h2 class="h2"> Delete Account Form </h2>

        <div>
            <form class= "registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="field">
                    <p>
                        <label class="error"><?php echo $errorForm ?></label>
                    </p>
                </div> 

                <div class="field">
                    <label class="labelpassword" for="password">Password:</label>
                    <input type="password" name="password" id="password" value="<?php echo $passValue ?>">
                    <span class="error">* <?php echo $passError ?></span> 
                </div> 

                <div>
                    <input class="button" type="submit" name="deleteAccount" value="Delete Account"></input>
                    <input class="button" type="submit" name="reconsidered" value="Reconsidered"></input>

                </div>   


            </form>
        </div>

        <!--         <div>
                            <input class="button" type="submit" value="Reconsidered"></input>
                        </div>  -->
        <!--          <div>
                    <form action="" method="post">
                        <div>
                            <input class="button" type="submit" value="Reconsidered"></input>
                        </div>   
                    </form>
                </div>-->

    </body>   
</html>


