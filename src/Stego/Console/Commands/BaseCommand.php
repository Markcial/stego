<?php

namespace Stego\Console\Commands;


use Composer\Composer;
use Composer\DependencyResolver\Pool;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Package\Package;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var ConsoleIO */
    protected $consoleIO;
    /** @var Composer */
    protected $composer;

    /**
     * @return ConsoleIO
     */
    protected function getConsoleIO()
    {
        return $this->consoleIO;
    }

    /**
     * @return Composer
     */
    protected function getComposer()
    {
        if (is_null($this->composer)) {
            $this->composer = Factory::create($this->getConsoleIO(), null, true);
        }

        return $this->composer;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->consoleIO = new ConsoleIO($input, $output, $this->getHelperSet());
        parent::initialize($input, $output);
    }

    /**
     * @param $name
     * @param $version
     * @return bool|Package
     */
    protected function searchPackage($name, $version = '@stable')
    {
        if ($composer = $this->getComposer(false)) {
            $localRepo = $composer->getRepositoryManager()->getLocalRepository();
            $repos = new CompositeRepository(array_merge(array($localRepo), $composer->getRepositoryManager()->getRepositories()));
        } else {
            $defaultRepos = Factory::createDefaultRepositories($this->getConsoleIO());
            $this->getConsoleIO()->writeError('No composer.json found in the current directory, searching packages from ' . implode(', ', array_keys($defaultRepos)));
            $repos = new CompositeRepository($defaultRepos);
        }

        $pool = new Pool();
        $pool->addRepository($repos);

        $parser = new VersionParser();
        $constraint = ($version) ? $parser->parseConstraints($version) : null;
        $packages = $pool->whatProvides($name, $constraint, true);

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
    }
} 