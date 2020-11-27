<?php
declare(strict_types=1);

namespace App\Application\Thumbnails\Errors;

use Slim\Exception\HttpBadRequestException;

class ThumbnailsArchiveNotFoundException extends HttpBadRequestException
{
    public function __construct() {
        $this->message = 'Requested archive not found.';
    }
}