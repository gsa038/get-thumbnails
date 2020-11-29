<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use App\Application\Thumbnails\Exceptions\ThumbnailsBadRequestException;
use App\Application\Thumbnails\Exceptions\ThumbnailsInternalServerErrorException;
use App\Application\Thumbnails\ThumbnailsSource;
use ZipArchive;
use JsonSerializable;

class ThumbnailsArchive implements JsonSerializable
{
    const THUMBNAILS_ARCHIVE_PATH = '/var/www/thumbnails/';
    private string $archiveName;
    private int $archiveSize;
    private array $ThumbnailsArray = [];
    private array $thumbnailSizes = [
        [1000,1000],
        [900,900],
        [800,800],
        [700,700],
        [600,600],
        [500,500],
        [400,400],
        [300,300],
        [200,200],
        [100,100]
    ];

    public function __construct()
    {
        $this->createThumbnailsArchive();
        $this->archiveSize = $this->getArchiveSize();
    }

    public function getArchiveName() : string
    {
        return $this->archiveName;
    }

    private function setArchiveName(string $tmpPath, string $realName) : void
    {
        $this->archiveName = md5($tmpPath . $realName);
    }

    private function createThumbnailsArchive() : void
    {
        $sourceImage = new ThumbnailsSource('image');
        if (!$sourceImage->getIsValid()) {
            throw new ThumbnailsBadRequestException('Wrong input file');
        }
        $this->setArchiveName($sourceImage->getThumbnailsSourcePath(), $sourceImage->getSourceFileNamePart());
        $archivePath = self::THUMBNAILS_ARCHIVE_PATH . $this->archiveName;
        $zip = new ZipArchive();
        if (!$zip->open($archivePath, ZipArchive::CREATE)) {
            throw new ThumbnailsInternalServerErrorException('Can\'t write new archive file');
        }
        foreach ($this->thumbnailSizes as list($rows,$columns)) {
            $thumbnailNameAddition = '_' . $rows . '_' . $columns;
            $thumbnail = new Thumbnail($columns, $rows, $sourceImage->getThumbnailsSourcePath());
            array_push($this->ThumbnailsArray, $thumbnail);
            $thumbnailFileName = $sourceImage->getSourceFileNamePart() . $thumbnailNameAddition . '.' . $sourceImage->getSourceFileExtensionPart();
            $zip->addFile($thumbnail->getThumbnailPath(), $thumbnailFileName);
        }
        $zip->close();
    }

    private function getArchiveSize() : int
    {
        return filesize(self::THUMBNAILS_ARCHIVE_PATH . $this->archiveName);
    }

    public function jsonSerialize()
    {
        return [
            'archiveName' => $this->archiveName,
            'archiveSize' => $this->archiveSize,
        ];
    }
}
