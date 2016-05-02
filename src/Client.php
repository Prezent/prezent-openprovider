<?php

namespace Prezent\OpenProvider;

use Http\Client\HttpClient;
use Prezent\OpenProvider\Exception\RequestException;
use Prezent\OpenProvider\Exception\ResponseException;

/**
 * OpenProvider API client
 *
 * @author Sander Marechal
 */
class Client
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Call an API method
     *
     * @param string $method
     * @param array $data
     * @return SimpleXMLElement
     */
    public function call($method, array $data = [])
    {
        if (substr($method, -7) !== 'Request') {
            $method .= 'Request';
        }

        $body = $this->createXml($method, $data);

        try {
            $response = $this->config->getHttpClient()->post($this->config->getUri(), [], $body);
            $xml = new \SimpleXMLElement($response->getBody());
        } catch (\Exception $e) {
            throw new RequestException('Server did not produce a response', 0, $e);
        }

        if ($xml->reply->code != 0) {
            throw new ResponseException('Server response error: ' . $xml->reply->desc, (int) $xml->reply->code);
        }

        return $xml->reply->data;
    }

    /**
     * Magic __call support
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public function __call($method, $args)
    {
        array_unshift($args, $method);
        return call_user_func_array([$this, 'call'], $args);
    }

    /**
     * Create an XML request document
     *
     * @param string $method
     * @param array $data
     * @return RequestInterface
     */
    private function createXml($method, array $data = [])
    {
        $dom = new \SimpleXMLElement('<openXML />');

        $creds = $dom->addChild('credentials');
        $creds->addChild('username', $this->config->getUsername());
        $creds->addChild('password', $this->config->getPassword());
        $creds->addChild('hash', $this->config->getHash());

        $request = $dom->addChild($method);
        $this->serialize($request, $data);

        return $dom->asXML();
    }

    /**
     * Serialize to XML
     *
     * @param \SimpleXMLElement $node
     * @param array $data
     * @return void
     */
    private function serialize(\SimpleXMLElement $node, $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $numeric = count(array_filter(array_keys($value), 'is_string')) == 0;

                if ($numeric) {
                    foreach ($value as $item) {
                        if (!is_array($item)) {
                            throw new \Exception('Not a nested array');
                        }

                        $this->serialize($node->addChild($key), $item);
                    }
                } else {
                    $this->serialize($node->addChild($key), $value);
                }
            } else {
                $node->addChild($key, (string) $value);
            }
        }
    }
}
