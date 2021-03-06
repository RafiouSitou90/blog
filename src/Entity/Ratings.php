<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\RatingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RatingsRepository::class)
 * @ORM\Table(name="tab_ratings")
 * @ORM\HasLifecycleCallbacks()
 */
class Ratings
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @var string
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     * @var Posts|null
     */
    private ?Posts $post;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     * @var Users|null
     */
    private ?Users $author;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private int $rating;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Posts|null
     */
    public function getPost(): ?Posts
    {
        return $this->post;
    }

    /**
     * @param Posts|null $post
     * @return $this
     */
    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Users|null
     */
    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    /**
     * @param Users|null $author
     * @return $this
     */
    public function setAuthor(?Users $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return $this
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
