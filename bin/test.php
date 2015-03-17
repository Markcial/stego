#!/usr/bin/php
<?php
system('bin/stego install doctrine/common v2.4.0');
system('bin/stego install doctrine/collections');
system('bin/stego install ulabox/money');
//system('bin/stego loader');

require 'src/functions.php';

Stego\import('doctrine/common', 'v2.4.0');
Stego\import('doctrine/collections');
Stego\import('ulabox/money');
Stego\import('symfony/event-dispatcher');
Stego\import('guzzle/guzzle');
Doctrine\Common\Util\Debug::dump('asdasd');
$array = new Doctrine\Common\Collections\ArrayCollection();
$array->add(12);
var_dump($array);
$money = Money\Money::EUR('10');
var_dump($money);

$client = new Guzzle\Http\Client();
var_dump($client);
//var_dump(spl_autoload_functions());die;