<?php 

declare(strict_types=1);

namespace App\Controller\utils;

class ThumbnailsRemover
{
    public static function deleteExpiredThumbnails() : void
    {
        $dir = '/var/www/thumbnails/';
        foreach (glob($dir."*") as $file) {
            if(time() - filectime($file) > 86400){
                unlink($file);
            }
        }
    }
}