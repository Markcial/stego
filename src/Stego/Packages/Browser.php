<?php

namespace Stego\Packages;

class Browser
{
    const REQUEST_GET = 'GET';
    const REQUEST_POST = 'POST';

    const PACKAGIST_SEARCH_URI = 'https://packagist.org/search.json?q=%s';
    const PACKAGIST_DETAILS_URI = 'https://packagist.org/packages/%s.json';

    protected function getContext($type)
    {
        return stream_context_create(
            array(
                "http" => array(
                    "method"  => $type,
                    "timeout" => 20,
                    "header"  => "User-agent: Stego package manager",
                ),
            )
        );
    }

    protected function doRequest($url, $type = self::REQUEST_GET)
    {
        return file_get_contents($url, false, $this->getContext($type));
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

        $response = $this->doRequest($uri);

        return json_decode($response, true);
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
    public function versionDetails($name, $version = null)
    {
        $data = $this->details($name, $version);

        $versions = $data['package']['versions'];

        if (is_null($version)) {
            // return the newest version
            return $data['package']['versions'][key($versions)];
        }

        if (!array_key_exists($version, $versions)) {
            throw new \Exception(
                sprintf(
                    'Version not found, avaliable versions are : %s.',
                    implode(", ", array_keys($versions))
                )
            );
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

        $response = $this->doRequest($uri);

        return json_decode($response, true);
    }
}
