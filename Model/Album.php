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
class Album {
    //put your code here
    private $album_id;
    private $title;
    private $accessibility_code;
    private $description;
    private $owner_id;
    private $date_updated;
    
    function __construct($album_id, $title, $accessibility_code, $description, $owner_id, $date_updated) {
        $this->title = $title;
        $this->accessibility_code = $accessibility_code;
        $this->description = $description;
        $this->owner_id = $owner_id;
        $this->album_id = $album_id;
        $this->date_updated = $date_updated;
    }
    
    public function save(){
        $result = "";
        $link = connect();
        if ($link){
            $query = "INSERT INTO album(title, description, date_updated, owner_id, accessibility_code) VALUES(?, ?, curdate(), ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $this->title, $this->description, $this->owner_id, $this->accessibility_code);
            if (!mysqli_stmt_execute($stmt)){
                $result = "The system is not available, try again later.";
            }
        }
        else {
            $result = "The system is not available, try again later.";
        }
        close($link);
        return $result;
    }
    
    private function getNumOfPics(){
        $link = connect();
        $query = "SELECT COUNT(*) as numOfPics FROM PICTURE WHERE album_id = $this->album_id GROUP BY album_id";
        $result = query($link, $query);
        $r = mysqli_fetch_assoc($result);
        close($link);
        if (is_numeric($r['numOfPics'])){
            return $r['numOfPics'];
        }
        return 0;
    }
    
    public function toHTML($accessibility){
        $accessibilityHTML = "<select class='form-control' style='width: fit-content;' name='accessibilities[]' >";
        foreach ($accessibility as $r){
            $accessibilityHTML .= $r['accessibility_code'] == $this->accessibility_code ? "<option value=$r[accessibility_code] selected>$r[description]</option>" : "<option value=$r[accessibility_code]>$r[description]</option>";
        }
        $accessibilityHTML .= "</select>";
        $numOfPics = $this->getNumOfPics();
        return "<tr><td><a href='MyPictures?id=$this->album_id'>$this->title</a></td><td>$this->date_updated</td><td>$numOfPics</td><td>$accessibilityHTML</td><td><a href='$_SERVER[PHP_SELF]?action=delete&id=$this->album_id' onclick=\"return confirm('Are you sure to delete the album?');\" >Delete</a><input type=hidden name=albums[] value='$this->album_id' ></td></tr>";
    }
    
    public static function findById($id){
        $link = connect();
        $query = "SELECT * FROM ALBUM WHERE album_id = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0){
                mysqli_stmt_bind_result($stmt, $album_id, $title, $description, $date_updated, $owner_id, $accessibility_code);
                mysqli_stmt_fetch($stmt);
                $instance = new self($album_id, $title, $accessibility_code, $description, $owner_id, $date_updated);
                close($link);
                return $instance;
            }
        }
        close($link);
        return "";
    }
    
    public function update($accessibility_code){
        if ($this->accessibility_code == $accessibility_code) {
            return "";
        }
        $result = "";
        $link = connect();
        if ($link){
            $query = "UPDATE album SET accessibility_code = ? WHERE album_id = $this->album_id";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 's', $accessibility_code);
            if (!mysqli_stmt_execute($stmt)){
                $result = "The system is not available, try again later.";
            }
        }
        else {
            $result = "The system is not available, try again later.";
        }
        close($link);
        return $result;
    }
    
    public function delete(){
        $result = "";
        $link = connect();
        if ($link){
            $query = "DELETE FROM album WHERE album_id = $this->album_id";
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
    
    function getOwner_id() {
        return $this->owner_id;
    }
    
    function getAlbum_id() {
        return $this->album_id;
    }



}
