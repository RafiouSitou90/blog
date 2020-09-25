<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PostsCommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Posts|null
     */
    private ?Posts $post;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @var Users|null
     */
    private ?Users $author;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="The content should not be blank")
     * @Assert\NotNull(message="The content should not be null.")
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "The content must be at least {{ limit }} characters long.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $content;

    /**
     * @ORM\Column(type="string", length=25)
     * @var string
     */
    private string $state;

    /**
     * @ORM\ManyToOne(targetEntity=PostsComments::class, inversedBy="replies")
     * @var PostsComments|null
     */
    private ?PostsComments $parentComment = null;

    /**
     * @ORM\OneToMany(targetEntity=PostsComments::class, mappedBy="parentComment")
     * @var ArrayCollection|PostsComments[]
     */
    private $replies;

    /**
     * PostsComments constructor.
     */
    public function __construct()
    {
        $this->replies = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Posts
     */
    public function getPost(): Posts
    {
        return $this->post;
    }

    /**
     * @param Posts $post
     * @return $this
     */
    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Users
     */
    public function getAuthor(): Users
    {
        return $this->author;
    }

    /**
     * @param Users $author
     * @return $this
     */
    public function setAuthor(Users $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return $this|null
     */
    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    /**
     * @param PostsComments|null $parentComment
     * @return $this
     */
    public function setParentComment(?self $parentComment): self
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    /**
     * @param PostsComments $reply
     * @return $this
     */
    public function addReply(self $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParentComment($this);
        }

        return $this;
    }

    /**
     * @param PostsComments $reply
     * @return $this
     */
    public function removeReply(self $reply): self
    {
        if ($this->replies->contains($reply)) {
            $this->replies->removeElement($reply);
            // set the owning side to null (unless already changed)
            if ($reply->getParentComment() === $this) {
                $reply->setParentComment(null);
            }
        }

        return $this;
    }
}
