<?php

require_once './DBConnection.php';
require_once 'Input.php';
define("MAIN_URL_ICONS", 'http://localhost/iconsss/', false);
                          
class Notes {
    
    private $db = null;
    private $userID = '';
    private $username = '';
//    private $title = '';
//    private $description = '';
    private $datacreated = '';
    private $datamodified = '';
    private $errorMesage = '';
  

        

    public function __construct($userID) {
        $this->db = DBConnection::getInstance();
        $this->userID = $userID;
        $this->username = self::getUsernameByID($userID);
    }
    
    
    public function getUsernameByID($UserID) {
        $query = 'SELECT `Username` FROM `users` WHERE `ID` = ?';
        $result = $this->db->prepare($query);
        $result->execute(array($UserID));
        $username = $result->fetchColumn(0);
        return $username;
    }

    public function add() {
        
        $this->datacreated = $this->datamodified = date("Y-m-d H:i:s");
        $this->username = "Owner: " . $this->username;

        $query = 'INSERT INTO `usernotes` (`OwnerID`, `Title`, `Description`, `Created`, `Modified`, `UsersWithAccess`) VALUES (?, ?, ?, ?, ?, ?)';
        $result = $this->db->prepare($query);
        $result->execute(array($this->userID, Input::get('title'), Input::get('description'), $this->datacreated, $this->datamodified, $this->username));
         header("location:home.php");
    }
    
    public function update() {
        if ($this->isOwner()) {
            $this->datamodified = date("Y-m-d H:i:s");
            $query = 'UPDATE `usernotes` SET `Title` = ?, `Description` = ?, `Modified` = ? WHERE `usernotes`.`ID` = ?';
            $result = $this->db->prepare($query);
            $result->execute(array(Input::get('title'), Input::get('description'), $this->datamodified, Input::get('id')));
        } else {
            $this->errorMesage = 'not permission edit';
        }
        header("location:home.php");
    }

    public function remove() {
        if ($this->isOwner()) {
            $query = 'DELETE FROM `usernotes` WHERE `ID` = ?';
            $result = $this->db->prepare($query);
            $result->execute(array(Input::get('id')));
            
            $query = 'DELETE FROM `permission` WHERE `NoteID` = ? ';
            $result = $this->db->prepare($query);
            $result->execute(array(Input::get('id')));
            
            $query = 'DELETE FROM `files` WHERE `NoteID` = ? ';
            $result = $this->db->prepare($query);
            $result->execute(array(Input::get('id')));
        } else {
            $this->errorMesage = 'not permission delete';
        }
        header("location:home.php");
    }

    public function share($noteID) {
        
        $arrayUsersID = Input::get('usersID');
        $currentNoteID = $noteID;
 
        
        
        foreach ($arrayUsersID as $usersID) {   
            $query = 'INSERT INTO `permission` (`UserID`, `NoteID`) VALUES (?, ?)';
            $result = $this->db->prepare($query);
            $result->execute(array($usersID,$currentNoteID));
            
        }
       header("location:home.php");
    }

    private function isOwner() {

        $query = 'SELECT * FROM `usernotes` WHERE `ID` = ?';
        $result = $this->db->prepare($query);
        $result->execute(array(Input::get('id')));

        $userIDUsernotesTable = $result->fetchColumn(1);

        if ($userIDUsernotesTable === $this->userID) {
            return true;
        }
        return false;
    }
    
    public function getCurrentNoteFiles($noteID) {
        $query = 'SELECT * FROM `files` WHERE NoteID = ?'; 
        $statement = DBConnection::getInstance()->prepare($query);
        $statement->execute(array($noteID));
        $allFiles = $statement->fetchAll();
        return $allFiles;
    }

    public function getAllNotes() {

        $arrayAllOwnedNotes = self::getOwnedNotes();
        $arrayAllSharedNotes = self::getSharedNotes();
        if (isset($arrayAllSharedNotes)) {
            return array_merge($arrayAllOwnedNotes, $arrayAllSharedNotes);
        } else {
            return $arrayAllOwnedNotes;
        }
    }

    private function getOwnedNotes() {
        $query = 'SELECT usernotes.*, (SELECT GROUP_CONCAT(`users`.Username SEPARATOR ", ") FROM `users` LEFT JOIN permission ON permission.UserID = users.ID WHERE permission.NoteID = `usernotes`.ID GROUP BY permission.NoteID ) as UsersWithAccess FROM `usernotes` WHERE `OwnerID` = ?';
        $statement = DBConnection::getInstance()->prepare($query);
        $statement->execute(array($this->userID));
        
//        echo "<pre>" . print_r($arrayAllOwnedNotes, true) . "</pre>";
//        echo '<br>';echo '<br>'; 
//        echo '-----------------------------------------------------------------------------------------------------------------------';
//        echo '<br>';echo '<br>';
//        echo "<pre>" .  print_r($arrayAllSheredNotes, true) . "</pre>";
//         echo '<br>';echo '<br>'; 
//        echo '-----------------------------------------------------------------------------------------------------------------------';
//        echo '<br>';echo '<br>';
        
     
       // echo "<pre>" .  print_r($arrayAllNotes, true) . "</pre>";
        //die();
        return $statement->fetchAll();
        
    }

    private function getSharedNotes() {

        $query = 'SELECT NoteID FROM `permission` WHERE `UserID` = ?';
        $statement = DBConnection::getInstance()->prepare($query);
        $statement->execute(array($this->userID));

        $array = $statement->fetchAll();

        $count = count($array);
        $arrayValues = NULL;
        for ($i = 0; $i < $count; $i++) {
            $arrayValues[$i] = $array[$i][0];
        }

            if (empty($arrayValues)) {
                return $arrayValues;
            } else {
        $query1 = 'SELECT * FROM `usernotes` WHERE FIND_IN_SET(`ID`, :ids)';
        $result = DBConnection::getInstance()->prepare($query1);
        $result->execute(array(':ids' => implode(',', $arrayValues)));

            return $result->fetchAll();
            
            }
    }

    public function getErrorMesage() {
        return $this->errorMesage;
    }

      public function getUsername() {
        return $this->username;
    }
    
}