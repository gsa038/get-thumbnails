<?php

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails;

use App\Application\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;

class GetThumbnailsArchive extends Controller
{
    public function __invoke(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        if (!file_exists($this->thumbnailsArchivePath . $request->getAttribute('archive'))) {
            $message = 'Not found';
            $messageDescription = 'Archive not found';
            $status = 404;
            $response->getBody()->write(json_encode([
                'message' => $message, 
                'statusCode' => "$status", 
                'description' => $messageDescription, 
                'archive' => $this->archive
            ]));
            return $response->withStatus($status, $message);
        }
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
        return $response->withStatus($status, $message);
    }   
}