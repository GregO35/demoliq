<?php

namespace App\Entity;

// le premier use permet de faire les vérifications du fomulaire
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    //Cette fonction sera appelée avant l'INSERT des questions
    // donc on en profite pour renseigner les valeurs par défaut
    /**
     * @ORM\PrePersist()
     *
     */
    public function prePersist()
    {
        $this->setCreationDate(new \DateTime());
        $this->setSupports(0);
        $this->setStatus('debating');
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez poser votre question")
     * @Assert\Length(
     *     min="15",
     *     max="255",
     *     minMessage="15 caractères minimum svp !",
     *     maxMessage="255 caractères maximum svp !"
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    //Assert pour la validation
    private $title;

    /**
     *
     * @Assert\Length(
     *     min="3",
     *     max="50000",
     *     minMessage="3 caractères minimum svp !",
     *     maxMessage="50000 caractères maximum svp !"
     * )
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $supports;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSupports(): ?int
    {
        return $this->supports;
    }

    public function setSupports(int $supports): self
    {
        $this->supports = $supports;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
