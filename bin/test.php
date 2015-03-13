#!/usr/bin/php
<?php
//system('bin/stego install doctrine/common -c2.4');
//system('bin/stego loader');

require 'deps/stego.phar';

Stego\import('doctrine/common','v2.4.0');
Stego\import('doctrine/collections', 'v1.0');
Stego\import('ulabox/money', '1.1.1');
Doctrine\Common\Util\Debug::dump('asdasd');
$array = new Doctrine\Common\Collections\ArrayCollection();
$array->add(12);
var_dump($array);
$money = Money\Money::EUR('10');
var_dump($money);
