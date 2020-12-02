<?php
declare(strict_types=1);

namespace App\Application\Thumbnails\Exceptions;

use Slim\Exception\HttpInternalServerErrorException;

class ThumbnailsInternalServerErrorException extends HttpInternalServerErrorException
{
    public function __construct(string $message = null) {
        if ($message !== null) {
            $this->message = $message;
        }
    }
}