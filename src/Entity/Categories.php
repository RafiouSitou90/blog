<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 * @ORM\Table(name="tab_categories")
 * @UniqueEntity(fields={"name"}, message="There is already category with this name")
 * @ORM\HasLifecycleCallbacks()
 */
class Categories
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
     * @Assert\NotBlank(message="The name should not be blank")
     * @Assert\NotNull(message="The name should not be null.")
     * @Assert\Length(
     *      min = 3,
     *      max = 200,
     *      minMessage = "The name must be at least {{ limit }} characters long.",
     *      maxMessage = "The name cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private string $slug = '';

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="category")
     * @var ArrayCollection|Posts[]
     */
    private $posts;

    /**
     * Categories constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @param Posts $post
     * @return $this
     */
    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Posts $post
     * @return $this
     */
    public function removePost(Posts $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
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
            $this->slug = (string) $slugger->slug((string) $this->getName())->lower();
        }
    }
}
