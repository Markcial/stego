<?php

namespace Stego\Console\Commands;

use Composer\DependencyResolver\Pool;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Package\Package;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Stego\Console\ApplicationAware;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

trait Command
{
    use ApplicationAware;

    abstract public function execute($args = array());

    protected $composer;

    protected $consoleIO;

    protected function getConsoleIO()
    {
        if (is_null($this->consoleIO)) {
            $this->consoleIO = new ConsoleIO(new ArgvInput($_SERVER['argv']), new ConsoleOutput(), new HelperSet());
        }

        return $this->consoleIO;
    }

    protected function getComposer()
    {
        if (is_null($this->composer)) {
            $this->composer = Factory::create($this->getConsoleIO());
        }

        return $this->composer;
    }

    /**
     * @param $name
     * @param $version
     * @return bool|Package
     */
    protected function searchPackage($name, $version = '@stable')
    {
        $localRepo = $this->getComposer()->getRepositoryManager()->getLocalRepository();
        $repos = new CompositeRepository(array_merge(array($localRepo), $this->getComposer()->getRepositoryManager()->getRepositories()));

        $pool = new Pool();
        $pool->addRepository($repos);

        $parser = new VersionParser();
        $constraint = ($version) ? $parser->parseConstraints($version) : null;
        $packages = $pool->whatProvides($name, $constraint);

        if (count($packages) > 1) {
            $package = reset($packages);
            $this->getConsoleIO()->writeError('<info>Found multiple matches, selected '.$package->getPrettyString().'.</info>');
            $this->getConsoleIO()->writeError('Alternatives were '.implode(', ', array_map(function ($p) { return $p->getPrettyString(); }, $packages)).'.');
            $this->getConsoleIO()->writeError('<comment>Please use a more specific constraint to pick a different package.</comment>');
        } elseif ($packages) {
            $package = reset($packages);
            $this->getConsoleIO()->writeError('<info>Found an exact match '.$package->getPrettyString().'.</info>');
        } else {
            $this->getConsoleIO()->writeError('<error>Could not find a package matching '.$name.'.</error>');

            return false;
        }

        return $package;
    }
} 