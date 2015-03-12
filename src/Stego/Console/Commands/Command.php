<?php

namespace Stego\Console\Commands;

use Stego\Console\ApplicationAware;

trait Command
{
    use ApplicationAware;

    abstract public function execute();

    /**
     * @param $name
     * @param $version
     * @return bool|Package
     */
    /*
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
            $this->getConsoleIO()->writeError('Alternatives were '.implode(', ', array_map(function (Package $p) { return $p->getPrettyString(); }, $packages)).'.');
            $this->getConsoleIO()->writeError('<comment>Please use a more specific constraint to pick a different package.</comment>');
        } elseif ($packages) {
            $package = reset($packages);
            $this->getConsoleIO()->writeError('<info>Found an exact match '.$package->getPrettyString().'.</info>');
        } else {
            $this->getConsoleIO()->writeError('<error>Could not find a package matching '.$name.'.</error>');

            return false;
        }

        return $package;
    }*/
} 