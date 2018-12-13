<?php 
session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
if (isset($_SESSION["userid"])){
    unset($_SESSION["userid"]);
}
unset($_SESSION['currentAlbum']);
unset($_SESSION['currentPic']);
unset($_SESSION['fcurrentAlbum']);
unset($_SESSION['fcurrentName']);
unset($_SESSION['fcurrentPic']);
$sidErr = "";
$nameErr = "";
$phoneErr = "";
$pwdErr = "";
$pwd2Err = "";

$txtSid = "";
$txtName = "";
$txtPhone = "";
$txtPwd = "";
$txtPwd2 = "";

$success = false;
include 'Common/ConnectDB.php';
include 'Common/Functions.php';
include 'Model/User.php';

extract($_POST);
if (isset($btnSubmit)){
    $sidErr = ValidateSid($sid);
    $nameErr = ValidateName($name);
    $phoneErr = ValidatePhone($phone);
    $pwdErr = ValidatePassword($pwd);
    if (trim($pwd2) == ""){
        $pwd2Err = "Password Again cannot be blank";
    }
    else if ($pwd != $pwd2){
        $pwd2Err = "Password and Password Again do not match";
    }
    if ($nameErr == "" && $sidErr == "" && $phoneErr == "" && $pwdErr == "" && $pwd2Err == ""){
        $success = true;
    }
    if ($success){
        $u = new User(htmlspecialchars($sid), htmlspecialchars($name), $phone, $pwd);
        $result = $u->save();
        if ($result == ""){
            header("Location: Login.php");
            exit();
        }
    }
}

if (isset($btnReset)){
    $name = null;
    $sid = null;
    $phone = null;
    $pwd = null;
    $pwd2 = null;
}

if (isset($name)){
    $txtName = $name;
}
if (isset($sid)){
    $txtSid = $sid;
}
if (isset($phone)){
    $txtPhone = $phone;
}
if (isset($pwd)){
    $txtPwd = $pwd;
}
if (isset($pwd2)){
    $txtPwd2 = $pwd2;
}
?>
<?php include("Common/Header.php") ?>
<div class="container">
    <div class="row">
        <h1 class="text-center col-sm-6">Sign Up</h1>
    </div>
    <p>All fields are required</p>
    <br>
    <form action="NewUser.php" method="POST">
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="sid">User ID:</label>
          <div class="col-sm-4">
              <input type="text" class="form-control" id="sid" name=sid value="<?php echo $txtSid ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $sidErr ?></span>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="name">Name:</label>
          <div class="col-sm-4"> 
            <input type="text" class="form-control" id="name" name=name value="<?php echo $txtName ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $nameErr ?></span>
        </div>
        <div class="form-group row">
            <div class="col-form-label col-sm-2">
                <label for="phone">Phone Number:</label>
                <br>
                <span>(nnn-nnn-nnnn)</span>
            </div>
            <div class="col-sm-4" style="margin: auto 0;">
                <input type="text" class="form-control" id="phone" name=phone value="<?php echo $txtPhone ?>">
            </div>
            <span class="col-sm-6 error" style="margin: auto 0;"><?php echo $phoneErr ?></span>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="pwd">Password:</label>
          <div class="col-sm-4"> 
              <input type="password" class="form-control" id="pwd" name=pwd value="<?php echo $txtPwd ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $pwdErr ?></span>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-2" for="pwd2">Password Again:</label>
          <div class="col-sm-4"> 
              <input type="password" class="form-control" id="pwd2" name=pwd2 value="<?php echo $txtPwd2 ?>">
          </div>
          <span class="col-sm-6 error"><?php echo $pwd2Err ?></span>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnSubmit>Submit</button></div>
            <div class="col-sm-2"><button class="btn btn-primary btn-block" type="submit" name=btnReset>Clear</button></div>
        </div>
        
    </form>
</div>

<?php include("Common/Footer.php") ?>