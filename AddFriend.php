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
$txtFid = "";
$fidErr = "";
$err = "";
$success = false;
extract($_POST);
if (isset($btnSubmit)){
    $fidErr = ValidateFid($fid, $_SESSION['userid']);
    if ($fidErr == ""){
        $success = true;
    }
    if ($success){
        $link = connect();
        if ($link){
            $fid = mysqli_real_escape_string($link, $fid);
            $query = "SELECT name FROM user WHERE userid = '$fid'";
            $result = query($link, $query);
            $fn = mysqli_fetch_assoc($result);
            $fName = $fn['name'];
            $query = "SELECT status FROM friendship WHERE friend_requesterid = '$fid' && friend_requesteeid = '$_SESSION[userid]'";
            $result = query($link, $query);
            $row_nums = mysqli_num_rows($result);
            $status = mysqli_fetch_assoc($result);
            if ($row_nums > 0){
                if ($status['status'] == "request"){
                    $query = "UPDATE friendship SET status = 'accepted' WHERE friend_requesterid = '$fid' && friend_requesteeid = '$_SESSION[userid]'";
                    query($link, $query);
                    $err = "You and $fName (ID: $fid) now are friends. From now, you are able to view $fName shared albums.";
                } else {
                    $err = "You and $fName (ID: $fid) have already been friends.";
                }
            }
            else {
                $query = "SELECT status FROM friendship WHERE friend_requesteeid = '$fid' && friend_requesterid = '$_SESSION[userid]'";
                $result = query($link, $query);
                $row_nums = mysqli_num_rows($result);
                if ($row_nums > 0) {
                    $err = "You already sent request to $fName.";
                }
                else {
                    $query = "INSERT INTO friendship VALUES('$_SESSION[userid]', '$fid', 'request')";
                    query($link, $query);
                    $err = "Your request has sent to $fName (ID: $fid). Once $fName accepts your request, you and $fName will be friends and be able to view each other's shared albums.";
                }
                
            }
        }
        else {
            $err = "The system is not available, try again later.";
        }
        close($link);
    }
}

include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center col-sm-6">Add Friend</h1>
    </div>
    <p>Welcome <b><?php echo $_SESSION["username"] ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p>Enter the ID of the user you want to be friend with</p>
    <p class="text-danger"><?php echo $err; ?></p>
    <form method="POST" action="AddFriend.php">
        <div class="form-group row">
            <label class="col-form-label col-sm-1" for="fid">ID:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="fid" name=fid value="<?php echo $txtFid ?>">
            </div>
            <button type="submit" name="btnSubmit" class="btn btn-primary">Send Friend Request</button>
            <span class="col-sm-6 error"><?php echo $fidErr ?></span>
        </div>
        
    </form>
</div>
<script>activeLink(2);</script>

<?php include 'Common/Footer.php'; ?>