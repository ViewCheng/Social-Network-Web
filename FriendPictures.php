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
include 'Common/ConnectDB.php';
include 'Common/ConstantsAndSettings.php';
include 'Common/Functions.php';
include 'Model/User.php';
include 'Model/Album.php';
include 'Model/Picture.php';
include 'Model/Comment.php';

$noAlbum = "";
$noPic = "";

extract($_POST);

if(isset($txtComment)){
    if (isset($_SESSION['fcurrentPic']) && trim($txtComment) != ""){
        $cmt = new Comment("", $_SESSION['userid'], $_SESSION['fcurrentPic'], htmlspecialchars($txtComment), "");
        $cmt->save();
    }
}

$link = connect();
if ($link){
    $query = "SELECT album_id, title, date_updated, owner_id, name FROM album LEFT JOIN user ON userid = owner_id WHERE owner_id in (SELECT userid FROM friendship LEFT JOIN user ON (userid = friend_requesteeid OR userid = friend_requesterid) AND userid <> ? WHERE (friend_requesteeid = ? OR friend_requesterid = ?) AND status = 'accepted') AND accessibility_code = 'shared'";
    if (isset($_GET["id"])){
        $query .= " AND owner_id = ?";
    }
    $stmt = mysqli_prepare($link, $query);
    if (isset($_GET["id"])){
        $id = htmlspecialchars($_GET['id']);
        mysqli_stmt_bind_param($stmt, 'ssss', $_SESSION['userid'], $_SESSION['userid'], $_SESSION['userid'], $id);
    }
    else {
        mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['userid'], $_SESSION['userid'], $_SESSION['userid']);
    }
    if (mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        $num_rows = mysqli_stmt_num_rows($stmt);
        if ($num_rows == 0){
            $noAlbum = "No Shared Album from Friends!";
            if (isset($_GET['id'])){
                $stmt = mysqli_prepare($link, "SELECT name FROM user WHERE userid = ?");
                mysqli_stmt_bind_param($stmt, 's', $id);
                if (mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    $num_rows = mysqli_stmt_num_rows($stmt);
                    if ($num_rows > 0){
                        mysqli_stmt_bind_result($stmt, $fName);
                        mysqli_stmt_fetch($stmt);
                        $_SESSION['fcurrentName'] = $fName;
                    }
                }
            } 
            if (!isset($_SESSION['fcurrentName']))
            {
                $_SESSION['fcurrentName'] = "";
            }
        }
        else {
            mysqli_stmt_bind_result($stmt, $aid, $tl, $dul, $oid, $nl);
            while (mysqli_stmt_fetch($stmt)){
                $al[] = array('album_id' => $aid, 'title' => $tl, 'date_updated' => $dul, 'owner_id' => $oid, 'name' => $nl);
                $fidl[] = $oid;
            }
            if (!isset($_SESSION['fcurrentAlbum'])){
                $_SESSION['fcurrentAlbum'] = $al[0]['album_id'];
                $_SESSION['fcurrentName'] = $al[0]['name'];
            }
            elseif (isset($albums)){
                $_SESSION['fcurrentAlbum'] = $albums;
            } else {
                $_SESSION['fcurrentAlbum'] = $al[0]['album_id'];
                $_SESSION['fcurrentName'] = $al[0]['name'];
            }
            $currentAlbum = Album::findById($_SESSION['fcurrentAlbum']);

            if ($currentAlbum == "" || !in_array($currentAlbum->getOwner_id(), $fidl) ){
                $_SESSION['fcurrentAlbum'] = $al[0]['album_id'];
                $_SESSION['fcurrentName'] = $al[0]['name'];
            } else {
                $_SESSION['fcurrentAlbum'] = $currentAlbum->getAlbum_id();
                $_SESSION['fcurrentName'] = $al[array_search($currentAlbum->getOwner_id(), $fidl)]['name'];
            }

            $query = "SELECT picture_id, filename FROM picture WHERE album_id = $_SESSION[fcurrentAlbum]";
            $result = query($link, $query);
            if (mysqli_num_rows($result) == 0){
                $noPic = "No Picture!";
            }
            else {
                while ($r = mysqli_fetch_assoc($result)){
                    $pl[] = $r;
                }
                if (!isset($_SESSION['fcurrentPic'])){
                    $_SESSION['fcurrentPic'] = $pl[0]['picture_id'];
                }
                elseif (isset($pics)){
                    $_SESSION['fcurrentPic'] = $pics;
                }
                $currentPic = Picture::findById($_SESSION['fcurrentPic']);
                if ($currentPic == "" || $_SESSION['fcurrentAlbum'] != $currentPic->getAlbum_id()){
                    $_SESSION['fcurrentPic'] = $pl[0]['picture_id'];
                } else {
                    $_SESSION['fcurrentPic'] = $currentPic->getPicture_id();
                }
                $query = "SELECT filename, title, description, comment_text, date, name FROM picture a left join comment b on a.picture_id = b.picture_id left join user c on b.author_id = c.userid where a.picture_id = $_SESSION[fcurrentPic] order by date desc";
                $result = query($link, $query);
                while ($r = mysqli_fetch_assoc($result)){
                    $cl[] = $r;
                }
            }
        }
    }

    
}
close($link);


include 'Common/Header.php';
?>

<div class="container">
    <h1 class="text-center"><?php echo $_SESSION['fcurrentName'] ?>'s Pictures</h1>
    <form method="POST" action="FriendPictures.php<?php echo "?".htmlspecialchars($_SERVER['QUERY_STRING']); ?>">
        <div class="row">
            <div class="col-sm-8 form-group">
                <select class="form-control" name="albums" onchange="this.form.submit()">
                    <?php 
                        if (!empty($al)){
                            foreach ($al as $r){
                                if (isset($_SESSION['fcurrentAlbum']) && $_SESSION['fcurrentAlbum'] == $r['album_id']){
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
                
                <div class="img-container friend-pic">
                    <?php
                        if (!empty($cl)){
                            $albumPath = ALBUM_PICTURES_DIR."/".$cl[0]['filename'];
                            echo "<img src=\"$albumPath?rnd=".rand()."\" >";
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
                                if (isset($_SESSION["fcurrentPic"]) && $_SESSION["fcurrentPic"] == $thumb["picture_id"]){
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