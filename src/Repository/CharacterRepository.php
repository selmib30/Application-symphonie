<?php

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Character>
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    /**
     * Recherche de personnages avec filtres : nom, classe, race
     */
    public function findWithFilters(?string $name, ?int $classId, ?int $raceId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.characterClass', 'cc')
            ->leftJoin('c.race', 'r');

        if ($name) {
            $qb->andWhere('c.name LIKE :name')
               ->setParameter('name', '%' . $name . '%');
        }

        if ($classId) {
            $qb->andWhere('cc.id = :classId')
               ->setParameter('classId', $classId);
        }

        if ($raceId) {
            $qb->andWhere('r.id = :raceId')
               ->setParameter('raceId', $raceId);
        }

        return $qb->orderBy('c.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}