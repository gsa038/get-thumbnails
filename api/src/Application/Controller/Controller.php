<?php
declare(strict_types=1);

namespace App\Application\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

abstract class Controller
{
    protected LoggerInterface $logger;

    protected Request $request;

    protected Response $response;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->request = $request;
        $this->response = $response;

        try {
            return $this->action($request, $response);
        } catch (Exception $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());
        }
    }

    abstract protected function action(Request $request, Response $response): Response;

        /**
     * @param  array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ControllerPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function respond(ControllerPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
}
