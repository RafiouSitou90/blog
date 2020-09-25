<?php

namespace App\EntityListener;

use App\Entity\Posts;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class PostsEntityListener
 * @package App\EntityListener
 */
class PostsEntityListener
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * CategoriesEntityListener constructor.
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param Posts $post
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Posts $post, LifecycleEventArgs $event): void
    {
        $post->computeSlug($this->slugger);
    }

    /**
     * @param Posts $post
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(Posts $post, LifecycleEventArgs $event): void
    {
        $post->computeSlug($this->slugger);
    }
}
