<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PostsCommentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostsCommentsRepository::class)
 * @ORM\Table(name="tab_posts_comments")
 * @ORM\HasLifecycleCallbacks()
 */
class PostsComments
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
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @var Posts
     */
    private Posts $post;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @var Users
     */
    private Users $author;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private string $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $state;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPost(): Posts
    {
        return $this->post;
    }

    public function setPost(Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getAuthor(): Users
    {
        return $this->author;
    }

    public function setAuthor(Users $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
