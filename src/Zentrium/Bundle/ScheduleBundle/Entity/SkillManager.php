<?php

namespace Zentrium\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zentrium\Bundle\CoreBundle\Entity\User;
use Zentrium\Bundle\CoreBundle\Util\SnapshotCollection;

class SkillManager
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
        $this->repository = $em->getRepository(Skill::class);
    }

    public function findAll()
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function findAllWithUserCounts()
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->select('s')
            ->addSelect('COUNT(u.id)')
            ->leftJoin('s.users', 'u')
            ->orderBy('s.name', 'ASC')
            ->groupBy('s.id')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByUser(User $user)
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->select('s')
            ->leftJoin('s.users', 'u')
            ->where('u.id = :user')
            ->orderBy('s.name', 'ASC')
            ->setParameter('user', $user->getId())
        ;

        return $qb->getQuery()->getResult();
    }

    public function updateUser(User $user, SnapshotCollection $skills)
    {
        $this->em->transactional(function (EntityManager $em) use ($user, $skills) {
            foreach ($skills->getInsertDiff() as $skill) {
                $skill->getUsers()->add($user);
                $em->persist($skill);
            }
            foreach ($skills->getDeleteDiff() as $skill) {
                $skill->getUsers()->removeElement($user);
                $em->persist($skill);
            }
            $em->flush();
            $skills->takeSnapshot();
        });
    }

    public function save(Skill $skill)
    {
        $this->em->transactional(function (EntityManager $em) use ($skill) {
            $em->persist($skill);
        });
    }
}
