<?php

namespace Stego\Console\Commands;

use Composer\Package\Link;
use Stego\Packages\Compiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Loader extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('loader')
            ->setDescription('Creates the autoloader.')
            ->setDefinition(
                array(
                    new InputArgument(
                        'name',
                        InputArgument::REQUIRED,
                        'Name of the package to bootstrap.'
                    )
                )
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $package = $this->searchPackage($name);

        $requires = $package->getRequires();

        /** @var Compiler $compiler */
        $compiler = $this->getApplication()->getCompiler();

        $paths = array(
            $compiler->getDestinationFolder($package) . $compiler::PHAR_NAME
        );
        /** @var Link $require */
        foreach ($requires as $require) {
            $dep = $this->searchPackage($require->getTarget(), $require->getPrettyConstraint());
            if ($dep) {
                $paths[] = $compiler->getDestinationFolder($dep) . $compiler::PHAR_NAME;
            }
        }

        $data = '<?php' . "\n";
        $data .= '$deps = array();' . "\n";
        foreach ($paths as $path) {
            $data .= sprintf('$deps[] = require "%s";', $path) . "\n";
        }
        $data .= 'return $deps;';

        file_put_contents('loader.php', $data);
    }
} 