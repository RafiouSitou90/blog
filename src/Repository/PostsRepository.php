<?php

namespace App\Repository;

use App\Entity\Posts;
use App\Entity\Tags;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * @param Users $author
     * @param Tags|null $tag
     * @return Posts[]
     */
    public function findAllLatestForAuthor(Users $author, ?Tags $tag)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect(['tags', 'category', 'author', 'ratings', 'comments', 'medias'])
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.medias', 'medias')
            ->leftJoin('p.tags', 'tags')
            ->leftJoin('p.author', 'author')
            ->leftJoin('p.ratings', 'ratings')
            ->leftJoin('p.comments', 'comments')
            ->andWhere('p.author = :author')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('author', $author)
        ;

        if ($tag !== null) {
            $qb->andWhere(':tag MEMBER OF p.tags')->setParameter('tag', $tag);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $slug
     * @param Users $author
     * @return Posts|null
     * @throws NonUniqueResultException
     */
    public function findOneBySlugForAuthor(string $slug, Users $author)
    {
        return $this->createQueryBuilder('p')
            ->addSelect(['tags', 'category', 'author', 'ratings', 'comments', 'medias'])
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.medias', 'medias')
            ->leftJoin('p.tags', 'tags')
            ->leftJoin('p.author', 'author')
            ->leftJoin('p.ratings', 'ratings')
            ->leftJoin('p.comments', 'comments')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.author = :author')
            ->setParameters(['slug' => $slug, 'author' => $author])
            ->getQuery()->getOneOrNullResult()
        ;
    }
}
