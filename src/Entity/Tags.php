<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\TagsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TagsRepository::class)
 * @ORM\Table(name="tab_tags")
 * @UniqueEntity(fields={"name"}, message="There is already tag with this name")
 * @ORM\HasLifecycleCallbacks()
 */
class Tags
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
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="The name should not be blank")
     * @Assert\NotNull(message="The name should not be null.")
     * @Assert\Length(
     *      min = 4,
     *      max = 50,
     *      minMessage = "The name must be at least {{ limit }} characters long.",
     *      maxMessage = "The name cannot be longer than {{ limit }} characters.",
     *      allowEmptyString = false
     * )
     * @var string
     */
    private string $name;

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
     * @codeCoverageIgnore
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
