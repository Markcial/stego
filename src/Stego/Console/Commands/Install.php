<?php


namespace Stego\Console\Commands;

use Stego\Packages\Browser;
use Stego\Packages\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $browser = new Browser();
//        $manager = new Installer();

        $data = $browser->versionDetails($name);

        //$data = $manager->download($name, $version);

        var_dump($data);

        die;
    }
}
