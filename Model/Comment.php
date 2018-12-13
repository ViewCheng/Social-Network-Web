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
class Comment {
    //put your code here
    private $comment_id;
    private $author_id;
    private $picture_id;
    private $comment_text;
    private $date;
    
    function __construct($comment_id, $author_id, $picture_id, $comment_text, $date) {
        $this->comment_id = $comment_id;
        $this->author_id = $author_id;
        $this->picture_id = $picture_id;
        $this->comment_text = $comment_text;
        $this->date = $date;
    }

    public function save(){
        $result = "";
        $link = connect();
        if ($link){
            $query = "INSERT INTO comment(author_id, picture_id, comment_text) VALUES(?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'sss', $this->author_id, $this->picture_id, $this->comment_text);
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
}
