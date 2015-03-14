<?php

namespace Stego\Console\Commands\Stdio;

/**
 * @method write
 * @method getCommand
 * @method getArgs
 */
class IOTerm
{
    /** @var Input */
    protected $input;
    /** @var Output */
    protected $output;

    /**
     * @param Input  $input
     * @param Output $output
     */
    public function __construct(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param $name
     * @param $args
     */
    public function __call($name, $args)
    {
        if (method_exists($this->input, $name)) {
            return call_user_func_array(array($this->input, $name), $args);
        }

        if (method_exists($this->output, $name)) {
            return call_user_func_array(array($this->output, $name), $args);
        }

        throw new \RuntimeException(sprintf('Method named %s not found.', $name));
    }
}
