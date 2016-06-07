<?php

namespace Zentrium\Bundle\ScheduleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zentrium\Bundle\CoreBundle\Controller\ControllerTrait;
use Zentrium\Bundle\ScheduleBundle\Entity\Skill;
use Zentrium\Bundle\ScheduleBundle\Form\Type\SkillType;

/**
 * @Route("/schedules/skills")
 */
class SkillController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="schedule_skills")
     * @Template
     */
    public function indexAction()
    {
        $skills = $this->get('zentrium_schedule.manager.skill')->findAllWithUserCounts();

        return [
            'skills' => $skills,
        ];
    }

    /**
     * @Route("/new", name="schedule_skill_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        return $this->handleEdit($request, new Skill());
    }

    /**
     * @Route("/{skill}/edit", name="schedule_skill_edit")
     * @Template
     */
    public function editAction(Request $request, Skill $skill)
    {
        return $this->handleEdit($request, $skill);
    }

    /**
     * @Route("/{skill}/list", name="schedule_skill_list")
     * @Template
     */
    public function listAction(Skill $skill)
    {
        return [
            'skill' => $skill,
        ];
    }

    private function handleEdit(Request $request, Skill $skill)
    {
        $form = $this->createForm(SkillType::class, $skill);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('zentrium_schedule.manager.skill');
            $manager->save($skill);

            $this->addFlash('success', 'zentrium_schedule.skill.form.saved');

            return $this->redirectToRoute('schedule_skills');
        }

        return [
            'skill' => $skill,
            'form' => $form->createView(),
        ];
    }
}
