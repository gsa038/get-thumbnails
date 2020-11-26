<?php
declare(strict_types=1);

namespace App\Application\Thumbnails;

use Exception;

class ThumbnailsSource
{
    private string $sourceFileExtensionPart = '';
    private string $sourceFileNamePart = '';
    private string $thumbnailsSourcePath;
    private bool $isValid;

    public function __construct(string $sourceFileKey)
    {
        $this->isValid = $this->isValidThumbnailsSource($sourceFileKey);
        if (!$this->isValid) 
        {
            throw new Exception("Not valid Source image");
        }
        $sourceFileFullName = $_FILES[$sourceFileKey]['name'];
        $this->thumbnailsSourcePath = $_FILES[$sourceFileKey]["tmp_name"];
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

    public function getSourceFileExtensionPart()
    {
        return $this->sourceFileExtensionPart;
    }

    public function getSourceFileNamePart()
    {
        return $this->sourceFileNamePart;
    }

    public function getThumbnailsSourcePath()
    {
        return $this->thumbnailsSourcePath;
    }

    public function getIsValid()
    {
        return $this->isValid;
    }

    public function isValidThumbnailsSource(string $sourceFileKey) : bool
    {
        if (count($_FILES) === 1) {
            return $_FILES[$sourceFileKey] !== null && (substr($_FILES[$sourceFileKey]['type'], 0, 5) === 'image');
        }
        return false;
    }
}