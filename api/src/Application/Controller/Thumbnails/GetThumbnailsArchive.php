<?php

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails;

use App\Application\Controller\Controller;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;

class GetThumbnailsArchive extends Controller
{
    public function action(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        if (!file_exists($this->thumbnailsArchivePath . $request->getAttribute('archive'))) {
            throw new Exception('Archive not found');
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