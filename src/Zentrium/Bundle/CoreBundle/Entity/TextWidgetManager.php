<?php

namespace Zentrium\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class TextWidgetManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(TextWidget::class);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function save(TextWidget $widget)
    {
        $this->em->transactional(function (EntityManager $em) use ($widget) {
            $em->persist($widget);
        });
    }
}
