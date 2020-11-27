<?php

declare(strict_types=1);

namespace App\Application\Controller\Thumbnails;

use App\Application\Controller\Controller;
use App\Application\Thumbnails\Exceptions\ThumbnailsBadRequestException;
use App\Application\Thumbnails\ThumbnailsArchive;
use App\Application\Thumbnails\ThumbnailsSource;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostThumbnailsArchive extends Controller
{
    public function action(ServerRequestInterface $request, 
                        ResponseInterface $response
                        ): ResponseInterface
    {
        $thumbnailsArchive = new ThumbnailsArchive();
        return $this->respondWithData($thumbnailsArchive);
    }


}