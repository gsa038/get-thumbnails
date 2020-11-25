<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use Exception;
use Imagick;

class Thumbnail{

    private string $thumbnailPath;

    public function __construct($columns, $rows, $sourcePath)
    {
        $this->thumbnailPath = $this->createThumbnail($columns, $rows, $sourcePath);
    }

    public function __destruct()
    {
        if (!unlink($this->thumbnailPath)) {
            throw new Exception("Can't delete thumbnail file");
        }
    }

    public function getThumbnailPath() : string
    {
        return $this->thumbnailPath;
    }

    public function createThumbnail(int $columns, int $rows, string $sourcePath): string
    {
        $resultPath = $sourcePath . '_' . $columns . '_' . $rows;
        $thumbnail = new Imagick($sourcePath);
        $thumbnail->thumbnailImage($columns, $rows);
        $thumbnail->writeImage($resultPath);
        $thumbnail->clear();
        return $resultPath;
    }
}