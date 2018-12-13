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
include 'Common/ConstantsAndSettings.php';
include 'Common/Functions.php';
include 'Model/Album.php';

$err = "";
extract($_POST);

if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $album = Album::findById($_GET["id"]);
    if ($album != "" && $album->getOwner_id() == $_SESSION['userid']){
        $link = connect();
        $id = mysqli_real_escape_string($link, $_GET['id']);
        $query = "SELECT * FROM picture WHERE album_id = $id";
        $result = query($link, $query);
        while ($p = mysqli_fetch_assoc($result)){
            unlink(ORIGINAL_PICTURES_DIR."/".$p['filename']);
            unlink(ALBUM_PICTURES_DIR."/".$p['filename']);
            unlink(ALBUM_THUMBNAILS_DIR."/".$p['filename']);
        }
        close($link);
        $album->delete();
    }
    else {
        $err = "Failed to delete";
    }
}

if (isset($btnSubmit)){
    if (!empty($albums) && !empty($accessibilities)){
        $i = 0;
        foreach ($albums as $album){
            $a = Album::findById($album);
            if ($a != "" && $a->getOwner_id() == $_SESSION['userid']){
                $result = $a->update($accessibilities[$i]);
                if ($result != ""){
                    $err = $result;
                    break;
                }
            }
            else {
                $err = "Failed to update";
                break;
            }
            $i++;
        }
    }
    else {
        $err = "Failed to update";
    }
}

include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center">My Albums</h1>
    </div>
    <p>Welcome <b><?php echo $_SESSION["username"] ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p class="text-danger"><?php echo $err; ?></p>
    
    
    <a class="pull-right" href="AddAlbum.php">Create a New Album</a>
    <form method="POST" action="MyAlbums.php">
        <table class="table">
            <thead>
                <th width="30%">Title</th>
                <th>Date Updated</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                <th></th>
            </thead>
            <tbody>
                <?php 
                    $link = connect();
                    if ($link){
                        $query = "SELECT * FROM ACCESSIBILITY";
                        $result = query($link, $query);
                        while ($r = mysqli_fetch_assoc($result)){
                            $accessibility[] = $r;
                        }
                        $query = "SELECT * FROM ALBUM WHERE owner_id = '$_SESSION[userid]'";
                        $result = query($link, $query);
                        while ($r = mysqli_fetch_assoc($result)){
                            $album = new Album($r['album_id'], $r['title'], $r['accessibility_code'], $r['description'], $r['owner_id'], $r['date_updated']);
                            echo $album->toHTML($accessibility);
                        }
                        if (mysqli_num_rows($result) == 0){
                            echo "<tr><td colspan='5' class='text-center text-danger'><b>No Album Found!</b></td></tr>";
                        }
                    }
                    else {
                        echo "<tr><td colspan='5' class='text-center text-danger'><b>No Album Found!</b></td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <button type="submit" name="btnSubmit" class="btn btn-primary pull-right">Save Changes</button>
    </form>
</div>
<script>activeLink(3);</script>

<?php include 'Common/Footer.php'; ?>