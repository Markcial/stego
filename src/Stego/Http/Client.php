<?php

namespace Stego\Http;

/**
 * @method static get
 * @method static put
 * @method static post
 * @method static head
 * @method static delete
 * @method static options
 * @method static trace
 * @method static connect
 */
class Client
{
    protected static $verbs = array(
        Request::TYPE_GET, Request::TYPE_PUT, Request::TYPE_POST, Request::TYPE_HEAD, Request::TYPE_DELETE,
        Request::TYPE_OPTIONS, Request::TYPE_TRACE, Request::TYPE_CONNECT,
    );

    public static function __callStatic($name, $args = array())
    {
        if (in_array($name, array_map('strtolower', self::$verbs))) {
            array_splice($args, 1, count($args), strtoupper($name));
            $class = new \ReflectionClass('Stego\Http\Request');
            return $class->newInstanceArgs($args);
        }

        echo 'method not found';
        die;
    }
}
