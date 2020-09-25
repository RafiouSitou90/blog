<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PostsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 * @ORM\Table(name="tab_posts")
 * @UniqueEntity(fields={"slug"}, message="There is already post with this slug")
 * @ORM\HasLifecycleCallbacks()
 */
class Posts
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
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank(message="The title should not be blank")
     * @Assert\NotNull(message="The title should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 255,
     *      minMessage = "The title must be at least {{ limit }} characters long.",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The summary should not be blank")
     * @Assert\NotNull(message="The summary should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 255,
     *      minMessage = "The summary must be at least {{ limit }} characters long.",
     *      maxMessage = "The summary cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $summary;

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
     * @ORM\Column(type="string", length=25)
     * @var string
     */
    private string $commentState;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $publishedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=PostMedias::class, mappedBy="post")
     * @var ArrayCollection|PostMedias[]
     */
    private $medias;

    /**
     * @ORM\ManyToMany(targetEntity=Tags::class, cascade={"persist"})
     * @ORM\JoinTable(name="tab_posts_tags")
     * @ORM\OrderBy({"name": "ASC"})
     * @var ArrayCollection|Tags[]
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="posts")
     * @var Categories|null
     */
    private ?Categories $category = null;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @var Users
     */
    private Users $author;

    /**
     * @ORM\OneToMany(targetEntity=Ratings::class, mappedBy="post", orphanRemoval=true)
     * @var ArrayCollection|Ratings[]
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity=PostsComments::class, mappedBy="post", orphanRemoval=true)
     * @var ArrayCollection|PostsComments[]
     */
    private $comments;

    /**
     * Posts constructor.
     */
    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

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
     * @return string
     */
    public function getCommentState(): string
    {
        return $this->commentState;
    }

    /**
     * @param string $commentState
     * @return $this
     */
    public function setCommentState(string $commentState): self
    {
        $this->commentState = $commentState;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTimeInterface $publishedAt
     * @return $this
     */
    public function setPublishedAt(DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection|PostMedias[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    /**
     * @param PostMedias $media
     * @return $this
     */
    public function addMedia(PostMedias $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setPost($this);
        }

        return $this;
    }

    /**
     * @param PostMedias $media
     * @return $this
     */
    public function removeMedia(PostMedias $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            // set the owning side to null (unless already changed)
            if ($media->getPost() === $this) {
                $media->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tags[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tags $tag
     * @return $this
     */
    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tags $tag
     * @return $this
     */
    public function removeTag(Tags $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Categories|null
     */
    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    /**
     * @param Categories|null $category
     * @return $this
     */
    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

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
     * @return Collection|Ratings[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    /**
     * @param Ratings $rating
     * @return $this
     */
    public function addRating(Ratings $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setPost($this);
        }

        return $this;
    }

    /**
     * @param Ratings $rating
     * @return $this
     */
    public function removeRating(Ratings $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getPost() === $this) {
                $rating->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostsComments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param PostsComments $comment
     * @return $this
     */
    public function addComment(PostsComments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * @param PostsComments $comment
     * @return $this
     */
    public function removeComment(PostsComments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @param SluggerInterface $slugger
     * @return void
     */
    public function computeSlug(SluggerInterface $slugger): void
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }
}
