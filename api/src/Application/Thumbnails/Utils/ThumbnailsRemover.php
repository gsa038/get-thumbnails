<?php 

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails\Service;

use App\Application\Thumbnails\Thumbnails;

class ThumbnailsRemover
{
    public static function deleteExpiredThumbnails() : void
    {
        $dir = Thumbnails::THUMBNAILS_ARCHIVE_PATH;
        foreach (glob($dir."*") as $file) {
            if(time() - filectime($file) > 86400){
                unlink($file);
            }
        }
    }
}