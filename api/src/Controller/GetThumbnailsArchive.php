<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\utils\ThumbnailsRemover as UtilsThumbnailsRemover;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;

class GetThumbnailsArchive
{
    private string $thumbnailsArchivePath = '/var/www/thumbnails/';
    private string $archive = '';

    // public function __destruct()
    // {
    //     if ($this->archive !== '') {
    //         $file = $this->thumbnailsArchivePath . $this->archive;
    //         if (file_exists($file)) {
    //             unlink($file);
    //         }
    //     }
    // }
    

    public function __invoke(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        UtilsThumbnailsRemover::deleteExpiredThumbnails();
        if (file_exists($this->thumbnailsArchivePath . $request->getAttribute('archive'))) {
            $this->archive = $request->getAttribute('archive');
            $archivePath = $this->thumbnailsArchivePath . $this->archive;
            $status = 200;
            $message = 'OK';
            $fileHandler = fopen($archivePath, 'rb');
            $stream = new Stream($fileHandler);
            $response = $response->withBody($stream);
            $response = $response->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Disposition', 'attachment; filename="' .basename("$this->archive.zip") . '"')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate')
                ->withHeader('Pragma', 'public')
                ->withHeader('Content-Length', filesize($archivePath));
        } else {
            $message = 'Not found';
            $messageDescription = 'Archive not found';
            $status = 404;
            $response->getBody()->write(json_encode([
                'message' => $message, 
                'statusCode' => "$status", 
                'description' => $messageDescription, 
                'archive' => $this->archive
            ]));
            }
        $response->withStatus($status, $message);
        ob_clean();
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://client.localhost:8880')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS, POST')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');
    }   
}