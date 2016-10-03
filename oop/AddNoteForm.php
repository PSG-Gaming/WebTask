<?php
require_once 'Input.php';
require_once 'DBConnection.php';
require_once 'Notes.php';

session_start();
if (!isset($_SESSION["userID"])) {
    header("location:LoginForm.php");
}
$userID = $_SESSION['userID'];
$note = new Notes($userID);
$errors = array();

// size 3MB = 3145728 byte
define("MAX_FILE_SIZE", 3145728, true);
define("FILE_PATH", 'G:\XAMPP\xampp\htdocs\upload\\', false);
define("MAIN_URL", 'http://localhost/upload/', false);

if (Input::isExist()) {

//      if ( !empty(Input::get('title')) || !empty(Input::get('description')) ) {
//        var_dump(empty(Input::get('title')));
//        echo"<br>";
//        var_dump(empty(Input::get('description')));
//        echo"<br>";
//        die();
        
    $datacreated = $datamodified = date("Y-m-d H:i:s");
    $username = "Owner: " . $note->getUsernameByID($userID);
    $query = 'INSERT INTO `usernotes` (`OwnerID`, `Title`, `Description`, `Created`, `Modified`, `UsersWithAccess`) VALUES (?, ?, ?, ?, ?, ?)';
    $result = DBConnection::getInstance()->prepare($query);
    $result->execute(array($userID, Input::get('title'), Input::get('description'), $datacreated, $datamodified, $username));

    $query = 'SELECT MAX(`ID`) FROM `usernotes`';
    $result = DBConnection::getInstance()->prepare($query);
    $result->execute();
    $noteID = $result->fetchColumn(0);
   // }

    if (!empty($_FILES['userfiles']['name'][0])) {
        $files = $_FILES['userfiles'];
        $allowedFileTypes = array('jpeg', 'jpg', 'png', 'docx', 'doc', 'xlsx', 'xls', 'pdf', 'txt');

        foreach ($files['name'] as $position => $fileName) {
            $fileTmpName = $files['tmp_name'][$position];
            $fileSize = $files['size'][$position];
            $fileError = $files['error'][$position];

            $fileExt = explode('.', $fileName);
            $fileExt = strtolower(end($fileExt));

            if (in_array($fileExt, $allowedFileTypes)) {

                if ($fileError === 0) {

                    if ($fileSize <= MAX_FILE_SIZE) {
                        $fileNameNew = uniqid('', true) . '.' . $fileExt;
                        $fileDestination = FILE_PATH . $fileNameNew;

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            // DB set   problem s  NoteID
                            $fileDestination = MAIN_URL . $fileNameNew;
                            $filePath = addslashes($fileDestination);
                            $query = "INSERT INTO `files` (`NoteID`, `Name`, `Size`, `URL`, `Type`) VALUES ('$noteID', '$fileNameNew', '$fileSize', '$filePath', '$fileExt')";
                            $result = DBConnection::getInstance()->prepare($query);
                            $result->execute();
                            header("location:home.php");
                        } else {
                            $errors[$position] = "[{$fileName}] faild to upload";
                        }
                    } else {
                        $errors[$position] = "[{$fileName}] is too large";
                    }
                } else {
                    $errors[$position] = "[{$fileName}] errored with code {$fileError} ";
                }
            } else {
                $types = "";
                foreach ($allowedFileTypes as $type){
                    $types += $type;   
                } 
                // error for array  to string not allowed
                $errors[$position] = "File : [{$fileName}] - file extension '{$fileExt}' is not allowed. You can upload ('{"  . $types . "}')";
                
                 
            }
        }
    } else {
        
      
        header("location:home.php");
    }

//  echo '<pre>', print_r($fileName), '</pre>';
    // $allowedFileSize = Input::get('MAX_FILE_SIZE');
    //echo '<pre>', print_r($files), '</pre>';
}
?>
<div>
    <p>Add Note</p>
</div>

<div>
    <form class= "registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="field">
            <p>
                <label class="error"><?php foreach ($errors as $error) {
    echo $error;
    } ?></label>
            </p>
        </div> 

        <div class="field">
            <label class="labelUsername" for="title">Title:</label>
            <input type="text" name="title" id="title" value="" autocomplete="off">
        </div> 

        <div class="field">
            <label class="labelpassword" for="tetxtArea">Description:</label>
            <div class="column2"><textarea name="description"  id="tetxtArea" rows=20 cols=60></textarea></div>
        </div> 

        <div class="field">
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
            <input name="userfiles[]" type="file"  id="userfile" multiple=""> 
        </div> 

        <div>
            <input class="button" type="submit" value="Add"></input>
        </div>   
    </form>
</div>

