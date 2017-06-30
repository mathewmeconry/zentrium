<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Zentrium\Bundle\CoreBundle\Twig\GridDistributor;

class GridDistributorTest extends TestCase
{
    /**
     * @dataProvider distributeSamples
     */
    public function testDistribute($columns, $minWidth, $n, $expected)
    {
        $distributor = new GridDistributor($columns, $minWidth);

        $actual = $distributor->distribute($n);

        $this->assertSame($expected, $actual);
    }

    public function distributeSamples()
    {
        return [
            [8, 1, 0, []],
            [8, 2, 5, [8, 4, 4, 4, 4]],
            [12, 3, 1, [12]],
            [12, 3, 2, [6, 6]],
            [12, 3, 10, [4, 4, 4, 4, 4, 4, 3, 3, 3, 3]],
            [12, 6, 3, [12, 6, 6]],
        ];
    }
}
