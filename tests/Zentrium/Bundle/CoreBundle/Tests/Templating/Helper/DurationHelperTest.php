<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Templating\Helper;

use DateTime;
use League\Period\Period;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper;

class DurationHelperTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        $this->translator = new Translator('en');
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', [
            'zentrium.templating.duration.hours' => '%hours% h',
            'zentrium.templating.duration.minutes' => '%minutes% m',
            'zentrium.templating.duration.seconds' => '%seconds% s',
        ], 'en');
    }

    /**
     * @dataProvider provideFormatSamples
     */
    public function testFormat($duration, $expected, $expectedCompact)
    {
        $helper = new DurationHelper($this->translator);

        $this->assertSame($expected, $helper->format($duration));
        $this->assertSame($expectedCompact, $helper->format($duration, ['format' => 'compact']));
    }

    public function provideFormatSamples()
    {
        return [
            [new Period(new DateTime('2016-01-01 00:00:00'), new DateTime('2016-01-03 00:00:10')), '48 h 10 s', '48:00:10'],
            [600, '10 m', '0:10'],
            [-600, '- 10 m', '-0:10'],
            [72 * 3600, '72 h', '72:00'],
            [-72 * 3600, '- 72 h', '-72:00'],
            [0, '0 s', '0:00'],
            [1, '1 s', '0:00:01'],
            [-1, '- 1 s', '-0:00:01'],
            [120.2, '2 m', '0:02'],
            [120.8, '2 m 1 s', '0:02:01'],
            [-120.2, '- 2 m', '-0:02'],
            [-120.8, '- 2 m 1 s', '-0:02:01'],
            [null, null, null],
        ];
    }

    /**
     * @dataProvider provideFormatWithSecondsSamples
     */
    public function testFormatWithSeconds($duration, $withSeconds, $expected)
    {
        $helper = new DurationHelper($this->translator);

        $this->assertSame($expected, $helper->format($duration, ['format' => 'compact', 'with_seconds' => $withSeconds]));
    }

    public function provideFormatWithSecondsSamples()
    {
        return [
            [7200, true, '2:00:00'],
            [7200, false, '2:00'],
            [7200, null, '2:00'],
            [7201, true, '2:00:01'],
            [7201, false, '2:00'],
            [7201, null, '2:00:01'],
        ];
    }
}
