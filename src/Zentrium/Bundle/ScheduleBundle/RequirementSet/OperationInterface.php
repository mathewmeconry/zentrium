<?php

namespace Zentrium\Bundle\ScheduleBundle\RequirementSet;

use Zentrium\Bundle\ScheduleBundle\Entity\RequirementSet;

interface OperationInterface
{
    public function apply(RequirementSet $set);
}
