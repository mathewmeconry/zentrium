<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint;

use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;

class Checker
{
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @var Schedule
     * @var ConstraintInterface[] $constraints
     *
     * @return MessageInterface[]
     */
    public function check(Schedule $schedule, array $constraints = [])
    {
        $messages = [];

        foreach ($constraints as $constraint) {
            $validator = $this->registry->getValidator($constraint->getType());
            if ($validator === null) {
                $messages[] = Message::critical($constraint, 'zentrium_schedule.constraint.no_validator');
                continue;
            }

            $messages = array_merge($messages, array_values($validator->validate($schedule, $constraint)));
        }

        return $messages;
    }
}
