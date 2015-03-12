<?php

namespace Stego\Http;

class Response
{
    /** @var array[] */
    protected $headers;
    /** @var int */
    protected $statusCode;
    /** @var string|resource */
    protected $body;

    /**
     * @return \array[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param \array[] $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return resource|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param resource|string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
