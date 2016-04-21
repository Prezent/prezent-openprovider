<?php

namespace Prezent\OpenProvider;

use Http\Client\HttpClient;

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
        $response = $this->config->getHttpClient()->post($this->config->getUri(), [], $body);

        return new \SimpleXMLElement($response->getBody());
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
    public function serialize(\SimpleXMLElement $node, $data)
    {

        // ['foo' => [
        //     ['bar' => 'one'],
        //     ['bar' => 'two'],
        // ]]
        //
        // <foo><bar>one</bar></foo>
        // <foo><bar>two</bar></foo>

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
