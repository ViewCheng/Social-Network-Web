<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('ORIGINAL_PICTURES_DIR', "Pictures/OriginalPictures");
define('ALBUM_PICTURES_DIR', "Pictures/AlbumPictures");
define('ALBUM_THUMBNAILS_DIR', "Pictures/AlbumThumbnails");

define('IMAGE_MAX_WIDTH', 1024);
define('IMAGE_MAX_HEIGHT', 800);

define('THUMB_MAX_WIDTH', 100);
define('THUMB_MAX_HEIGHT', 100);

$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
date_default_timezone_set("America/Toronto");