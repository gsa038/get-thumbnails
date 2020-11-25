<?php
declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Controller
{
    protected LoggerInterface $logger;

    protected Request $request;

    protected Response $response;

    protected array $args;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    abstract public function __invoke(Request $request, Response $response, $args): Response;
}
