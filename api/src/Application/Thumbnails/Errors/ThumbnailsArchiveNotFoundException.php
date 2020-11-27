<?php
declare(strict_types=1);

namespace App\Application\Thumbnails\Errors;

use Slim\Exception\HttpNotFoundException;

class ThumbnailsArchiveNotFoundException extends HttpNotFoundException
{
    public function __construct() {
        $this->message = 'Requested archive not found.';
    }
}