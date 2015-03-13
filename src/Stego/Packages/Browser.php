<?php

namespace Stego\Packages;

use Guzzle\Service\Client;
use Guzzle\Stream\Stream;

class Browser
{
    const PACKAGIST_SEARCH_URI = 'https://packagist.org/search.json?q=%s';
    const PACKAGIST_DETAILS_URI = 'https://packagist.org/packages/%s.json';
    /** @var Client */
    protected $client;

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * @param $name
     * @param null $page
     *
     * @return array|bool|float|int|string
     *
     * @throws \HttpException
     */
    public function find($name, $page = null)
    {
        $uri = sprintf(self::PACKAGIST_SEARCH_URI, $name);
        if (!is_null($page)) {
            $uri .= '&page=' . $page;
        }

        $request = $this->getClient()->get($uri);
        $response = $request->send();
        if ($response->getStatusCode() !== 200) {
            throw new \HttpException('Http error');
        }

        return $response->json();
    }

    /**
     * @param $name
     * @param string $version
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws \HttpException
     */
    public function versionDetails($name, $version = 'dev-master')
    {
        $data = $this->details($name, $version);

        $versions = $data['package']['versions'];

        if (!array_key_exists($version, $versions)) {
            throw new \Exception('Version not found');
        }

        return $data['package']['versions'][$version];
    }

    /**
     * @param $name
     *
     * @return array|bool|float|int|string
     *
     * @throws \HttpException
     */
    public function details($name)
    {
        $uri = sprintf(self::PACKAGIST_DETAILS_URI, $name);

        $request = $this->getClient()->get($uri);
        $response = $request->send();
        if ($response->getStatusCode() !== 200) {
            throw new \HttpException('Http error');
        }

        return $response->json();
    }

    /**
     * @param $name
     * @param string $version
     *
     * @return Stream
     *
     * @throws \Exception
     * @throws \HttpException
     */
    public function getZipStream($name, $version = 'dev-master')
    {
        $data = $this->versionDetails($name, $version);

        if (!array_key_exists('dist', $data) || !array_key_exists('url', $data['dist'])) {
            throw new \HttpException('Downloadable zip not found.');
        }

        $stream = tempnam(sys_get_temp_dir(), 'stego');
        $request = $this->getClient()->get($data['dist']['url']);
        $response = $request->setResponseBody($stream)->send();

        if ($response->getStatusCode() !== 200) {
            throw new \HttpException('Http error');
        }

        return $stream;
    }
}
