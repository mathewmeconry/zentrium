<?php

namespace Vkaf\Bundle\OafBundle\Tests\Twig;

use PHPUnit_Framework_TestCase;
use Vkaf\Bundle\OafBundle\Twig\Extension;

class ExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider truncateSamples
     */
    public function testDistribute($text, $length, $expected)
    {
        $extension = new Extension();

        $actual = $extension->truncate($text, $length);

        $this->assertSame($expected, $actual);
    }

    public function truncateSamples()
    {
        return [
            [null, 6, null],
            ['', 6, ''],
            ['Lorem ipsum dolor sit amet', 6, '…'],
            ['Lorem ipsum dolor sit amet', 7, 'Lorem …'],
            ['Lorem ipsum dolor sit amet', 20, 'Lorem ipsum dolor …'],
        ];
    }
}
