<?php

namespace Stego\Packages;

use Composer\Package\Package;

interface CompilerInterface {
    public function compile($package, $source);
}