<?php

namespace Zentrium\Bundle\CoreBundle\Doctrine;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\UnexpectedResultException;
use PDO;

class ColumnHydrator extends AbstractHydrator
{
    const NAME = 'column';

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        $result = $this->_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return $result;
        }

        if (!is_array($result[0])) {
            throw new UnexpectedResultException();
        }

        $indexBy = $this->_rsm->hasIndexBy('scalars') ? $this->_rsm->indexByMap['scalars'] : null;

        $scalars = [];
        foreach ($result as $row) {
            if ($indexBy) {
                $key = $row[$indexBy];
                unset($row[$indexBy]);
                $scalars[$key] = array_shift($row);
            } else {
                $scalars[] = array_shift($row);
            }
        }

        return $scalars;
    }
}
