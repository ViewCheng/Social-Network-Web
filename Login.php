<?php
session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
unset($_SESSION['currentAlbum']);
unset($_SESSION['currentPic']);
unset($_SESSION['fcurrentAlbum']);
unset($_SESSION['fcurrentName']);
unset($_SESSION['fcurrentPic']);
if (isset($_SESSION["userid"])){
    unset($_SESSION["userid"]);
}
$sidErr = "";
$pwdErr = "";
$wrongIdOrPwd = "";
$txtSid = "";
$txtPwd = "";

extract($_POST);
$success = false;
include 'Common/Functions.php';
include 'Common/ConnectDB.php';

if (isset($btnSubmit)){
    $sidErr = ValidateBlank($sid, "User ID");
    $pwdErr = ValidateBlank($pwd, "Password");
    if ($sidErr == "" && $pwdErr == ""){
        $success = true;
    }
    if ($success){
        $link = connect();
        if ($link){
            $query = "SELECT name FROM user WHERE userid = ? AND password = ?";
            $stmt = mysqli_prepare($link, $query);
            $hash = sha1($pwd);
            $sid = htmlspecialchars($sid);
            mysqli_stmt_bind_param($stmt, "ss", $sid, $hash);
            if (mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                $row_nums = mysqli_stmt_num_rows($stmt);
                if($row_nums > 0){
                    mysqli_stmt_bind_result($stmt, $name);
                    mysqli_stmt_fetch($stmt);
                    $_SESSION["username"] = $name;
                    $_SESSION["userid"] = $sid;
                    close($link);
                    $url = isset($_SESSION["url"]) ? $_SESSION["url"] : "Index.php";
                    header("Location: $url");
                    exit();
                }
                else {
                    $wrongIdOrPwd = "Incorect user ID and/or Password!";
                }
            }
        }
        else {
            $err = "The system is not available, try again later.";
        }
        close($link);
    }
}

if (isset($btnReset)){
    $sid = null;
    $pwd = null;
}

if (isset($sid)){
    $txtSid = $sid;
}
if (isset($pwd)){
    $txtPwd = $pwd;
}
?>
<?php include("Common/Header.php"); ?>
<div class="container">
    <div class="row">
        <h1 class="text-center col-sm-6">Log In</h1>
    </div>
    <p>You need to <a href="NewUser.php">sign up</a> if you are new user</p>
    <br>
    <p class="error"><?php echo $wrongIdOrPwd ?></p>
    <form action="Login.php" method="POST">
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="sid">User ID:</label>
          <div class="col-sm-4">
              <input type="text" class="form-control" id="sid" name=sid value="<?php echo $txtSid ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $sidErr ?></span>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="pwd">Password:</label>
          <div class="col-sm-4"> 
              <input type="password" class="form-control" id="pwd" name=pwd value="<?php echo $txtPwd ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $pwdErr ?></span>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnSubmit>Submit</button></div>
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnReset>Clear</button></div>
        </div>
        
    </form>
</div>
<script>activeLink(6);</script>
<?php include("Common/Footer.php") ?>