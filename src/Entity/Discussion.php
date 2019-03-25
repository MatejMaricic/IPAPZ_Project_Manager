<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\DiscussionRepository")
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class Discussion
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Project", inversedBy="discussions")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $createdBy;

    /**
     * @Doctrine\ORM\Mapping\Column(type="text")
     */
    private $content;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Comments", mappedBy="discussion", orphanRemoval=false)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @Doctrine\ORM\Mapping\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(\App\Entity\Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(\App\Entity\Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setDiscussion($this);
        }

        return $this;
    }

    public function removeComment(\App\Entity\Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getDiscussion() === $this) {
                $comment->setDiscussion(null);
            }
        }

        return $this;
    }
}
