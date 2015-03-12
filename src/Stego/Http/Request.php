<?php

namespace Stego\Http;

class Request
{
    const TYPE_GET = 'GET';
    const TYPE_PUT = 'PUT';
    const TYPE_POST = 'POST';
    const TYPE_HEAD = 'HEAD';
    const TYPE_DELETE = 'DELETE';
    const TYPE_OPTIONS = 'OPTIONS';
    const TYPE_TRACE = 'TRACE';
    const TYPE_CONNECT = 'CONNECT';

    private $userAgent = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7';

    protected $url;
    protected $type;
    protected $params;
    protected $headers;

    protected $stream;
    protected $showProgress;

    public function __construct($url, $type = self::TYPE_GET, $params = array(), $headers = array())
    {
        $this->url = $url;
        $this->type = $type;
        $this->params = $params;
        $this->headers = $headers;
    }

    public function getResponse()
    {
        $response = new Response();

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        // post setup
        //curl_setopt($curl, CURLOPT_POST, true);
        //curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch,CURLOPT_USERAGENT, $this->getUserAgent());
        // if the request uses some stream for the response
        if ($stream = $this->getStream()) {
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $str) use ($stream) {
                $len = fwrite($stream, $str);
                return $len;
            });
            $response->setBody($stream);
        }

        if ($this->getShowProgress()) {
            $progressBar = new Progress();
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($rsc, $dSize, $dld, $uSize, $uld) use ($progressBar) {
                if($dSize > 0) {
                    $current = sprintf('%.2f', 100 * ($dld / $dSize));
                    $progressBar->show($current, 100);
                }
            });
        }

        $response = curl_exec($ch);

        // get headers
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);


        curl_close($ch);
    }

    public function getShowProgress()
    {
        return $this->showProgress;
    }

    public function setShowProgress($showProgress)
    {
        $this->showProgress = $showProgress;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function setStream($resource)
    {
        if (!is_resource($resource)) {
            throw new \RuntimeException('The received parameter is not a valid resource');
        }
        $this->stream = $resource;
    }

    private function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    private function getHeaders()
    {
        return $this->headers;
    }
}
