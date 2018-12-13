<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
if (!isset($_SESSION["userid"])){
    $_SESSION["url"] = $_SERVER["PHP_SELF"];
    header("Location: Login.php");
    exit();
}
unset($_SESSION['currentAlbum']);
unset($_SESSION['currentPic']);
unset($_SESSION['fcurrentAlbum']);
unset($_SESSION['fcurrentName']);
unset($_SESSION['fcurrentPic']);
include 'Common/ConnectDB.php';
include 'Common/Functions.php';
include 'Model/Album.php';

extract($_POST);

$txtTitle = "";
$txtDes = "";

$titleErr = "";
$err = "";

if (isset($btnSubmit)){
    $titleErr = ValidateBlank($title, "Title");
    if ($titleErr == ""){
        $album = new Album("", htmlspecialchars($title), $accessibility, $description, $_SESSION['userid'], "");
        $result = $album->save();
        if ($result == ""){
            header("Location: MyAlbums.php");
            exit();
        }
        else {
            $err = "The system is not available, try again later.";
        }
    }  
}

include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center">Create New Album</h1>
    </div>
    <p>Welcome <b><?php echo $_SESSION["username"] ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p class="text-danger"><?php echo $err; ?></p>
    <form action="AddAlbum.php" method="POST">
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="title">Title:</label>
          <div class="col-sm-4">
              <input type="text" class="form-control" id="title" name=title value="<?php echo $txtTitle ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $titleErr ?></span>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="accessibility">Accessibility:</label>
          <div class="col-sm-4"> 
              <select name="accessibility" id="accessibility" class="form-control">
                  <?php 
                    $link = connect();
                    if ($link) {
                        $query = "SELECT * FROM ACCESSIBILITY";
                        $result = query($link, $query);
                        while ($r = mysqli_fetch_assoc($result)){
                            echo "<option value=$r[accessibility_code]>$r[description]</option>";
                        }
                    }
                    close($link);
                  ?>
              </select>
          </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-2" for="description">Description:</label>
            <div class="col-sm-4">
                <textarea class="form-control" name="description" id="description" rows="10"><?php echo $txtDes ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnSubmit>Submit</button></div>
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnReset>Clear</button></div>
        </div>
        
    </form>
</div>
<script>activeLink(3);</script>

<?php include 'Common/Footer.php'; ?>