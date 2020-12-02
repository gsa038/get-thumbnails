<?php

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails;

use App\Application\Controller\Controller;
use App\Application\Thumbnails\Exceptions\ThumbnailsInternalServerErrorException;
use App\Application\Thumbnails\ThumbnailsArchive;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;

class GetThumbnailsArchive extends Controller
{
    public function action(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        $archiveName = $request->getAttribute('archive');
        if (!file_exists(ThumbnailsArchive::THUMBNAILS_ARCHIVE_PATH . $archiveName)) {
            throw new ThumbnailsInternalServerErrorException('Requested archive was deleted');
        }
        $archivePath = ThumbnailsArchive::THUMBNAILS_ARCHIVE_PATH . $archiveName;
        $fileHandler = fopen($archivePath, 'rb');
        $stream = new Stream($fileHandler);
        $response = $response->withBody($stream);
        $response = $response->withHeader('Content-Type', 'application/zip')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Disposition', 'attachment; filename="' .basename("$archiveName.zip") . '"')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', filesize($archivePath));
        return $response;
    }   
}