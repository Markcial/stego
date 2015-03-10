<?php

namespace Stego\Console\Commands;

use Stego\Packages\Browser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Search extends Command
{
    protected function configure()
    {
        $this
            ->setName('search')
            ->setAliases(array('browse', 'find'))
            ->setDescription('Searches for packages.')
            ->setDefinition(
                array(
                    new InputArgument(
                        'name',
                        InputArgument::REQUIRED,
                        'Name of the package to search.'
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
        $data = $browser->find($name);

        $display = array();
        foreach ($data['results'] as $result) {
            $display[] = sprintf(
                "%s : %s %s %s %s %s",
                str_pad(substr($result['name'], 0, 20), 20, ' '),
                str_pad(substr($result['description'], 0, 110), 110, ' '),
                str_pad($result['downloads'], 8, ' ', STR_PAD_LEFT),
                chr(0xf0).chr(0x9f).chr(0x92).chr(0xbe),
                str_pad($result['favers'], 3, ' ', STR_PAD_LEFT),
                chr(0xf0).chr(0x9f).chr(0x91).chr(0x8d)
            );
        }
        $output->writeln($display);
    }
}
