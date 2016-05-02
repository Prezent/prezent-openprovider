<?php

namespace Prezent\OpenProvider\Tests;

use Http\Adapter\Guzzle6\Client as GuzzleClient;
use Http\Client\Common\HttpMethodsClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Prezent\OpenProvider\Client;
use Prezent\OpenProvider\Config;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp()
    {
        if (!$GLOBALS['OP_API_URI'] || !$GLOBALS['OP_API_USERNAME'] || !$GLOBALS['OP_API_PASSWORD']) {
            $this->markTestSkipped();
        }

        $httpClient = new HttpMethodsClient(new GuzzleClient(), new GuzzleMessageFactory());

        $config = new Config();
        $config->setHttpClient($httpClient);
        $config->setUri($GLOBALS['OP_API_URI']);
        $config->setUsername($GLOBALS['OP_API_USERNAME']);
        $config->setPassword($GLOBALS['OP_API_PASSWORD']);

        $this->client = new Client($config);
    }

    public function testRetrieveExtension()
    {
        $response = $this->client->retrieveExtension([
            'name' => 'com',
        ]);

        $this->assertEquals('com', $response->name);
    }

    /**
     * @expectedException Prezent\OpenProvider\Exception\ResponseException
     */
    public function testRetrieveInvalidExtension()
    {
        $response = $this->client->retrieveExtension([
            'name' => 'invalid',
        ]);
    }
}
