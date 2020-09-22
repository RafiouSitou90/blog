<?php

namespace App\Repository;

use App\Entity\UsersProfiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersProfiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersProfiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersProfiles[]    findAll()
 * @method UsersProfiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersProfilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersProfiles::class);
    }

    // /**
    //  * @return UsersProfiles[] Returns an array of UsersProfiles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersProfiles
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
