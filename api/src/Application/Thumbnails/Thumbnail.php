<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use App\Application\Thumbnails\Exceptions\ThumbnailsInternalServerErrorException;
use Exception;
use Imagick;

class Thumbnail{

    private string $thumbnailPath;

    public function __construct($columns, $rows, $sourcePath)
    {
        $this->thumbnailPath = $this->createThumbnail($columns, $rows, $sourcePath);
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
        try {
            $thumbnail->writeImage($resultPath);
        } catch (Exception $ex) {
            throw new ThumbnailsInternalServerErrorException('Can\'t write thumbnail file');
        }
        $thumbnail->clear();
        return $resultPath;
    }
}