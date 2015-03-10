<?php

namespace Stego\Packages;

use Composer\Package\Package;

interface CompilerInterface {
    public function compile(Package $package, $source);
}