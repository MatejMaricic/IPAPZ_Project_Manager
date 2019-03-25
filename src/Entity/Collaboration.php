<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\CollaborationRepository")
 */
class Collaboration
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="collaboration",
     *     cascade={"persist", "remove"}
     *     )
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $subscribed;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $subscribedUntil;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Project", mappedBy="collaboration")
     */
    private $projects;


    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\App\Entity\User $user): self
    {
        $this->user = $user;

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

    public function getSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function getSubscribedUntil(): ?\DateTimeInterface
    {
        return $this->subscribedUntil;
    }

    public function setSubscribedUntil(\DateTimeInterface $subscribedUntil): self
    {
        $this->subscribedUntil = $subscribedUntil;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(\App\Entity\Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setCollaboration($this);
        }

        return $this;
    }

    public function removeProject(\App\Entity\Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getCollaboration() === $this) {
                $project->setCollaboration(null);
            }
        }

        return $this;
    }
}
