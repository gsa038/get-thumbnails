<?php 
declare(strict_types=1);

namespace App\Application\Console\Service;

use App\Application\Thumbnails\ThumbnailsArchive;

class ThumbnailsRemover
{
    public function __invoke()
    {
        $this->deleteExpiredThumbnails();
    }

    private function deleteExpiredThumbnails() : void
    {
        $dir = ThumbnailsArchive::THUMBNAILS_ARCHIVE_PATH;
        foreach (glob($dir."*") as $file) {
            echo "$file : filesize($file)";
            if(time() - filectime($file) > 400){
                unlink($file);
            }
        }
    }
}