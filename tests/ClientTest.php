<?php

namespace Prezent\OpenProvider\Tests;

use Prezent\OpenProvider\Client;
use Prezent\OpenProvider\Config;
use Prezent\OpenProvider\Tests\PHPUnit\MockClientTest;

class ClientTest extends MockClientTest
{
    public function testAuthWithPassword()
    {
        $config = new Config();
        $config->setHttpClient($this->createMockHttpClient($this->createResponse()));
        $config->setUri('http://api.example.org');
        $config->setUsername('user');
        $config->setPassword('pass');

        $client = new Client($config);
        $client->call('searchDomain');

        $request = $this->getLastRequest();

        $this->assertEquals('user', (string) $request->credentials->username);
        $this->assertEquals('pass', (string) $request->credentials->password);
        $this->assertEmpty((string) $request->credentials->hash);
    }

    public function testAuthWithHash()
    {
        $config = new Config();
        $config->setHttpClient($this->createMockHttpClient($this->createResponse()));
        $config->setUri('http://api.example.org');
        $config->setUsername('user');
        $config->setHash($hash = md5('pass'));

        $client = new Client($config);
        $client->call('searchDomain');

        $request = $this->getLastRequest();

        $this->assertEquals('user', (string) $request->credentials->username);
        $this->assertEquals($hash, (string) $request->credentials->hash);
        $this->assertEmpty((string) $request->credentials->password);
    }

    public function testCallMethodName()
    {
        $client = $this->createMockClient();
        $client->call('searchDomainRequest');

        $request = $this->getLastRequest();

        $this->assertCount(1, $request->xpath('searchDomainRequest'));
    }

    public function testCallShortMethodName()
    {
        $client = $this->createMockClient();
        $client->call('searchDomain');

        $request = $this->getLastRequest();

        $this->assertCount(1, $request->xpath('searchDomainRequest'));
    }

    public function testMagicCall()
    {
        $client = $this->createMockClient();
        $client->searchDomain();

        $request = $this->getLastRequest();

        $this->assertCount(1, $request->xpath('searchDomainRequest'));
    }

    public function testSimpleArgs()
    {
        $client = $this->createMockClient();
        $client->searchDomain([
            'limit' => 10,
            'offset' => 20,
        ]);

        $request = $this->getLastRequest();

        $this->assertEquals(10, (int) $request->searchDomainRequest->limit);
        $this->assertEquals(20, (int) $request->searchDomainRequest->offset);
    }

    public function testComplexArgs()
    {
        $client = $this->createMockClient();
        $client->createCustomer(['name' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]]);

        $request = $this->getLastRequest();

        $this->assertEquals(2, count($request->createCustomerRequest->name->children()));
        $this->assertEquals('John', (string) $request->createCustomerRequest->name->firstName);
        $this->assertEquals('Doe', (string) $request->createCustomerRequest->name->lastName);
    }

    public function testNestedArgs()
    {
        $client = $this->createMockClient();
        $client->createCustomer(['name' => [
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
            [
                'firstName' => 'Mary',
                'lastName' => 'Jane',
            ],
        ]]);

        $request = $this->getLastRequest();

        $this->assertEquals(2, count($request->createCustomerRequest->name->array->item));
        $this->assertEquals('John', (string) $request->createCustomerRequest->name->array->item[0]->firstName);
        $this->assertEquals('Doe', (string) $request->createCustomerRequest->name->array->item[0]->lastName);
        $this->assertEquals('Mary', (string) $request->createCustomerRequest->name->array->item[1]->firstName);
        $this->assertEquals('Jane', (string) $request->createCustomerRequest->name->array->item[1]->lastName);
    }
}
