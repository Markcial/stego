<?php

namespace Stego\Console\Stdio;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    protected $output;
    protected $stdout;
    protected $stderr;

    protected function setUp()
    {
        $this->stdout = fopen('php://memory', 'rw');
        $this->stderr = fopen('php://memory', 'rw');

        $this->output = \Mockery::mock('\Stego\Console\Stdio\Console');
        $this->output->makePartial();
        $this->output->shouldAllowMockingProtectedMethods();
        $this->output->shouldReceive('getStdOut')->andReturn($this->stdout);
        $this->output->shouldReceive('getStdErr')->andReturn($this->stderr);

        parent::setUp();
    }

    public function outputMessagesDataProvider()
    {
        return array(
            array('foo' . "\n", 'foo'),
            //DEBUG self::COMMENT, self::WARNING, self::ERROR, self::FATAL
            array(Console::C_CYAN . "some message" . Console::C_RESET . "\n", '%[debug]some message'),
            //INFO
            array(Console::C_BPURPLE . "some message" . Console::C_RESET . "\n", '%[info]some message'),
            //COMMENT
            array(Console::C_WHITE . Console::C_ON_BLUE . "some message" . Console::C_RESET . "\n", '%[comment]some message'),
            //WARNING
            array(Console::C_BYELLOW . "some message" . Console::C_RESET . "\n", '%[warning]some message', 1),
            //ERROR
            array(Console::C_BRED . "some message" . Console::C_RESET . "\n", '%[error]some message', 1),
            //FATAL
            array(Console::C_WHITE . Console::C_UWHITE . Console::C_ON_RED . 'some message' . Console::C_RESET . "\n", '%[fatal]some message', 1),
            // formatter out of place
            array("asd a%[asdadd ]\n", 'asd a%[asdadd ]'),
            // utf-8 chars
            array("ñ`ó äÂÊ ç € åå∫∂" . "\n", "ñ`ó äÂÊ ç € åå∫∂"),
            // utf-8 chars with formatting
            array(Console::C_WHITE . Console::C_UWHITE . Console::C_ON_RED . "ñ`ó äÂÊ ç € åå∫∂" . Console::C_RESET . "\n", "%[fatal]ñ`ó äÂÊ ç € åå∫∂", 1),
            // unicode chars
            array("🐒 🐉 🐲 🐊 🐍 🐢 🐸 🐋 🐳 🐬 🐙 🐟 🐠 🐡 🐚 " . "\n", "🐒 🐉 🐲 🐊 🐍 🐢 🐸 🐋 🐳 🐬 🐙 🐟 🐠 🐡 🐚 "),
            // unicode chars with formatting
            array(Console::C_CYAN . "🍔 🍕 🍖 🍗 🍘 🍙 🍚" . Console::C_RESET . "\n", '%[debug]🍔 🍕 🍖 🍗 🍘 🍙 🍚'),
        );
    }

    /**
     * @param $expect
     * @param $message
     * @dataProvider outputMessagesDataProvider
     */
    public function testOutputMessages($expect, $message, $severity = 0)
    {
        $this->output->write($message);
        if ($severity === 0) {
            rewind($this->stdout);
            $this->assertEquals($expect, fgets($this->stdout));
        } else {
            rewind($this->stderr);
            $this->assertEquals($expect, fgets($this->stderr));
        }
    }

    public function testNl()
    {
        $this->output->nl();
        rewind($this->stdout);
        $this->assertEquals(PHP_EOL, fgets($this->stdout));
    }

    public function testClear()
    {
        $this->output->clear();
        rewind($this->stdout);
        $this->assertEquals("\r", fgets($this->stdout));
    }

    public function testOutputResources()
    {
        $this->markTestSkipped('revisit, needed to test stdout and stderr');
        $output = new Console();
        $output->out('foo');
        $output->err('bar');
    }

    public function testResources()
    {
        $console = new Console();
        $reflection = new \ReflectionClass($console);
        foreach (array('getStdin', 'getStdout', 'getStderr') as $method) {
            $refl = $reflection->getMethod($method);
            $refl->setAccessible(true);
            $resource = $refl->invoke($console);
            $this->assertTrue(is_resource($resource));
        }
    }

    public function testInput()
    {
        /*
        $input = new Input();
        ob_start(function ($text) {
            var_dump($text);die;
        }, 2);
        $input->readline();
*/
    }
}
