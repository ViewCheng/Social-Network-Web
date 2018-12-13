<!DOCTYPE html>
<html lang="en" style="position: relative; min-height: 100%;">
<head>
<title>Algonquin Social Media Network</title>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Expires" content="-1">
        <link rel="icon" type="image/png" sizes="32x32" href="https://s7494.pcdn.co/wp-content/themes/ac-2017/images/favicons/favicon-32x32.png?v=xQd5vya3e2">
        <link rel="icon" type="image/png" sizes="16x16" href="https://s7494.pcdn.co/wp-content/themes/ac-2017/images/favicons/favicon-16x16.png?v=xQd5vya3e2">
        <link href="Contents/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="Scripts/jquery-2.2.4.min.js" type="text/javascript"></script>
        <script src="Contents/js/bootstrap.min.js" type="text/javascript"></script>
        <link href="Contents/FinalCss/Site.css" rel="stylesheet" type="text/css"/>
<!--        <script src="Contents/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="Contents/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
        --><script src="Scripts/Site.js" type="text/javascript"></script>
</head>
<body style="padding-top: 50px; margin-bottom: 60px;">
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
                       data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" style="padding: 10px" href="http://www.algonquincollege.com">
              <img src="Contents/img/AC.png" 
                   alt="Algonquin College" style="max-width:100%; max-height:100%;"/>
          </a>    
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
               <li><a href="Index.php">Home</a></li>
               <li class='dropdown'>
                   <a href="MyFriends.php">My Friends</a>
                   <ul class='dropdown-menu'>
                       <li class='dropdown-item'><a href="AddFriend.php">Add Friends</a></li>
                   </ul>
               </li>
               <li class='dropdown'>
                   <a href="MyAlbums.php">My Albums</a>
                   <ul class='dropdown-menu'>
                       <li class='dropdown-item'><a href="AddAlbum.php">Create Album</a></li>
                   </ul>
               </li>
               <li class="dropdown">
                   <a href="MyPictures.php">My Pictures</a>
                   <ul class='dropdown-menu'>
                       <li class='dropdown-item'><a href="FriendPictures.php">Friend Pictures</a></li>
                   </ul>
               </li>
               <li><a href="UploadPictures.php">Upload Pictures</a></li>
               <li><a id="login"></a></li>
            </ul>
        </div>
      </div>  
    </nav>
<?php
    if (isset($_SESSION['userid'])){ ?>
        <script>loginLink(2)</script>
<?php
    }
    else { ?>
        <script>loginLink(1)</script>
<?php
    }
?>