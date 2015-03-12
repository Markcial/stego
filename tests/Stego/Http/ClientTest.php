<?php

namespace Stego\Http;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testVerbs()
    {
        $url = 'http://ftp.free.org/mirrors/releases.ubuntu-fr.org/11.04/ubuntu-11.04-desktop-i386-fr.iso';
        $request = Client::get($url);
        //$stream = fopen('foo.txt', 'w');
        //$request->setStream($stream);
        $request->setShowProgress(true);
        $request->getResponse();


    }
} 