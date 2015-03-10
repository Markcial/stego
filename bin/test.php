#!/usr/bin/php
<?php
//system('bin/stego install doctrine/common -c2.4');
system('bin/stego loader');

$loader = require 'deps/stego.phar';
$loader->import('doctrine/common');
$loader->import('doctrine/collections', 'v1.0');
$loader->import('ulabox/money');
Doctrine\Common\Util\Debug::dump('asdasd');
$array = new Doctrine\Common\Collections\ArrayCollection();
$array->add(12);
var_dump($array);
$money = Money\Money::EUR('10');
var_dump($money);
