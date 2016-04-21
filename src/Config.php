<?php

namespace Prezent\OpenProvider;

use Http\Client\Common\HttpMethodsClient;

/**
 * API client configuration
 *
 * @author Sander Marechal
 */
class Config
{
    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $hash;

    /**
     * Get httpClient
     *
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
    
    /**
     * Set httpClient
     *
     * @param HttpMethodsClient $httpClient
     * @return self
     */
    public function setHttpClient(HttpMethodsClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Set uri
     *
     * @param string $uri
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set username
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Set password
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * Set hash
     *
     * @param string $hash
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }
}
