<?php

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails;

use App\Application\Controller\Controller;
use App\Application\Thumbnails\ThumbnailsArchive;
use App\Application\Thumbnails\ThumbnailsSource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostThumbnailsArchive extends Controller
{
    public function __invoke(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        $sourceImage = new ThumbnailsSource('image');
        if (!$sourceImage->getIsValid()) {
            $archivePath = null;
            $message = 'Bad Request';
            $messageDescription = 'Wrong input file';
            $status = 500;
            return $response->withStatus($status, $message);
        }
        $thumbnailsArchive = new ThumbnailsArchive();
        $archivePath = $thumbnailsArchive->getArchivepath();
        $status = 200;
        $message = 'OK';
        $messageDescription = '';
        $response->getBody()->write(json_encode([
            'message' => $message, 
            'statusCode' => $status, 
            'description' => $messageDescription, 
            'archive' => $archivePath
        ]));
        return $response->withStatus($status, $message);
    }


}