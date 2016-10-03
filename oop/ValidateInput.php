<?php
require_once 'DBConnection.php';
require_once 'Input.php';


class Validate {

    private $_db = null;
    private $result = "";
            
    private $usernameErrorMessage = '';
    private $passwordErrorMessage = '';
    private $passwordConfirmErrorMessage = '';
    private $formErrorMessage = '';
    private $usernameValue = '';
    private $passwordValue = '';
    private $confirmPasswordValue = '';
    

    public function __construct() {
        $this->_db = DBConnection::getInstance();
        $this->usernameValue = Input::get('username');
        $this->passwordValue = Input::get('password');
        $this->confirmPasswordValue = Input::get('confirmPassword');
       
    }

    
    
    public function isUserLoggedIn() {
        $isExist = $this->isUserExist();
        $checkName = $this->checkUsername();
        $checkPass = $this->checkPassword();
        if ($checkName && $checkPass && $isExist) {
            return true;
        }
        return false;
    }
    
    public function isUserSignedup() {
        $isExist = $this->isUserExist();
        $checkName = $this->checkUsername();
        $checkPass = $this->checkPassword();   
        $isConfirmed = $this->isPasswordConfirmed();
        
        if ($checkName && $checkPass && !$isExist && $isConfirmed) {
           return true;  
        }
        return false;
    }
    
    
    public function checkUsername() {
         $this->usernameErrorMessage = '';
        if (empty($this->usernameValue)) {
            $this->usernameErrorMessage = "Username is required field";
            $this->formErrorMessage = "";
            return false;
        } else {
            if (!preg_match("/^[a-zA-Z ]*$/", $this->usernameValue)) {
                $this->usernameValue = "";
                $this->usernameErrorMessage = "Only letters and white space allowed";
                $this->formErrorMessage = "";
                 return false;
            }
        }
        return true;
    }

    public function checkPassword() {
        $this->passwordErrorMessage = "";
        if (empty($this->passwordValue)) {
            $this->passwordErrorMessage = "Password is required field";
            $this->formErrorMessage = "";
            return false;
        } else {
            if (!preg_match("/^[a-zA-Z0-9]*$/", $this->passwordValue)) {
                $this->passwordValue = "";
                $this->passwordErrorMessage = "Only letters and numbers allowed";
                $this->formErrorMessage = "";
                return false;
            }
        } 
        return true;
    }

    public function isUserExist() {
        
        $this->result = $this->_db->prepare('SELECT `Username`, `Password` FROM `users`
        WHERE Username = ? AND Password = ?');
        $password = md5($this->passwordValue);
        $this->result->execute(array($this->usernameValue, $password));
        $count = $this->result->rowCount();
        
        if ($count > 0) {
            return true;
        } else {
            $this->formErrorMessage = "User doesn't exist ";
           // $this->passwordValue = "";

            return false;
        }
    }

    private function isPasswordConfirmed() {
        
        if (empty($this->passwordValue)) {
            $this->passwordConfirmErrorMessage = "Confirm Password is required field";
            return false;
        } else if ($this->passwordValue === $this->confirmPasswordValue) {
            return true;
        } 
        //$this->passwordValue = "";
        return false;
    }
        
    
    public function getUsernameErrorMessage() {
        return $this->usernameErrorMessage;
    }

     public function getPasswordErrorMessage() {
        return $this->passwordErrorMessage;
    }
    
    public function getFormErrorMessage() {
        return $this->formErrorMessage;
    }

    public function getUsernameValue() {
        return $this->usernameValue;
    }

    public function getPasswordValue() {
        return $this->passwordValue;
    }

    public function getConfirmPasswordValue() {
        return $this->confirmPasswordValue;
    }
    
    public function getPasswordConfirmErrorMessage() {
        return $this->passwordConfirmErrorMessage;
    }

        
}
