<?php

namespace Stego\Packages;

class InspectorTest extends \PHPUnit_Framework_TestCase
{
    public function versionConverterDataProvider()
    {
        return array(
            array('^1.2.3', '^1\..*!'),
            array('^v2.4.3', '^v2\..*!'),
            array('~1.2.3', '^1\.2\..*!'),
            array('~1.2', '^1\..*!'),
            array('1.0 - 2.3', ''),
        );
    }

    /**
     * @param $version
     * @param $expected
     * @dataProvider versionConverterDataProvider
     */
    public function testVersionConverter($version, $expected)
    {
        $inspector = new Inspector();
        $result = $inspector->createVersionExpression($version);

        var_dump($result);
    }
} 