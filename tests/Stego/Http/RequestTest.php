<?php

namespace Stego\Http;


class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $request = new Request('http://www.google.com');
    }
} 