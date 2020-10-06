<?php

namespace App\Repository;

use App\Entity\Posts;
use App\Entity\Tags;
use App\Entity\Users;
use DateTime;
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

    /**
     * @param Tags|null $tag
     * @param int|null $limit
     * @return Posts[]
     */
    public function findAllLatest(?Tags $tag = null, ?int $limit = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('author', 'category')
            ->innerJoin('p.author', 'author')
            ->leftJoin('p.category', 'category')
            ->where('p.publishedAt <= :now')
            ->andWhere('p.state = :state')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameters([
                'now' => new DateTime('now'),
                'state' => Posts::getPublished()
            ])
        ;

        if ($tag !== null) {
            $qb->andWhere(':tag MEMBER OF p.tags')->setParameter('tag', $tag);
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $slug
     * @return Posts|null
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->addSelect('author', 'tags', 'comments', 'category', 'ratings', 'medias')
            ->leftJoin('p.author', 'author')
            ->leftJoin('p.category', 'category')
            ->leftJoin('p.comments', 'comments')
            ->leftJoin('p.tags', 'tags')
            ->leftJoin('p.ratings', 'ratings')
            ->leftJoin('p.medias', 'medias')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.state = :state')
            ->orderBy('comments.createdAt', 'DESC')
            ->setParameters([
                'slug' => $slug,
                'state' => Posts::getPublished()
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string|null $words
     * @return array|int|mixed|string|Posts[]
     */
    public function search(string $words = null)
    {
        if ($words != null) {
            $query = $this->createQueryBuilder('p');
            $query
                ->where("p.state = 'published'")
//                ->where('p.state = :published')
                ->andWhere('MATCH_AGAINST(p.title, p.summary, p.content) AGAINST (:words boolean) > 0')
                ->setParameter('words', $words)
//                ->setParameters(['words' => $words, 'published' => 'published'])
            ;
            return $query->getQuery()->getResult();
        } else {
            return [];
        }
    }
}
