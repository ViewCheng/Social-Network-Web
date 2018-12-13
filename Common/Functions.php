<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

# User Information Processing

function blankError($in){
    return $in . " can not be blank";
}

function ValidateName($name){
    $err = "";
    if (!isset($name) || trim($name) == "")
        $err = blankError("Name");
    return $err;
}

function ValidateSid($sid){
    if (!isset($sid) || trim($sid) == "") {
        return blankError("User ID");
    }
    if (strlen($sid) > 16){
        return "Your user ID is too long";
    }
    $link = connect();
    if (!$link) {
        return "The system is not available, try again later.";
    }
    $query = "SELECT * FROM user WHERE userid = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $sid);
    if (mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        $row_nums = mysqli_stmt_num_rows($stmt);
        close($link);
        if($row_nums > 0){
            return "A user with this ID has already signed up";
        }
        return "";
    } else {
        close($link);
        return "The system is not available, try again later.";
    }
    
}

function ValidateFid($fid, $sid){
    if (!isset($fid) || trim($fid) == "") {
        return blankError("ID");
    }
    if ($fid == $sid){
        return "You cannot send friend request to yourself";
    }
    $link = connect();
    if (!$link) {
        return "The system is not available, try again later.";
    }
    $query = "SELECT * FROM user WHERE userid = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $fid);
    if (mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        $row_nums = mysqli_stmt_num_rows($stmt);
        close($link);
        if($row_nums > 0){
            return "";
        }
        else {
            return "The user $fid does not exist";
        }
    } else {
        close($link);
        return "The system is not available, try again later.";
    }
    
}

function ValidateBlank($str, $type){
    if (!isset($str) || trim($str) == "") {
        return blankError($type);
    }
    return "";
}

function ValidatePassword($pwd){
    if (!isset($pwd) || trim($pwd) == "" || strlen($pwd) < 6 || !preg_match('@[A-Z]@', $pwd) || !preg_match('@[a-z]@', $pwd) || !preg_match('@[0-9]@', $pwd)){
        return "Password is not in correct format";
    }
    return "";
}

function ValidatePhone($phone){
    if (!isset($phone) || trim($phone) == "" || !preg_match("/[2-9][0-9]{2}-[2-9][0-9]{2}-[0-9]{4}/", trim($phone)))
        return "Phone number is not in correct format";
    return "";
}

# Picture Processing

function save_uploaded_file($destinationPath, $tmp_name, $name)
{
    if (!file_exists($destinationPath))
    {
        mkdir($destinationPath);
    }

    $tempFilePath = $tmp_name;
    $filePath = $destinationPath."/".$name;

    $pathInfo = pathinfo($filePath);
    $dir = $pathInfo['dirname'];
    $fileName = $pathInfo['filename'];
    $ext = $pathInfo['extension'];

    //make sure not to overwrite existing files 
    $i="";
    while (file_exists($filePath))
    {	
        $i++;
        $filePath = $dir."/".$fileName."_".$i.".".$ext;
    }
    move_uploaded_file($tempFilePath, $filePath);

    return $filePath;
}

function resamplePicture($filePath, $destinationPath, $maxWidth, $maxHeight)
{
    if (!file_exists($destinationPath))
    {
        mkdir($destinationPath);
    }

    $imageDetails = getimagesize($filePath);

    $originalResource = null;
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
        $originalResource = imagecreatefromjpeg($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
        $originalResource = imagecreatefrompng($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
        $originalResource = imagecreatefromgif($filePath);
    }
    $widthRatio = $imageDetails[0] / $maxWidth;
    $heightRatio = $imageDetails[1] / $maxHeight;
    $ratio = max($widthRatio, $heightRatio);

    $newWidth = $imageDetails[0] / $ratio;
    $newHeight = $imageDetails[1] / $ratio;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    $success = imagecopyresampled($newImage, $originalResource, 0, 0, 0, 0, $newWidth, $newHeight, $imageDetails[0], $imageDetails[1]);

    if (!$success)
    {
        imagedestroy($newImage);
        imagedestroy($originalResource);
        return "";
    }
    $pathInfo = pathinfo($filePath);
    $newFilePath = $destinationPath."/".$pathInfo['filename'];
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
        $newFilePath .= ".".$pathInfo['extension'];
        $success = imagejpeg($newImage, $newFilePath, 100);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
        $newFilePath .= ".png";
        $success = imagepng($newImage, $newFilePath, 0);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
        $newFilePath .= ".gif";
        $success = imagegif($newImage, $newFilePath);
    }

    imagedestroy($newImage);
    imagedestroy($originalResource);

    if (!$success)
    {
        return "";
    }
    else
    {
        return $newFilePath;
    }
}

function rotateImage($filePath, $degrees)
{
    $imageDetails = getimagesize($filePath);

    $originalResource = null;
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
        $originalResource = imagecreatefromjpeg($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
        $originalResource = imagecreatefrompng($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
        $originalResource = imagecreatefromgif($filePath);
    }

    $rotatedResource = imagerotate($originalResource, $degrees, 0);

    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
        $success = imagejpeg($rotatedResource, $filePath, 100);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
        $success = imagepng($rotatedResource, $filePath, 0);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
        $success = imagegif($rotatedResource, $filePath);
    }

    imagedestroy($rotatedResource);
    imagedestroy($originalResource);
}

function downloadFile($filePath){
    $fileName = basename($filePath);
    $fileLength = filesize($filePath);
    
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename = \"$fileName\"");
    header("Content-Length: $fileLength");
    header("Content-Description: File Transfer");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: private");
    
    ob_clean();
    flush();
    readfile($filePath);
    flush();
}