<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use App\Application\Thumbnails\ThumbnailsSource;
use Exception;
use Imagick;
use ZipArchive;

class Thumbnails
{
    const THUMBNAILS_ARCHIVE_PATH = '/var/www/thumbnails/';
    private string $archivePath;
    private string $sourceFileExtensionPart = '';
    private string $sourceFileNamePart = '';
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
        $this->writeSourceFileNamePartsToProperties();
        $this->archivePath = $this->getThumbnailsArchive();
    }

    public function getArchivepath() : string
    {
        return $this->archivePath;
    }

    private function writeSourceFileNamePartsToProperties() : void
    {
        $sourceFileFullName = $_FILES['image']['name'];
        $sourceFileFullName = trim($sourceFileFullName, " \.");
        $nameParts = explode('.', $sourceFileFullName);
        $namePartsCount = count(($nameParts));
        if ($namePartsCount > 2) {
            $this->sourceFileExtensionPart = $nameParts[$namePartsCount - 1];
            $this->sourceFileNamePart = implode('.', array_slice($nameParts, 0, $namePartsCount - 1));
        }elseif ($namePartsCount === 2) {
            list($this->sourceFileNamePart, $this->sourceFileExtensionPart) = $nameParts;
        } elseif ($namePartsCount === 1)  {
            $this->sourceFileNamePart = $nameParts[0];
        } else {
            throw new Exception("Source file name problem");
        }
    }

    private function getArchiveName(string $tmpName, string $realName) : string
    {
        return md5($tmpName . $realName);
    }

    private function getThumbnailsArchive() : string
    {
        $archiveName = $this->getArchiveName(ThumbnailsSource::getThumbnailsSourcePath(), $this->sourceFileNamePart);
        $archivePath = self::THUMBNAILS_ARCHIVE_PATH . $archiveName;
        $zip = new ZipArchive();
        if (!$zip->open($archivePath, ZipArchive::CREATE)) {
            throw new Exception("Can't write new archive file");
        }
        $thumbnailNameBase = $this->sourceFileNamePart;
        foreach ($this->thumbnailSizes as list($rows,$columns)) {
            $thumbnailNameAddition = $rows . '_' . $columns . '.' . $this->sourceFileExtensionPart;
            $thumbnail = new Thumbnail($columns, $rows, ThumbnailsSource::getThumbnailsSourcePath());
            $thumbnailPath = $thumbnail->getThumbnailPath();
            $thumbnailFileName = $thumbnailNameBase . '_' . $thumbnailNameAddition;
            $zip->addFile($thumbnailPath, $thumbnailFileName);
        }
        $zip->close();
        return $archiveName;
    }
}
