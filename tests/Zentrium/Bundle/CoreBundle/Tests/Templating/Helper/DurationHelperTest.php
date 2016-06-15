<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Twig;

use DateTime;
use League\Period\Period;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper;

class DurationHelperTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider provideSamples
     */
    public function testFormat($duration, $expectedOutput)
    {
        $helper = new DurationHelper($this->translator);

        $this->assertSame($expectedOutput, $helper->format($duration));
    }

    public function provideSamples()
    {
        return [
            [new Period(new DateTime('2016-01-01 00:00:00'), new DateTime('2016-01-03 00:00:10')), '48 h 10 s'],
            [600, '10 m'],
            [-600, '- 10 m'],
            [72 * 3600, '72 h'],
            [-72 * 3600, '- 72 h'],
            [0, '0 s'],
            [1, '1 s'],
            [-1, '- 1 s'],
            [120.2, '2 m'],
            [120.8, '2 m 1 s'],
            [-120.2, '- 2 m'],
            [-120.8, '- 2 m 1 s'],
            [null, null],
        ];
    }
}
