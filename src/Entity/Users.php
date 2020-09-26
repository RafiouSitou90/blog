<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @ORM\Table(name="tab_users")
 * @UniqueEntity(fields={"username"}, message="There is already user with this username")
 * @UniqueEntity(fields={"email"}, message="There is already user with this email")
 * @ORM\HasLifecycleCallbacks()
 */
class Users implements UserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="The username should not be blank")
     * @Assert\NotNull(message="The username should not be null.")
     * @Assert\Length(
     *      min = 8,
     *      max = 180,
     *      minMessage = "The username must be at least {{ limit }} characters long.",
     *      maxMessage = "The username cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="The email should not be blank.")
     * @Assert\NotNull(message="The email should not be null.")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="The firstName should not be blank")
     * @Assert\NotNull(message="The firstName should not be null.")
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your firstName must be at least {{ limit }} characters long.",
     *      maxMessage = "Your firstName cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="The lastName should not be blank")
     * @Assert\NotNull(message="The lastName should not be null.")
     * @Assert\Length(
     *      min = 2,
     *      max = 150,
     *      minMessage = "Your lastName must be at least {{ limit }} characters long.",
     *      maxMessage = "Your lastName cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="author", orphanRemoval=true)
     * @var ArrayCollection|Posts[]
     */
    private $posts;

    /**
     * @ORM\OneToOne(targetEntity=UsersProfiles::class, cascade={"persist", "remove"})
     * @var UsersProfiles|null
     */
    private ?UsersProfiles $profile = null;

    /**
     * @ORM\OneToMany(targetEntity=Ratings::class, mappedBy="author", orphanRemoval=true)
     * @var ArrayCollection|Ratings[]
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity=PostsComments::class, mappedBy="author", orphanRemoval=true)
     * @var ArrayCollection|PostsComments[]
     */
    private $comments;

    /**
     * Users constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @see UserInterface
     * @return string|null
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @codeCoverageIgnore
     *
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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
            $post->setAuthor($this);
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
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return UsersProfiles|null
     */
    public function getProfile(): ?UsersProfiles
    {
        return $this->profile;
    }

    /**
     * @param UsersProfiles|null $profile
     * @return $this
     */
    public function setProfile(?UsersProfiles $profile): self
    {
        $this->profile = $profile;

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
            $rating->setAuthor($this);
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
            if ($rating->getAuthor() === $this) {
                $rating->setAuthor(null);
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
            $comment->setAuthor($this);
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
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
