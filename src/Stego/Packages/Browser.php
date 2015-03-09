<?php

namespace Stego\Packages;

use Guzzle\Service\Client;

class Browser
{
    const PACKAGIST_SEARCH_URI = 'https://packagist.org/search.json?q=%s';
    const PACKAGIST_DETAILS_URI = 'https://packagist.org/packages/%s.json';

    public function find($name, $page = null)
    {
        $uri = sprintf(self::PACKAGIST_SEARCH_URI, $name);
        if (!is_null($page)) {
            $uri .= '&page=' . $page;
        }
        $client = new Client();
        $request = $client->get($uri);
        $response = $request->send();
        if ($response->getStatusCode() !== 200) {
            throw new \HttpException('Http error');
        }

        return $response->json();
    }

    public function versionDetails($name, $version = 'dev-master')
    {
        $data = $this->details($name, $version);

        $versions = $data['package']['versions'];

        if (!array_key_exists($version, $versions)) {
            throw new \Exception('Version not found');
        }

        return $data['package']['versions'][$version];
    }

    public function details($name)
    {
        $uri = sprintf(self::PACKAGIST_DETAILS_URI, $name);

        $client = new Client();
        $request = $client->get($uri);
        $response = $request->send();
        if ($response->getStatusCode() !== 200) {
            throw new \HttpException('Http error');
        }

        return $response->json();
    }
}
