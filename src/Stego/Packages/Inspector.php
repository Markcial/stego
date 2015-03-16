<?php

namespace Stego\Packages;

class Inspector
{
    const CARET = '^';
    const TILDE = '~';
    const BIGT = '>';
    const BET = '>=';
    const LOWT = '<';
    const LET = '<=';
    const EQ = '=';

    protected $operators = array(
        self::CARET, self::TILDE, self::BIGT, self::BET, self::LOWT, self::LET, self::EQ,
    );

    private function getVersionString($version)
    {
        return preg_replace(sprintf('!^(%s)!', implode("|", $this->operators)), '', $version);
    }

    public function createVersionExpression($version)
    {
        // tilde operator, minor upgrades
        if (substr($version, 0, 1) === '~') {
            $pieces = explode(".", $this->getVersionString($version));
            array_pop($pieces);

            return sprintf("!^%s\..*!", implode("\.", $pieces));
        }
        // caret operator, major upgrades
        if (substr($version, 0, 1) === '^') {
            $version = substr($version, 1, strlen($version));
            $pieces = explode(".", $version);
            $major = array_shift($pieces);

            return sprintf("!^%s\..*!", $major);
        }
        // wildcard
        if (strpos($version, '*') !== false) {
            return sprintf('!^%s!', str_replace('*', '.*', preg_quote($version)));
        }
        // hyphen range
        if (strpos($version, ' - ') !== false) {
            $versions = explode(' - ', $version);
        }
        // range

        // exact version

        return '';
    }
    // responsible of the package versioning checks
    protected function getRequires($vendor)
    {
    }

    public function resolveDependencies($vendor, $version)
    {
        $browser = new Browser();
        $blacklist = array('php');

        $dependencies = array();
    }
}
