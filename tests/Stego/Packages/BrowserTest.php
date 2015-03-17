<?php

namespace Stego\Packages;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    public function testFindMethod()
    {
        $data = '{"foo":"bar"}';
        $name = 'foo/bar';
        $url = sprintf(Browser::PACKAGIST_SEARCH_URI, $name);
        /** @var Browser|\PHPUnit_Framework_MockObject_MockObject $browser */
        $browser = $this->getMockBuilder('\Stego\Packages\Browser')->setMethods(array('doRequest'))->getMock();
        $browser->expects($this->atLeast(1))
            ->method('doRequest')
            ->with($url)
            ->willReturn($data);
        $result = $browser->find($name);
        $this->assertEquals(json_decode($data, true), $result);

        /** @var Browser|\PHPUnit_Framework_MockObject_MockObject $browser */
        $browser = $this->getMockBuilder('\Stego\Packages\Browser')->setMethods(array('doRequest'))->getMock();
        $browser->expects($this->atLeast(1))
            ->method('doRequest')
            ->with($url . '&page=2')
            ->willReturn($data);

        $result = $browser->find($name, 2);
        $this->assertEquals(json_decode($data, true), $result);
    }

    public function testDetailsMethod()
    {
        $data = '{"foo":"bar"}';
        $name = 'foo/bar';
        $url = sprintf(Browser::PACKAGIST_DETAILS_URI, $name);
        /** @var Browser|\PHPUnit_Framework_MockObject_MockObject $browser */
        $browser = $this->getMockBuilder('\Stego\Packages\Browser')->setMethods(array('doRequest'))->getMock();
        $browser->expects($this->atLeast(1))
            ->method('doRequest')
            ->with($url)
            ->willReturn($data);
        $result = $browser->details($name);
        $this->assertEquals(json_decode($data, true), $result);
    }

    public function testVersionDetailsMethod()
    {
        $data = '{"package": {"versions" : {"1": "holla", "2" :"whatup"}}}';
        $name = 'foo/bar';
        $url = sprintf(Browser::PACKAGIST_DETAILS_URI, $name);
        /** @var Browser|\PHPUnit_Framework_MockObject_MockObject $browser */
        $browser = $this->getMockBuilder('\Stego\Packages\Browser')->setMethods(array('doRequest'))->getMock();
        $browser->expects($this->atLeast(1))
            ->method('doRequest')
            ->with($url)
            ->willReturn($data);
        $result = $browser->versionDetails($name);
        $this->assertEquals("holla", $result);

        $result = $browser->versionDetails($name, 2);
        $this->assertEquals("whatup", $result);

        try {
            $result = $browser->versionDetails($name, 10);
        } catch (\Exception $e) {
            $this->assertEquals('Version not found, avaliable versions are : 1, 2.', $e->getMessage());
        }
    }

    public function testDoRequest()
    {
        $text = 'some text for testing';
        $tmpfile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpfile, $text);

        $browser = new Browser();

        $method = new \ReflectionMethod($browser, 'doRequest');
        $method->setAccessible(true);
        $result = $method->invokeArgs($browser, array($tmpfile));
        $this->assertSame($text, $result);
        unlink($tmpfile);
    }
}
