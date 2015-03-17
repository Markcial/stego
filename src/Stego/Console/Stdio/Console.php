<?php

namespace Stego\Console\Stdio;

class Console
{
    const PROMPT = "\033[0;34mStg>\033[0m ";

    const DEBUG = 'debug';
    const INFO = 'info';
    const COMMENT = 'comment';
    const WARNING = 'warning';
    const ERROR = 'error';
    const FATAL = 'fatal';

    const TIMESTAMP = '%H:%M:%S.%f %a, %d %B of %Y';

    # Reset
    const C_RESET = "\033[0m";       # Text Reset

    # Regular Colors
    const C_BLACK = "\033[0;30m";        # Black
    const C_RED = "\033[0;31m";          # Red
    const C_GREEN = "\033[0;32m";        # Green
    const C_YELLOW = "\033[0;33m";       # Yellow
    const C_BLUE = "\033[0;34m";         # Blue
    const C_PURPLE = "\033[0;35m";       # Purple
    const C_CYAN = "\033[0;36m";         # Cyan
    const C_WHITE = "\033[0;37m";        # White

    # Bold
    const C_BBLACK = "\033[1;30m";       # Black
    const C_BRED = "\033[1;31m";         # Red
    const C_BGREEN = "\033[1;32m";       # Green
    const C_BYELLOW = "\033[1;33m";      # Yellow
    const C_BBLUE = "\033[1;34m";        # Blue
    const C_BPURPLE = "\033[1;35m";      # Purple
    const C_BCYAN = "\033[1;36m";        # Cyan
    const C_BWHITE = "\033[1;37m";       # White

    # Underline
    const C_UBLACK = "\033[4;30m";       # Black
    const C_URED = "\033[4;31m";         # Red
    const C_UGREEN = "\033[4;32m";       # Green
    const C_UYELLOW = "\033[4;33m";      # Yellow
    const C_UBLUE = "\033[4;34m";        # Blue
    const C_UPURPLE = "\033[4;35m";      # Purple
    const C_UCYAN = "\033[4;36m";        # Cyan
    const C_UWHITE = "\033[4;37m";       # White

    # Background
    const C_ON_BLACK = "\033[40m";       # Black
    const C_ON_RED = "\033[41m";         # Red
    const C_ON_GREEN = "\033[42m";       # Green
    const C_ON_YELLOW = "\033[43m";      # Yellow
    const C_ON_BLUE = "\033[44m";        # Blue
    const C_ON_PURPLE = "\033[45m";      # Purple
    const C_ON_CYAN = "\033[46m";        # Cyan
    const C_ON_WHITE = "\033[47m";       # White

    /**
     * @var array
     */
    protected $severities = array(
        self::DEBUG, self::INFO, self::COMMENT, self::WARNING, self::ERROR, self::FATAL,
    );

    /**
     * @var array
     */
    protected static $formats = array(
        self::DEBUG => array(self::C_CYAN),
        self::INFO => array(self::C_BPURPLE),
        self::COMMENT => array(self::C_WHITE, self::C_ON_BLUE),
        self::WARNING => array(self::C_BYELLOW),
        self::ERROR => array(self::C_BRED),
        self::FATAL => array(self::C_WHITE, self::C_UWHITE, self::C_ON_RED),
    );

    /** @var resource */
    protected $stdin;
    /** @var resource */
    protected $stdout;
    /** @var resource */
    protected $stderr;

    /** @var string */
    protected $command;
    /** @var array */
    protected $args;

    /**
     * @return resource
     */
    protected function getStdin()
    {
        if (is_null($this->stdin)) {
            $this->stdin = fopen('php://stdin', 'r');
        }

        return $this->stdin;
    }

    /**
     * @return resource
     */
    protected function getStdOut()
    {
        if (is_null($this->stdout)) {
            $this->stdout = fopen('php://stdout', 'w');
        }

        return $this->stdout;
    }

    /**
     * @return resource
     */
    protected function getStdErr()
    {
        if (is_null($this->stderr)) {
            $this->stderr = fopen('php://stderr', 'w');
        }

        return $this->stderr;
    }

    /**
     * @param $msg
     *
     * @return int
     */
    public function write($msg, $newLine = true)
    {
        $isError = false;
        $pattern = sprintf('!^%%\[(?P<tag>%s)\]!', implode('|', $this->severities));
        if (preg_match($pattern, $msg, $matches)) {
            $msg = preg_replace_callback($pattern, function ($matches) use (&$isError) {
                if (in_array($matches['tag'], array(self::WARNING, self::FATAL, self::ERROR))) {
                    $isError = true;
                }

                return implode(self::$formats[$matches['tag']]);
            }, $msg);
            $msg .= self::C_RESET;
        }
        if ($newLine) {
            $msg .= PHP_EOL;
        }
        if ($isError) {
            $this->err($msg);
        }

        $this->out($msg);
    }

    /**
     * @return int
     */
    public function clear()
    {
        fwrite($this->getStdOut(), "\r");
    }

    /**
     *
     */
    public function nl()
    {
        fwrite($this->getStdOut(), PHP_EOL);
    }

    /**
     * @param $text
     */
    public function err($text)
    {
        fwrite($this->getStdErr(), $text);
    }

    /**
     * @param $text
     *
     * @return int
     */
    public function out($text)
    {
        fwrite($this->getStdOut(), $text);
    }

    public function readline()
    {
        $cmd = readline(self::PROMPT);
        readline_add_history($cmd);
        $pieces = explode(" ", trim($cmd));
        $this->command = array_shift($pieces);
        $this->getArgs();
    }

    public function areArgsValid()
    {
        return $this->args !== false;
    }

    public function getArgs()
    {
        if (is_null($this->args)) {
            $argv = $_SERVER['argv'];
            $this->args = array_slice($argv, 2);
        }

        return $this->args;
    }

    public function getCommand()
    {
        if (is_null($this->command)) {
            $argv = $_SERVER['argv'];
            array_shift($argv);
            $this->command = array_shift($argv);
        }

        return $this->command;
    }
}
