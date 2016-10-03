<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>"Notes Home Page"</title>
        <link rel="stylesheet" href="homeStyles.css">
    </head>
    <body >

        <script>
            function confirmDeleteNote() {
                var isConfirmed = confirm("Do you really want to remove the note ?");
                if (!isConfirmed) {
                    event.preventDefault();
                }
                //document.notesForm.submit();
                //location.href = 
                // document.getElementById("form").submit();

                //location.href=”index.php?uid=1";
                //message = "You pressed OK!";
                //message = "You pressed Cancel!";

                //document.getElementById("p").innerHTML = message;

            }
            function confirmLogOut() {
                var isConfirmed = confirm("Do you really want to log out ?");
                if (!isConfirmed) {
                    event.preventDefault();
                }
                //document.notesForm.submit();
                //location.href = 
                // document.getElementById("form").submit();

                //location.href=”index.php?uid=1";
                //message = "You pressed OK!";
                //message = "You pressed Cancel!";

                //document.getElementById("p").innerHTML = message;

            }
            
        </script>



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



        $note = new Notes($userID);

        // $statement = $note->getOwnedNotes();

     function chooseFileIcon($files) {
         $result='';
    switch ($files['Type']) {
        case 'pdf':
            $result = MAIN_URL_ICONS . 'pdf-icon.png';
            break;
        case 'txt':
            $result = MAIN_URL_ICONS . 'txt-icon.png';
            break;
        case 'docx':
            $result = MAIN_URL_ICONS . 'docx-icon.png';
            break;
        case 'doc':
            $result = MAIN_URL_ICONS . 'docx-icon.png';
            break;
        case 'xlsx':
            $result = MAIN_URL_ICONS . 'xlsx-icon.png';
            break;
        case 'xls':
            $result = MAIN_URL_ICONS . 'xlsx-icon.png';
            break;
        default:
            $result = $files['URL'];
            break;
    }   
    return $result;
}

if (Input::isExist()) {
            //print_r("ima info ");

            $add = Input::get("add");
            $update = Input::get('update');
            $delete = Input::get('delete');
            $share = Input::get('share');
            $send = Input::get('send');
            $noteID = Input::get('send');
            $logOut = Input::get('LogOut');
            $deleteAccount = Input::get('DeleteAccount');
            
            if ($add === 'add') {
                header("location:AddNoteForm.php");
            } else
            if ($update === 'update') {
                $note->update();
            } else
            if ($delete === 'delete') {
                $note->remove();
            } else
            if ($share === 'share') {
                header("location:ShareUserList.php?noteID=" . Input::get('id') . "");
            } else if ($logOut === 'Log Out') {
                session_destroy();
                header("location:LoginForm.php");
            } else if ($deleteAccount === 'Delete Account') {
                header("location:DeleteAccountForm.php");
            }
        }

        $allNotes = $note->getAllNotes();
        
//        echo '<pre>' . print_r($allNotes, true) . '</pre>';
//        die();
        
        ?>
                        
        
        
        <div>
           <form  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">          
           <input type="submit" class="button" name="DeleteAccount" value="Delete Account"/>
           </form>
        </div>
        
          <div>
           <form  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
           <input type="submit" class="button" name="LogOut" value="Log Out" onclick="confirmLogOut()"/>
           </form>
        </div>
                    
        <h2> Your Notes! </h2>



         <div class="titles">
            <form class="addButtonForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="addButton"><input class="inputAddButton" type="submit" name="add" value="add"></div>
            </form>
         </div>
        
        
        <div class="titles">
            <div class="column1"><label class="label" name = "modified"><?php echo 'Title' ?></label></div>
            <div class="column2"><label class="label" name = "userWithAccsess"><?php echo 'Description' ?></label></div>
            <div class="column2"><label class="label" name = "files"><?php echo 'File' ?></label></div>
            <div class="column3"><label class="label" name="created"><?php echo 'Data Created' ?></label></div>
            <div class="column4"><label class="label" name = "modified"><?php echo 'Data Modified' ?></label></div>
            <div class="column5"><label class="label" name = "userWithAccsess"><?php echo 'Users With Access' ?></label></div>
        </div>               

        
        <?php foreach ($allNotes as $row) : ?>
            <div class="titles">
                <form id="form"  name="notesForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <?php if ($userID === $row['OwnerID']): ?>
                        <div class="column1"><input type="text" name="title" value="<?php echo $row['Title']; ?>" /></div>
                        <div class="column2"><textarea name="description" rows=5 cols=25><?php echo $row['Description']; ?></textarea></div>
                         <div class="column3">     
                            <div class="content">
                                 <?php foreach ($note->getCurrentNoteFiles($row['ID']) as $files) : ?>
                                 <a href="<?php echo $files['URL'];?>" download="<?php echo $files['Name']; ?>" title="<?php echo $files['Name']; ?>">
                                     <img style="width:50px;height:50px;border:0px;" alt="<?php echo $files['Name']; ?>" src="<?php echo chooseFileIcon($files); ?>">
                                    <span><pre><?php echo $files['Name']; ?></pre></span>
                                 </a>
                                 <?php endforeach; ?>
                             </div>
                             </div>
                        
                        <div class="column4" name="created"><?php echo $row['Created']; ?></div>
                        <div class="column5" name = "modified"><?php echo $row['Modified']; ?></div>
                        <div class="column6" name = "userWithAccsess"><?php echo $row['UsersWithAccess']; ?></div>
                        <div class="column7"><input type="hidden" name="id" value="<?php echo $row['ID']; ?>" /></div>
                        <div class="column8">
                        <input type="submit" name="update" value="update">
                        <input type="submit" name="delete" value="delete" onclick="confirmDeleteNote()">
                        <input type="submit" name="share" value="share">
                        </div>
                    <?php else: ?>
                        
                         <!--<h2> Shared Notes! </h2>-->
                        <div class="column1" type="text" name="title"><?php echo $row['Title']; ?></div>
                        <div class="column2" name="description"><?php echo $row['Description']; ?></div>
                        <div class="column3"> 
                             <div class="content">
                                <?php foreach ($note->getCurrentNoteFiles($row['ID']) as $files) : ?>
                                     <a href="<?php echo $files['URL'];?>" download="<?php echo $files['Name']; ?>" title="<?php echo $files['Name']; ?>">
                                     <img style="width:50px;height:50px;border:0px;" alt="<?php echo $files['Name']; ?>" src="<?php echo chooseFileIcon($files); ?>">
                                     <span><pre><?php echo $files['Name']; ?></pre></span>
                                 </a>                                 
                                     <?php endforeach; ?>
                             </div>
                             </div>
                        <div class="column4" name="created"><?php echo $row['Created']; ?></div>
                        <div class="column5" name = "modified"><?php echo $row['Modified']; ?></div>
                        <div class="column6" name = "userWithAccsess"><?php echo $row['UsersWithAccess']; ?></div>   
                    <?php endif; ?>

                </form>
            </div>
        <?php endforeach; ?>

        
    </body>
</html>















<!--                <form id="form"  name="notesForm" method="post" action="<?php // echo htmlspecialchars($_SERVER["PHP_SELF"]);  ?>">
                        <tr>
                            <td><input type="text" name="title" value="<?php //echo $row['Title'];  ?>" /></td>
                            <td><textarea name="description" rows=5 cols=40><?php //echo $row['Description'];  ?></textarea></td>
                            <td><label name="created"><?php //echo $row['Created']  ?></label></td>
                            <td><label name = "modified"><?php //echo $row['Modified'];  ?></label></td>
                            <td><label name = "userWithAccsess"><?php // echo $row['UsersWithAccess'];  ?></label></td>
                            <td><input type="hidden" name="id" value="<?php //echo $row['ID'];  ?>" /></td>
                            <td><input type="submit" name="update" value="update"></td>
                            <td><input type="submit" name="delete" value="delete" onclick="myFunction()"></td> 
                            <td><input type="submit" name="share" value="share"></td>  
                        </tr>
                    </form>-->