<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
unset($_SESSION['currentAlbum']);
unset($_SESSION['currentPic']);
unset($_SESSION['fcurrentAlbum']);
unset($_SESSION['fcurrentName']);
unset($_SESSION['fcurrentPic']);
include 'Common/Header.php';
?>

<div class="container">
    <h1>Welcome to Algonquin Social Media Website</h1>
    <p>If you have never used this before, you have to <a href="NewUser.php">sign up</a> first.</p>
    <p>If you have already signed up, you can <a href="Login.php">login</a> now.</p>
</div>
<script>activeLink(1);</script>

<?php include 'Common/Footer.php'; ?>