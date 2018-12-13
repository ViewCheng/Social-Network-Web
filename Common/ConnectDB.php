<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function connect(){
    $dbConnection = parse_ini_file('db_connection.ini');
    extract($dbConnection);
    $link = mysqli_connect($host, $username, $password, $dbName);
    if (!$link){
        return NULL;
    }
    return $link;
}
function close($link){
    mysqli_close($link);
}
function query($link, $query){
    return mysqli_query($link, $query);
}