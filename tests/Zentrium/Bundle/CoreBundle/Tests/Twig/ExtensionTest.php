<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Twig;

use DateTime;
use League\Period\Period;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Zentrium\Bundle\CoreBundle\Twig\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    public function setUp()
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [
            'zentrium.twig.duration.hours' => '%hours% h',
            'zentrium.twig.duration.minutes' => '%minutes% m',
            'zentrium.twig.duration.seconds' => '%seconds% s',
        ], 'en');

        $dateTimeHelper = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $phoneNumberHelper = $this->getMockBuilder('Zentrium\Bundle\CoreBundle\Templating\Helper\PhoneNumberHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->extension = new Extension($translator, $phoneNumberHelper, $dateTimeHelper);
    }

    /**
     * @dataProvider durationFilterSamples
     */
    public function testDurationFilter($duration, $expected)
    {
        $actual = $this->extension->durationFilter($duration);
        $this->assertSame($expected, $actual);
    }

    public function durationFilterSamples()
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
