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

$err = "";
extract($_POST);

if (isset($unfriendSubmit)){
    if (!empty($unfriends)){
        $link = connect();
        foreach ($unfriends as $uf){
            $uf = mysqli_real_escape_string($link, $uf);
            $query = "DELETE FROM friendship WHERE ((friend_requesterid = '$_SESSION[userid]' AND friend_requesteeid = '$uf') OR (friend_requesteeid = '$_SESSION[userid]' AND friend_requesterid = '$uf')) AND status = 'accepted'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to unfriend";
    }
}

if (isset($acceptSubmit)){
    if (!empty($friendRequests)){
        $link = connect();
        foreach ($friendRequests as $fr){
            $fr = mysqli_real_escape_string($link, $fr);
            $query = "UPDATE friendship SET status = 'accepted' WHERE (friend_requesteeid = '$_SESSION[userid]' AND friend_requesterid = '$fr') AND status = 'request'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to accept the request";
    }
}

if (isset($denySubmit)){
    if (!empty($friendRequests)){
        $link = connect();
        foreach ($friendRequests as $fr){
            $fr = mysqli_real_escape_string($link, $fr);
            $query = "DELETE FROM friendship WHERE (friend_requesteeid = '$_SESSION[userid]' AND friend_requesterid = '$fr') AND status = 'request'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to deny the request";
    }
}

include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center">My Friends</h1>
    </div>
    <p>Welcome <b><?php echo $_SESSION["username"] ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p class="text-danger"><?php echo $err; ?></p>
    
    
    <a class="pull-right" href="AddFriend.php">Add Friends</a>
    <form method="POST" action="MyFriends.php">
        <h3>Friends:</h3>
        <table class="table">
            <thead>
                <th>Name</th>
                <th>Shared Albums</th>
                <th>Unfriend</th>
            </thead>
            <tbody>
                <?php 
                    $link = connect();
                    if ($link){
                        $query = "SELECT userid, name, COUNT(album_id) as sharedalbums FROM friendship LEFT JOIN user a ON (userid = friend_requesteeid OR userid = friend_requesterid) AND userid <> '$_SESSION[userid]' LEFT JOIN album ON userid = owner_id AND accessibility_code = 'shared' WHERE (friend_requesteeid = '$_SESSION[userid]' OR friend_requesterid = '$_SESSION[userid]') AND status = 'accepted' GROUP BY userid, name";

                        $result = query($link, $query);
                        while ($r = mysqli_fetch_assoc($result)){
                            echo "<tr><td><a href='FriendPictures.php?id=$r[userid]'>$r[name]</a></td><td>$r[sharedalbums]</td><td><input type='checkbox' name='unfriends[]' value='$r[userid]' ></td></tr>";
                        }
                        if (mysqli_num_rows($result) == 0){
                            echo "<tr><td colspan='3' class='text-center text-danger'><b>No Friend Found!</b></td></tr>";
                        }
                    }
                    else {
                        echo "<tr><td colspan='3' class='text-center text-danger'><b>No Friend Found!</b></td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <div class='row text-right'>
            <button type="submit" name="unfriendSubmit" class="btn btn-primary" onclick="return confirm('The selected friends will be unfriended!');">Unfriend Selected</button>
        </div>
        
        <br>
        <h3>Friend Requests:</h3>
        <table class='table'>
            <thead>
                <th>Name</th>
                <th>Accept or Deny</th>
            </thead>
            <tbody>
                <?php 
                    $query = "SELECT userid, name FROM friendship LEFT JOIN user ON userid = friend_requesterid WHERE status = 'request' AND friend_requesteeid = '$_SESSION[userid]'";
                    $result = query($link, $query);
                    while ($r = mysqli_fetch_assoc($result)){
                        echo "<tr><td>$r[name]</td><td><input type='checkbox' name='friendRequests[]' value='$r[userid]' ></td></tr>";
                    }
                    if (mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='2' class='text-center text-danger'><b>No Friend Request Found!</b></td></tr>";
                    }
                    close($link);
                ?>
            </tbody>
        </table>
        <div class='row text-right'>
            <button type="submit" name="acceptSubmit" class="btn btn-primary">Accept Selected</button>
            <button type="submit" name="denySubmit" class="btn btn-primary" onclick="return confirm('The selected requests will be denied!');">Deny Selected</button>
        </div>
        
    </form>
</div>
<script>activeLink(2);</script>

<?php include 'Common/Footer.php'; ?>