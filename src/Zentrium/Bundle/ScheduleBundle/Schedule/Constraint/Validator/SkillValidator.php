<?php

namespace Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Validator;

use Zentrium\Bundle\CoreBundle\Templating\Helper\DurationHelper;
use Zentrium\Bundle\ScheduleBundle\Entity\Schedule;
use Zentrium\Bundle\ScheduleBundle\Entity\UserManager;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\ConstraintInterface;
use Zentrium\Bundle\ScheduleBundle\Schedule\Constraint\Message;

class SkillValidator implements ValidatorInterface
{
    private $userManager;
    private $durationHelper;

    public function __construct(UserManager $userManager, DurationHelper $durationHelper)
    {
        $this->userManager = $userManager;
        $this->durationHelper = $durationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Schedule $schedule, ConstraintInterface $constraint)
    {
        $messages = [];

        $users = $this->userManager->findAll();
        foreach ($schedule->getShifts() as $shift) {
            $user = $users[$shift->getUser()->getId()];
            $skill = $shift->getTask()->getSkill();
            if ($skill !== null && !$user->getSkills()->contains($skill)) {
                $messages[] = Message::warning($constraint, 'zentrium_schedule.constraint.skill.message', [
                    '%user%' => $user->getBase()->getName(),
                    '%duration%' => $this->durationHelper->format($shift->getPeriod()->getTimestampInterval()),
                    '%skill%' => $skill->getName(),
                ], [$shift]);
            }
        }

        return $messages;
    }
}
