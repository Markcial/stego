<?php

namespace Stego\Tasks\Fs;

use Stego\Packages\Compiler;
use Stego\Tasks\Task;

class PharTask
{
    use Task;
    /** @var Compiler */
    protected $compiler;

    protected function init()
    {
        $this->setRequired(array('source', 'destination'));
    }

    /**
     * @return Compiler
     */
    private function getCompiler()
    {
        if (is_null($this->compiler)) {
            $this->compiler = $this->getContainer()->get('compiler');
        }

        return $this->compiler;
    }

    protected function doTask()
    {
        $source = $this->getParam('source');
        $destination = $this->getParam('destination');
        $bootstrap = $this->getParam('bootstrap');
        $metadata = $this->getParam('metadata');

        set_error_handler(function ($code, $message) {
            $this->out('%[warning]' . $message);
        });

        $this->getCompiler()->compile($destination, $source, $bootstrap, $metadata);

        return 0;
    }
}
