<?php
declare(strict_types=1);

namespace App\Application\Thumbnails\Exceptions;

use Slim\Exception\HttpBadRequestException;

class ThumbnailsBadRequestException extends HttpBadRequestException
{
    public function __construct(string $message = null) {
        if ($message !== null) {
            $this->message = $message;
        }
    }
}