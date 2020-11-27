<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use App\Application\Thumbnails\ThumbnailsSource;
use Exception;
use Imagick;
use ZipArchive;

class ThumbnailsArchive
{
    const THUMBNAILS_ARCHIVE_PATH = '/var/www/thumbnails/';
    private string $archivePath;
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
        $this->archivePath = $this->getThumbnailsArchive();
    }

    public function getArchivepath() : string
    {
        return $this->archivePath;
    }

    private function getArchiveName(string $tmpPath, string $realName) : string
    {
        return md5($tmpPath . $realName);
    }

    private function getThumbnailsArchive() : string
    {
        $sourceImage = new ThumbnailsSource('image');
        $archiveName = $this->getArchiveName($sourceImage->getThumbnailsSourcePath(), $sourceImage->getSourceFileNamePart());
        $archivePath = self::THUMBNAILS_ARCHIVE_PATH . $archiveName;
        $zip = new ZipArchive();
        if (!$zip->open($archivePath, ZipArchive::CREATE)) {
            $this->logger->error("Can't write new archive file");
        }
        foreach ($this->thumbnailSizes as list($rows,$columns)) {
            $thumbnailNameAddition = '_' . $rows . '_' . $columns;
            $thumbnail = new Thumbnail($columns, $rows, $sourceImage->getThumbnailsSourcePath());
            $thumbnailFileName = $sourceImage->getSourceFileNamePart() . $thumbnailNameAddition . '.' . $sourceImage->getSourceFileExtensionPart();
            $zip->addFile($thumbnail->getThumbnailPath(), $thumbnailFileName);
        }
        $zip->close();
        return $archiveName;
    }
}
