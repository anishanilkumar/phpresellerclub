<?php

declare(strict_types=1);

namespace Resellerclub\Tests\Support;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Resellerclub\Config\Credentials;
use Resellerclub\Config\Environment;
use Resellerclub\Http\ApiClient;
use Resellerclub\Http\RequestBuilder;

/**
 * Builds an {@see ApiClient} backed by a Guzzle {@see MockHandler} so tests can
 * queue responses and assert on the request that was actually built and sent.
 */
final class MockTransport
{
    public readonly ApiClient $apiClient;

    private MockHandler $mock;

    public function __construct(
        Environment $environment = Environment::Test,
        Credentials $credentials = new Credentials('AUTHUSER', 'APIKEY'),
    ) {
        $this->mock = new MockHandler();
        $stack = HandlerStack::create($this->mock);

        $factory = new HttpFactory();
        $this->apiClient = new ApiClient(
            new GuzzleClient(['handler' => $stack]),
            $factory,
            $factory,
            $credentials,
            new RequestBuilder($environment),
        );
    }

    /**
     * Queue a JSON response the next request will receive.
     */
    public function willReturn(string $json = '{"status":"ok"}', int $status = 200): void
    {
        $this->mock->append(new Response($status, ['Content-Type' => 'application/json'], $json));
    }

    /**
     * Queue a transport-level failure for the next request.
     */
    public function willThrow(\Throwable $e): void
    {
        $this->mock->append($e);
    }

    public function lastRequest(): RequestInterface
    {
        $request = $this->mock->getLastRequest();
        \PHPUnit\Framework\Assert::assertNotNull($request, 'No request was sent.');

        return $request;
    }
}
