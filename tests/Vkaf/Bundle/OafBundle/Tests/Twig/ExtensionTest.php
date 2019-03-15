<?php

namespace Vkaf\Bundle\OafBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Vkaf\Bundle\OafBundle\Twig\Extension;

class ExtensionTest extends TestCase
{
    /**
     * @dataProvider truncateSamples
     */
    public function testTruncate($text, $length, $expected)
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $extension = new Extension('', $urlGenerator, $translator);

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
