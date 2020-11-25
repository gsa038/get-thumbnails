<?php
declare(strict_types=1);

namespace App\Application\ResponseEmitter;

use Psr\Http\Message\ResponseInterface;
use Slim\ResponseEmitter as SlimResponseEmitter;

class ResponseEmitter extends SlimResponseEmitter
{
    public function emit(ResponseInterface $response): void
    {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', 'http://client.localhost:8880')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS, POST')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

        if (ob_get_contents()) {
            ob_clean();
        }

        parent::emit($response);
    }
}
