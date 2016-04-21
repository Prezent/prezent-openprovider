<?php

namespace Prezent\OpenProvider\Tests\PHPUnit;

use GuzzleHttp\Psr7\Response;
use Http\Client\Common\HttpMethodsClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Mock\Client as MockClient;
use Prezent\OpenProvider\Client;
use Prezent\OpenProvider\Config;

abstract class MockClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockClient
     */
    private $client;

    /**
     * Create a mock API client
     *
     * @param ResponseInterface|array $responses
     * @param \Exception|array $exceptions
     * @return HttpMethodsClient
     */
    public function createMockClient($responses = [], $exceptions = [])
    {
        $config = new Config();
        $config->setHttpClient($this->createMockHttpClient($responses, $exceptions));
        $config->setUri('http://api.example.org');
        $config->setUsername('user');
        $config->setHash(md5('pass'));

        return new Client($config);
    }

    /**
     * Create a mock HttpMethodsClient
     *
     * @param ResponseInterface|array $responses
     * @param \Exception|array $exceptions
     * @return HttpMethodsClient
     */
    protected function createMockHttpClient($responses = [], $exceptions = [])
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }

        if (empty($responses)) {
            $responses[] = $this->createResponse();
        }

        if (!is_array($exceptions)) {
            $exceptions = [$exceptions];
        }

        $factory = new GuzzleMessageFactory();
        $this->client = new MockClient($factory);

        foreach ($responses as $response) {
            $this->client->addResponse($response);
        }

        foreach ($exceptions as $exception) {
            $this->client->addexception($exception);
        }

        return new HttpMethodsClient($this->client, $factory);
    }

    /**
     * Create a PSR7 response
     *
     * @param string $data XML response data
     * @param int $status
     * @param int $code
     * @return Response
     */
    protected function createResponse($data = '', $status = 200, $code = 0)
    {
        return new Response($status, [], '<openXML><reply><code>' . $code . '</code><desc></desc><data>' . $data . '</data></reply></openXML>');
    }

    /**
     * Return the XML body of the last request
     *
     * @return \SimpleXMLElement
     */
    protected function getLastRequest()
    {
        return new \SimpleXMLElement(array_pop($this->client->getRequests())->getBody());
    }
}
