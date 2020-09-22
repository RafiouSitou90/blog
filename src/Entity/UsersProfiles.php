<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UsersProfilesRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UsersProfilesRepository::class)
 * @ORM\Table(name="tab_users_profiles")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class UsersProfiles
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @var string
     */
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="users_profiles", fileNameProperty="avatarName", size="avatarSize")
     * @var File|null
     */
    private ?File $avatarFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private ?string $avatarName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private ?int $avatarSize = null;

    /**
     * @param File|UploadedFile|null $avatarFile
     *
     * @return $this
     */
    public function setAvatarFile(?File $avatarFile): self
    {
        $this->avatarFile = $avatarFile;
        if (null !== $avatarFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return null|File
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @return string|null
     */
    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * @param string|null $avatarName
     *
     * @return $this
     */
    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    /**
     * @param int|null $avatarSize
     *
     * @return $this
     */
    public function setAvatarSize(?int $avatarSize): self
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }
}
