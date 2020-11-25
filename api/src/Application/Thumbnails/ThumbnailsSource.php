<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;


class ThumbnailsSource
{
    static public function isValidThumbnailsSource() : bool
    {
        if (count($_FILES) === 1) {
            return $_FILES['image'] !== null && (substr($_FILES['image']['type'], 0, 5) === 'image');
        }
        return false;
    }
    
    static public function getThumbnailsSourcePath() : string
    {
        return $_FILES['image']['tmp_name'];
    }
       
}