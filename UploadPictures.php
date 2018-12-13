<?php 
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
include 'Model/Picture.php';
$txtTitle = "";
$txtDes = "";
$noAlbum = "";
extract($_POST);

if (isset($btnSubmit)){
    if (isset($album)){
        $uploadedAlbum = Album::findById($album);
        if ($uploadedAlbum != "" && $uploadedAlbum->getOwner_id() == $_SESSION['userid']){
            for ($j = 0; $j < count($_FILES['img']['tmp_name']); $j++)
            {
                $fileName = $_FILES['img']['name'][$j];
                if ($_FILES['img']['error'][$j] == 0)
                {
                    $filePath = save_uploaded_file(ORIGINAL_PICTURES_DIR, $_FILES['img']['tmp_name'][$j], $fileName);
                    $imageDetails = getimagesize($filePath);

                    if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes))
                    {
                        resamplePicture($filePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
                        resamplePicture($filePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
                        $pic = new Picture("", $album, basename($filePath), htmlspecialchars($title), htmlspecialchars($description), "");
                        $result = $pic->save($_SESSION['userid']);
                        if ($result != ""){
                            $error[] = $result;
                            unlink($filePath);
                            unlink(ALBUM_PICTURES_DIR."/".$fileName);
                            unlink(ALBUM_THUMBNAILS_DIR."/".$fileName);
                        }
                        else {
                            $success[] = "File ($fileName) was uploaded successfully";
                        }
                    }
                    else
                    {
                        $error[] = "Uploaded file ($fileName) is not a supported type"; 
                        unlink($filePath);
                    }
                }
                elseif ($_FILES['img']['error'][$j]  == 1)
                {			
                    $error[] = "$fileName is too large <br/>";
                }
                elseif ($_FILES['img']['error'][$j]  == 4)
                {
                    $error[] = "No upload file specified <br/>"; 
                }
                else
                {
                    $error[] = "Error happened while uploading the file(s). Try again late<br/>"; 
                }
            }
        } else {
            $error[] = "Failed to upload picture(s)";
        }
    }
    else {
        $noAlbum = "You must select 1 album";
    }
}
include("Common/Header.php");
?>
<div class="container">
    <h1 class="text-center">Upload Pictures</h1>
    <p>Accepted picture types: JPG(JPEG), GIF and PNG.</p>
    <p>You can upload multiple pictures at a time by pressing the shift key while selecting pictures</p>
    <p>When upload multiple pictures, the title and description will be applied to all pictures</p>
    <br>
    <form method="POST" action="UploadPictures.php" enctype="multipart/form-data">
        <div class="form-group row">
            <label class="col-form-label col-sm-2" for="album">Upload to Album:</label>
            <div class="col-sm-4">
                <select class="form-control" id="album" name="album">
                    <?php
                        $link = connect();
                        if ($link){
                            $query = "SELECT album_id, title FROM ALBUM WHERE owner_id = '$_SESSION[userid]'";
                            $result = query($link, $query);
                            while ($r = mysqli_fetch_assoc($result)){
                                echo "<option value='$r[album_id]'>$r[title]</option>";
                            }
                            if (mysqli_num_rows($result) == 0){
                                $noAlbum = "No Album! Create new Album <a href='AddAlbum.php'>here</a>.";
                            }
                        }
                        close($link);
                    ?>
                </select>
            </div>
            <span class="col-sm-6 text-danger"><?php echo $noAlbum ?></span>
        </div>
        <div class="form-group row">
            <label for="img" class="col-form-label col-sm-2">File to Upload:</label>
            <div class="col-sm-4">
                <input type="file" accept="image/gif,image/jpeg,image/png,image/jpg" name="img[]" id="img" multiple="multiple" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-2" for="title">Title:</label>
            <div class="col-sm-4">
                <input type="text" value="<?php echo $txtTitle ?>" id="title" name="title" class="form-control">
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
    <br>
    <?php
    if (!empty($success)){
        echo "<ul class='text-success'>";
        foreach ($success as $s){
            echo "<li>$s</li>";
        }
        echo "</ul>";
    }
    if (!empty($error)){
        echo "<ul class='text-danger'>";
        foreach ($error as $e){
            echo "<li>$e</li>";
        }
        echo "</ul>";
    }
    ?>
</div>
<script>activeLink(5);</script>
<?php include("Common/Footer.php") ?>