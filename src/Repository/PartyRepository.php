<?php

namespace App\Repository;

use App\Entity\Party;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Party>
 */
class PartyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Party::class);
    }

    // src/Repository/PartyRepository.php

    public function findWithFilter(?string $filter): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.characters', 'c')
            ->addSelect('c');

        if ($filter === 'full') {
            // Groupes où le nombre de personnages est égal au maxSize
            $qb->andWhere('SIZE(p.characters) >= p.maxSize');
        } elseif ($filter === 'available') {
            // Groupes où il reste de la place
            $qb->andWhere('SIZE(p.characters) < p.maxSize');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Party[] Returns an array of Party objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Party
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
