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
        if ($this->isValid) 
        {
            $sourceFileFullName = $_FILES[$sourceFileKey]['name'];
            $this->thumbnailsSourcePath = $_FILES[$sourceFileKey]["tmp_name"];
            list($this->sourceFileNamePart, $this->sourceFileExtensionPart) = $this->getSourceFileNameParts($sourceFileFullName);
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
            try {
                return $_FILES[$sourceFileKey] !== null && (substr($_FILES[$sourceFileKey]['type'], 0, 5) === 'image');
            } catch (Exception $ex) {
                return false;
            }
        }
    }

    private function getSourceFileNameParts(string $sourceFileFullName) : array
    {
        $sourceFileFullName = trim($sourceFileFullName, " \.");
            $nameParts = explode('.', $sourceFileFullName);
            $namePartsCount = count(($nameParts));
            if ($namePartsCount > 2) {
                $sourceFileExtensionPart = $nameParts[$namePartsCount - 1];
                $sourceFileNamePart = implode('.', array_slice($nameParts, 0, $namePartsCount - 1));
            }elseif ($namePartsCount === 2) {
                list($sourceFileNamePart, $sourceFileExtensionPart) = $nameParts;
            } elseif ($namePartsCount === 1)  {
                $sourceFileNamePart = $nameParts[0];
                $sourceFileExtensionPart = '';
            }
            return [$sourceFileNamePart, $$sourceFileExtensionPart];
    }
}