<?php


namespace Stego\Console\Commands;

use Composer\Package\Link;
use Composer\Package\Package;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends BaseCommand
{
    const BASE = 'deps';
    const PHAR_NAME = 'pkg.phar';

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

    /**
     * @param $vendor
     * @param string $version
     * @return string
     */
    protected function getInstallPath($vendor, $version = 'dev-master')
    {
        return self::BASE . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . $vendor;
    }

    /**
     * @return string
     */
    protected function getTempPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stego';
    }

    /**
     * @param $name
     * @param $version
     * @return Package
     */
    protected function install(Package $package)
    {
        /** @var Package $package */
        $this->getComposer()->getDownloadManager()->download($package, $this->getTempPath());

        $this->getApplication()->getCompiler()->compile($package, $this->getTempPath());

        return $package;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if (!$version = $input->getOption('constraint')) {
            $version = '@stable';
        }

        $package = $this->searchPackage($name, $version);
        $package = $this->install($package);

        /** @var Link $require */
        foreach ($package->getRequires() as $require) {
            $dep = $this->searchPackage($require->getTarget(), $require->getPrettyConstraint());
            if ($dep) {
                $this->install($dep);
            }
        }
    }
}
