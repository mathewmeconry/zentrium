<?php

namespace Zentrium\Bundle\CoreBundle\Twig;

class GridDistributor
{
    private $columns;
    private $minWidth;

    public function __construct($columns, $minWidth)
    {
        $this->columns = $columns;
        $this->minWidth = $minWidth;
    }

    /**
     * Distribute a number of boxes such that every row is filled.
     *
     * @param int $n Number of boxes
     *
     * @return array Widths of all boxes
     */
    public function distribute($n)
    {
        if ($n <= 0) {
            return [];
        }

        $result = array_fill(0, $n, $this->minWidth);
        $sum = $n * $this->minWidth;
        for ($weight = $this->minWidth + 1; $weight <= $this->columns && $sum % $this->columns != 0; $weight++) {
            if ($this->columns % $weight != 0) {
                continue;
            }
            $count = $this->columns / $weight;
            for ($i = 0; $i < $n && $sum % $this->columns != 0; $i += $count) {
                for ($j = $i; $j < $i + $count && $j < $n; $j++) {
                    $sum += $weight - $result[$j];
                    $result[$j] = $weight;
                }
            }
        }

        return $result;
    }
}
