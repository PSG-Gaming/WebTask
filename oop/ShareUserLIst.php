
<?php
require_once 'DBConnection.php';
require_once 'ValidateInput.php';
require_once 'Input.php';
require_once 'Notes.php';
session_start();
if (!isset($_SESSION["userID"])) {
    header("location:LoginForm.php");
}

$userID = $_SESSION["userID"];
$noteID = Input::get('noteID');

if (!isNoteIDCorrec($userID)){
    header("location:home.php");
}


function isNoteIDCorrec($userID) {
    $query = 'SELECT * FROM `usernotes` WHERE `OwnerID` = ?';
    $result =  DBConnection::getInstance()->prepare($query);
    $result->execute(array($userID));
    $count = $result->rowCount();
    if ($count > 0) {
        return true;   
    } else {
        return false;
    }
}

$note = new Notes($userID);



$share = Input::get('shareToAll');
$back = Input::get('backToHome');

if ($share === 'Share') {
    var_dump('shareToAll');
    $note->share($noteID);
    header("location:home.php");
} else if($back === 'Back to my notes'){
    var_dump('backToHome');
    header("location:home.php");
}

$query = 'SELECT users.* FROM `users` WHERE users.ID NOT IN (SELECT permission.UserID FROM permission WHERE permission.NoteID = ? OR permission.UserID = ?) AND users.ID != ?';
//$query = 'SELECT users.* FROM `users` LEFT JOIN `permission` ON users.ID = permission.UserID  WHERE (permission.NoteID != ? AND users.ID != ?) OR permission.ID IS NULL GROUP BY users.ID ';
//$query = 'SELECT users.* FROM `users` LEFT JOIN `permission` ON users.ID = permission.UserID WHERE  permission.NoteID != ? AND users.ID != ?';
$result = DBConnection::getInstance()->prepare($query);
$result->execute(array($noteID, $userID, $userID));
$rowCount = $result->rowCount();


//$query = 'SELECT users.* FROM `users` WHERE users.ID NOT IN (SELECT permission.UserID FROM permission WHERE permission.NoteID = ? OR permission.UserID = ?)';
////$query = 'SELECT users.* FROM `users` LEFT JOIN `permission` ON users.ID = permission.UserID  WHERE (permission.NoteID != ? AND users.ID != ?) OR permission.ID IS NULL GROUP BY users.ID ';
////$query = 'SELECT users.* FROM `users` LEFT JOIN `permission` ON users.ID = permission.UserID WHERE  permission.NoteID != ? AND users.ID != ?';
//$result = DBConnection::getInstance()->prepare($query);
//$result->execute(array($noteID, $userID));
//$rowCount = $result->rowCount();
    

if ($rowCount == 0){
    $error = "current note is already shared to all users";
} else {
    $error = "";
}
?>




<h2> Choose users </h2>
<label><?php echo $error; ?></label>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <?php $count = 0; 
    foreach ($result as $row) :
        
    echo "№" . $count; ?>
            <label><input type="checkbox" name="usersID[]" value="<?php echo $row["ID"]; ?>"/> User: ( <?php echo $row["Username"]; ?> )</label><br />
            <input type="hidden" name="noteID" value="<?php echo $noteID; ?>"/>       
    <?php endforeach; ?>
            <?php if ($rowCount == 0): ?>
                <input type="submit" value="Back to my notes" name="backToHome" />
            <?php else: ?>
                <input type="submit" value="Share" name="shareToAll" />
            <?php endif; ?>
                
</form>