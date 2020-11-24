<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZipArchive;
use Imagick;

class PostThumbnailsArchive
{
    private string $thumbnailsArchivePath = '/var/www/thumbnails/';
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

    public function __invoke(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        $archivePath = null;
        if ($this->isValidUploadedImage() && $this->writeSourceFileNamePartsToProperties()) {
            $archivePath = $this->getThumbnailsArchive();
            $status = 200;
            $message = 'OK';
            $messageDescription = '';
        } else {
            $message = 'Bad Request';
            $messageDescription = 'Wrong input file';
            $status = 500;
        }
        $response->getBody()->write(json_encode(['message' => $message, 'statusCode' => $status, 'description' => $messageDescription, 'archive' => $archivePath]));
        $response = $response->withStatus($status, $message);
        ob_clean();   
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://client.localhost:8880')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS, POST')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');
    }

    private function isValidUploadedImage() : bool
    {
        if (count($_FILES) === 1) {
            return $_FILES['image'] !== null && (substr($_FILES['image']['type'], 0, 5) === 'image');
        }
        return false;
    }

    private function writeSourceFileNamePartsToProperties() : bool{
        $sourceFileFullName = $_FILES['image']['name'];
        $sourceFileFullName = trim($sourceFileFullName);
        $sourceFileFullName = trim($sourceFileFullName, '.');
        $nameParts = explode('.', $sourceFileFullName);
        $namePartsCount = count(($nameParts));
        if ($namePartsCount > 2) {
            $this->sourceFileExtensionPart = $nameParts[$namePartsCount - 1];
            $this->sourceFileNamePart = implode('.', array_slice($nameParts, 0, $namePartsCount - 1));
            return true;
        }
        if ($namePartsCount === 2) {
            list($this->sourceFileNamePart, $this->sourceFileExtensionPart) = $nameParts;
            return true;
        }
        if ($namePartsCount === 1)  {
            $this->sourceFileNamePart = $nameParts[0];
            return true;
        }
        return false;
    }

    public function getUploadedImagePath() : string
    {
        return $_FILES['image']['tmp_name'];
    }

    public function getArchiveName(string $tmpName, string $realName) : string
    {
        return md5($tmpName . $realName);
    }

    public function getThumbnailsArchive(): string
    {
        $archiveName = $this->getArchiveName($this->getUploadedImagePath(), $this->sourceFileNamePart);
        $archivePath = $this->thumbnailsArchivePath . $archiveName;
        $zip = new ZipArchive();
        if (!$zip->open($archivePath, ZipArchive::CREATE)) {
            throw new Exception("Can't write new archive file");
        }
        $archivedThumbnails = [];
        $thumbnailNameBase = $this->sourceFileNamePart;
        foreach ($this->thumbnailSizes as list($rows,$columns)) {
            $thumbnailNameAddition = $rows . '_' . $columns . '.' . $this->sourceFileExtensionPart;
            $thumbnailPath = $this->getThumbnail($columns, $rows, $this->getUploadedImagePath());
            $thumbnailFileName = $thumbnailNameBase . '_' . $thumbnailNameAddition;
            $zip->addFile($thumbnailPath, $thumbnailFileName);
            array_push($archivedThumbnails, $thumbnailPath);
        }
        $zip->close();
        $this->deleteProcessedThumbnailFiles($archivedThumbnails);
        return $archiveName;
    }

    public function getThumbnail(int $columns, int $rows, string $sourcePath): string
    {
        $resultPath = $sourcePath . '_' . $columns . '_' . $rows;
        $thumbnail = new Imagick($sourcePath);
        $thumbnail->thumbnailImage($columns, $rows);
        $thumbnail->writeImage($resultPath);
        $thumbnail->clear();
        return $resultPath;
    }

    private function deleteProcessedThumbnailFiles(array $archivedThumbnails) : void
    {
        foreach ($archivedThumbnails as $file) {
            if (!unlink($file)) {
                throw new Exception("Can't delete processed archived thumbnail file");
            }
        }
    }
}