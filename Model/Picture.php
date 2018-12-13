<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Huy
 */
class Picture {
    //put your code here
    private $picture_id;
    private $album_id;
    private $filename;
    private $title;
    private $description;
    private $date_added;
    
    function __construct($picture_id, $album_id, $filename, $title, $description, $date_added) {
        $this->picture_id = $picture_id;
        $this->album_id = $album_id;
        $this->filename = $filename;
        $this->title = $title;
        $this->description = $description;
        $this->date_added = $date_added;
    }

    public function save($owner_id){
        $album = Album::findById($this->album_id);
        if ($album == "" || $album->getOwner_id() != $owner_id){
            return "Failed to Upload $this->filename";
        }
        $result = "";
        $link = connect();
        if ($link){
            $query = "INSERT INTO picture(album_id, filename, title, description, date_added) VALUES(?, ?, ?, ?, curdate())";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $this->album_id, $this->filename, $this->title, $this->description);
            if (!mysqli_stmt_execute($stmt)){
                $result = "The system is not available, try again later.";
            } else {
                $query = "UPDATE album SET date_updated = curdate() WHERE album_id = $this->album_id";
                query($link, $query);
            }
        }
        else {
            $result = "The system is not available, try again later.";
        }
        close($link);
        return $result;
    }
    
    public static function findById($id){
        $link = connect();
        $query = "SELECT * FROM picture WHERE picture_id = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0){
                mysqli_stmt_bind_result($stmt, $picture_id, $album_id, $filename, $title, $description, $date_added);
                mysqli_stmt_fetch($stmt);
                $instance = new self($picture_id, $album_id, $filename, $title, $description, $date_added);
                close($link);
                return $instance;
            }
        }
        close($link);
        return "";
    }
    
    public function delete(){
        $result = "";
        $link = connect();
        if ($link){
            $query = "DELETE FROM picture WHERE picture_id = $this->picture_id";
            query($link, $query);
            if (mysqli_affected_rows($link) != 1){
                $result = "The system is not available, try again later.";
            }
        }
        else {
            $result = "The system is not available, try again later.";
        }
        close($link);
        return $result;
    }

    function getAlbum_id() {
        return $this->album_id;
    }

    function getFilename() {
        return $this->filename;
    }

    function getPicture_id() {
        return $this->picture_id;
    }


}
