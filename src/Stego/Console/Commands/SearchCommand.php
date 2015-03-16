<?php

namespace Stego\Console\Commands;

class SearchCommand
{
    use Command;

    public function execute($args = array())
    {
        $library = array_shift($args);
        $version = array_shift($args);

        // function that fetches the dependencies in the src folder
        $container = $this->getContainer();
        $browser = $container->get('browser');
        $data = $browser->find($library, $version);

        $display = array();
        foreach ($data['results'] as $result) {
            $display[] = sprintf(
                "%s : %s %s %s %s %s",
                str_pad(substr($result['name'], 0, 20), 20, ' '),
                str_pad(substr($result['description'], 0, 110), 110, ' '),
                str_pad($result['downloads'], 8, ' ', STR_PAD_LEFT),
                chr(0xf0) . chr(0x9f) . chr(0x92) . chr(0xbe),
                str_pad($result['favers'], 3, ' ', STR_PAD_LEFT),
                chr(0xf0) . chr(0x9f) . chr(0x91) . chr(0x8d)
            );
        }
        $this->getStdio()->write(implode("\n", $display));
    }
}
