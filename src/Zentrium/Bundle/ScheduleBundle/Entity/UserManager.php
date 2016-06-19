<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\Group;
use Zentrium\Bundle\CoreBundle\Entity\User as BaseUser;

class UserManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var EntityRepository
     */
    private $parentRepository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
        $this->parentRepository = $em->getRepository(BaseUser::class);
    }

    public function findAll()
    {
        $qb = $this->createParentQueryBuilder();
        $rows = $qb->getQuery()->getResult();

        return $this->handleParentResult($rows);
    }

    public function findBySkill(Skill $skill)
    {
        $qb = $this->createParentQueryBuilder()
            ->leftJoin('e.skills', 'sf')
            ->where('sf.id = :skill')
            ->setParameter('skill', $skill)
        ;
        $rows = $qb->getQuery()->getResult();

        return $this->handleParentResult($rows);
    }

    public function findByGroup(Group $group)
    {
        $qb = $this->createParentQueryBuilder()
            ->leftJoin('u.groups', 'gf')
            ->where('gf.id = :group')
            ->setParameter('group', $group)
        ;
        $rows = $qb->getQuery()->getResult();

        return $this->handleParentResult($rows);
    }

    public function findByGroupAndSkill(Group $group, Skill $skill)
    {
        $qb = $this->createParentQueryBuilder()
            ->leftJoin('u.groups', 'gf')
            ->leftJoin('e.skills', 'sf')
            ->where('gf.id = :group')
            ->andWhere('sf.id = :skill')
            ->setParameter('group', $group)
            ->setParameter('skill', $skill)
        ;
        $rows = $qb->getQuery()->getResult();

        return $this->handleParentResult($rows);
    }

    public function findOneByBase(BaseUser $base)
    {
        $user = $this->repository->findOneBy(['base' => $base]);

        if ($user === null) {
            $user = new User($base);
            if ($base->getId() !== null) {
                $this->save($user);
            }
        }

        return $user;
    }

    public function save(User $user)
    {
        $this->em->transactional(function (EntityManager $em) use ($user) {
            $em->persist($user);
        });
    }

    private function createParentQueryBuilder()
    {
        return $this->parentRepository->createQueryBuilder('u')
            ->addSelect('e')
            ->addSelect('g')
            ->addSelect('s')
            ->leftJoin('u.groups', 'g')
            ->leftJoin(User::class, 'e', 'WITH', 'e.base = u.id')
            ->leftJoin('e.skills', 's')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
        ;
    }

    private function handleParentResult($rows)
    {
        $result = [];
        foreach ($rows as $row) {
            if ($row instanceof User) {
                $result[$row->getBase()->getId()] = $row;
            } elseif ($row instanceof BaseUser && !isset($result[$row->getId()])) {
                $result[$row->getId()] = new User($row);
            }
        }

        return $result;
    }
}
