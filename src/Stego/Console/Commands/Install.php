<?php


namespace Stego\Console\Commands;

use Composer\Installer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install
{
    use Command;

    /** @var RootPackage */
    protected $rootPackage;
    protected $localRepo;
    protected $platformRepo;
    protected $installedRepo;
/*
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Searches for packages.')
            ->setDefinition(
                array(
                    new InputArgument(
                        'name',
                        InputArgument::REQUIRED,
                        'Name of the package to install.'
                    ),
                    new InputOption(
                        'constraint',
                        'c',
                        InputOption::VALUE_OPTIONAL,
                        'Version constraint'
                    )
                )
            )
        ;
    }
*/

    /**
     * @return Package
     */
    protected function install($package)
    {
        $tmp = $this->getContainer()->get('stego:vars:fs:tmp');
        /** @var Package $package */
        $this->getComposer()->getDownloadManager()->download($package, $tmp);

        $this->getContainer()->get('stego:compiler')->compile($package, $tmp);

        @unlink($tmp);

        return $package;
    }

    public function execute($args = array())
    {
        $library = array_shift($args);
        $version = array_shift($args);

        $package = $this->searchPackage($library, $version);
        $package = $this->install($package);

        /** @var Link $require */
        foreach ($package->getRequires() as $require) {
            if (preg_match('#^(php|ext-.*)$#', $require->getTarget())) {
                continue;
            }
            $dep = $this->searchPackage($require->getTarget(), $require->getPrettyConstraint());

            if ($dep) {
                $this->install($dep);
            }
        }
    }
}
