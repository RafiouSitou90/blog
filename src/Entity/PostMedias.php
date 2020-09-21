<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PostMediasRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PostMediasRepository::class)
 * @ORM\Table(name="tab_post_medias")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class PostMedias
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="post_medias", fileNameProperty="mediaName", size="mediaSize")
     * @var File|UploadedFile|null
     */
    private ?File $mediaFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private ?string $mediaName =  null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private ?int $mediaSize = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return File|UploadedFile|null
     */
    public function getMediaFile(): ?File
    {
        return $this->mediaFile;
    }

    /**
     * @param File $mediaFile
     * @return $this
     */
    public function setMediaFile(File $mediaFile): self
    {
        $this->mediaFile = $mediaFile;
        if (null !== $mediaFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }

    /**
     * @param string $mediaName
     * @return $this
     */
    public function setMediaName(string $mediaName): self
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMediaSize(): ?int
    {
        return $this->mediaSize;
    }

    /**
     * @param int $mediaSize
     * @return $this
     */
    public function setMediaSize(int $mediaSize): self
    {
        $this->mediaSize = $mediaSize;

        return $this;
    }
}
