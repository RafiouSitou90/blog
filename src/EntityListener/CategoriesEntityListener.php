<?php

namespace App\EntityListener;

use App\Entity\Categories;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class CategoriesEntityListener
 * @package App\EntityListener
 */
class CategoriesEntityListener
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
     * @param Categories $category
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Categories $category, LifecycleEventArgs $event): void
    {
        $category->computeSlug($this->slugger);
    }

    /**
     * @param Categories $category
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(Categories $category, LifecycleEventArgs $event): void
    {
        $category->computeSlug($this->slugger);
    }
}
