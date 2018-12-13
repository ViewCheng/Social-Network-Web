<?php 
session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
if (!isset($_SESSION["userid"])){
    $_SESSION["url"] = $_SERVER["PHP_SELF"];
    header("Location: Login.php");
    exit();
}

unset($_SESSION['fcurrentAlbum']);
unset($_SESSION['fcurrentName']);
unset($_SESSION['fcurrentPic']);
include 'Common/ConnectDB.php';
include 'Common/ConstantsAndSettings.php';
include 'Common/Functions.php';
include 'Model/Album.php';
include 'Model/Picture.php';
include 'Model/Comment.php';

$noAlbum = "";
$noPic = "";

extract($_POST);
if (isset($download)){
    if (isset($_SESSION["currentPic"])){
        $downloadPic = Picture::findById($_SESSION["currentPic"]);
        if ($downloadPic != "" && $downloadPic->getAlbum_id() == $_SESSION["currentAlbum"]){
            downloadFile(ORIGINAL_PICTURES_DIR."/".$downloadPic->getFilename());
        }
    }
}
elseif (isset($rotateLeft)){
    if (isset($_SESSION["currentPic"])){
        $rotatePic = Picture::findById($_SESSION["currentPic"]);
        if ($rotatePic != "" && $rotatePic->getAlbum_id() == $_SESSION["currentAlbum"]){
            rotateImage(ORIGINAL_PICTURES_DIR."/".$rotatePic->getFilename(), 90);
            rotateImage(ALBUM_PICTURES_DIR."/".$rotatePic->getFilename(), 90);
            rotateImage(ALBUM_THUMBNAILS_DIR."/".$rotatePic->getFilename(), 90);
            header("Location: MyPictures.php");
        }
    }   
}
elseif (isset($rotateRight)){
    if (isset($_SESSION["currentPic"])){
        $rotatePic = Picture::findById($_SESSION["currentPic"]);
        if ($rotatePic != "" && $rotatePic->getAlbum_id() == $_SESSION["currentAlbum"]){
            rotateImage(ORIGINAL_PICTURES_DIR."/".$rotatePic->getFilename(), -90);
            rotateImage(ALBUM_PICTURES_DIR."/".$rotatePic->getFilename(), -90);
            rotateImage(ALBUM_THUMBNAILS_DIR."/".$rotatePic->getFilename(), -90);
            header("Location: MyPictures.php");
        }
    }
}
elseif (isset($delete)){
    if (isset($_SESSION["currentPic"])){
        $deletePic = Picture::findById($_SESSION["currentPic"]);
        if ($deletePic != "" && $deletePic->getAlbum_id() == $_SESSION["currentAlbum"]){
            $result = $deletePic->delete();
            if ($result == ""){
                unlink(ORIGINAL_PICTURES_DIR."/".$deletePic->getFilename());
                unlink(ALBUM_PICTURES_DIR."/".$deletePic->getFilename());
                unlink(ALBUM_THUMBNAILS_DIR."/".$deletePic->getFilename());
                unset($_SESSION["currentPic"]);
                header("Location: MyPictures.php");
            }
        }
    }
}
elseif(isset($txtComment)){
    if (isset($_SESSION['currentPic']) && trim($txtComment) != ""){
        $cmt = new Comment("", $_SESSION['userid'], $_SESSION['currentPic'], htmlspecialchars($txtComment), "");
        $cmt->save();
    }
}

$link = connect();
if ($link){
    $query = "SELECT album_id, title, date_updated FROM ALBUM WHERE owner_id = '$_SESSION[userid]'";
    $result = query($link, $query);

    if (mysqli_num_rows($result) == 0){
        $noAlbum = "No Album! Create new Album <a href='AddAlbum.php'>here</a>.";
    }
    else {
        while ($r = mysqli_fetch_assoc($result)){
            $al[] = $r;
        }
        if (!isset($_SESSION['currentAlbum']) && !isset($_GET['id'])){
            $_SESSION['currentAlbum'] = $al[0]['album_id'];
        }
        elseif (isset($albums)){
            $_SESSION['currentAlbum'] = $albums;
        }
        elseif (isset($_GET['id'])){
            $_SESSION['currentAlbum'] = htmlspecialchars($_GET['id']);
        }
        $currentAlbum = Album::findById($_SESSION['currentAlbum']);
        if ($currentAlbum == "" || $currentAlbum->getOwner_id() != $_SESSION['userid']){
            $_SESSION['currentAlbum'] = $al[0]['album_id'];
        } else {
            $_SESSION['currentAlbum'] = $currentAlbum->getAlbum_id();
        }
        
        $query = "SELECT picture_id, filename FROM picture WHERE album_id = $_SESSION[currentAlbum]";
        $result = query($link, $query);
        if (mysqli_num_rows($result) == 0){
            $noPic = "No Picture! Upload Pictures <a href='UploadPictures.php'>here</a>.";
        }
        else {
            while ($r = mysqli_fetch_assoc($result)){
                $pl[] = $r;
            }
            if (!isset($_SESSION['currentPic'])){
                $_SESSION['currentPic'] = $pl[0]['picture_id'];
            }
            elseif (isset($pics)){
                $_SESSION['currentPic'] = $pics;
            }
            $currentPic = Picture::findById($_SESSION['currentPic']);
            if ($currentPic == "" || $_SESSION['currentAlbum'] != $currentPic->getAlbum_id()){
                $_SESSION['currentPic'] = $pl[0]['picture_id'];
            } else {
                $_SESSION['currentPic'] = $currentPic->getPicture_id();
            }
            $query = "SELECT filename, title, description, comment_text, date, name FROM picture a left join comment b on a.picture_id = b.picture_id left join user c on b.author_id = c.userid where a.picture_id = $_SESSION[currentPic] order by date desc";
            $result = query($link, $query);
            while ($r = mysqli_fetch_assoc($result)){
                $cl[] = $r;
            }
        }
    }
}
close($link);


include 'Common/Header.php';
?>

<div class="container">
    <h1 class="text-center">My Pictures</h1>
    <form method="POST" action="MyPictures.php">
        <div class="row">
            <div class="col-sm-8 form-group">
                <select class="form-control" name="albums" onchange="this.form.submit()">
                    <?php 
                        if (!empty($al)){
                            foreach ($al as $r){
                                if (isset($_SESSION['currentAlbum']) && $_SESSION['currentAlbum'] == $r['album_id']){
                                    echo "<option value='$r[album_id]' selected>$r[title] - updated on $r[date_updated]</option>";
                                }
                                else {
                                    echo "<option value='$r[album_id]'>$r[title] - updated on $r[date_updated]</option>";
                                }
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-sm-4"><p class="text-danger"><?php echo $noAlbum ?></p></div>
        </div>
        <div class="row">
            <h2 class="text-center col-sm-8"><?php echo !empty($cl) ? $cl[0]['title'] : ""; ?></h2>
        </div>
        
        <div class="row">
            <div class="col-sm-8">
                
                <div class="img-container">
                    <?php
                        if (!empty($cl)){
                            $albumPath = ALBUM_PICTURES_DIR."/".$cl[0]['filename'];
                            echo "<img src=\"$albumPath?rnd=".rand()."\" >
                                    <div class=\"action-list\">
                                        <a id=\"rotateLeft\"><button type=\"submit\" name=\"rotateLeft\" class=\"glyphicon glyphicon-repeat gly-flip-horizontal\"></button></a>
                                        <a id=\"rotateRight\"><button type=\"submit\" name=\"rotateRight\" class=\"glyphicon glyphicon-repeat\"></button></a>
                                        <a id=\"download\"><button type=\"submit\" name=\"download\" class=\"glyphicon glyphicon-download-alt\"></button></a>
                                        <a id=\"delete\"><button type=\"submit\" name=\"delete\" class=\"glyphicon glyphicon-trash\"></button></a>
                                    </div>";
                        }
                        else
                        {
                            echo "<p class='text-danger'>$noPic</p>";
                        }
                    ?>
                </div>
            </div>
            <div class="col-sm-4 comment-area">
                <?php 
                    if ($noPic == ""){
                        $des = $cl[0]['description'];
                        echo "<div>
                                <div class='description'>
                                    <p><b>Description:</b></p>
                                    <p>$des</p>
                                </div> 
                                <div class='comment'>
                                    <p><b>Comment:</b></p>
                                ";
                        foreach ($cl as $c){
                            if ($c['comment_text'] != ""){
                                $date_time = new DateTime($c['date']);
                                $format_date = $date_time->format('Y-m-d');
                                echo "<p><b class='text-primary'>$c[name] ($format_date):</b> $c[comment_text]</p>";
                            }
                        }
                        echo "</div></div>";
                    }
                ?>
                
            </div>
        </div>
        
        <br>
        <div class="row">
            <div class="col-sm-8">
                <div class="scrollable">
                    <?php
                        if (!empty($pl)){
                            $id = 0;
                            foreach ($pl as $thumb){
                                $thumbPath = ALBUM_THUMBNAILS_DIR."/".$thumb['filename'];
                                if (isset($_SESSION["currentPic"]) && $_SESSION["currentPic"] == $thumb["picture_id"]){
                                    echo "<input type=radio name=pics id=$id value='$thumb[picture_id]' checked>";
                                }
                                else
                                    echo "<input type=radio name=pics id=$id value='$thumb[picture_id]' onclick='this.form.submit()'>";
                                echo "<label for=$id>";
                                echo "<img src='$thumbPath?rnd=".rand()."' class=img-thumbnail >";
                                echo "</label>";
                                $id++;
                            }
                        }
                    ?>
                </div>
                
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                        if ($noPic == "" && $noAlbum == "") {
                            echo "<textarea class=\"form-control\" name=\"txtComment\" onkeypress=\"enterComment(event)\" placeholder=\"Leave a comment...\"></textarea>";
                        }
                    ?>
                </div>
            </div>
        </div>
        
        

    </form>
</div>
<script>
    $(document).ready(function(){
        activeLink(4);
        $(".comment-area").height($(".img-container").height());
        $("textarea").outerHeight($(".scrollable").outerHeight());
    });
    function enterComment(e){
        if (e.keyCode === 13 && !e.shiftKey && $("textarea").val().trim() != ""){
            e.preventDefault();
            $("form").submit();
        }
    }
    $(window).resize(function(){
        $(".comment-area").height($(".img-container").height());
        $("textarea").outerHeight($(".scrollable").outerHeight());
    });
</script>
<?php include("Common/Footer.php") ?>