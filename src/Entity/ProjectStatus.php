<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\ProjectStatusRepository")
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class ProjectStatus
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(
     *     targetEntity="App\Entity\project",
     *     mappedBy="projectStatus",
     *     cascade={"persist"}
     *     )
     */
    private $projects;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdat;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Task", mappedBy="status")
     */
    private $tasks;


    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @Doctrine\ORM\Mapping\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdat = new \DateTime('now');
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
            $project->addProjectStatus($this);
        }

        return $this;
    }

    public function removeProject(\App\Entity\Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeProjectStatus($this);
        }

        return $this;
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

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeInterface $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(\App\Entity\Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->addProjectStatus($this);
        }

        return $this;
    }

    public function removeTask(\App\Entity\Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            $task->removeProjectStatus($this);
        }

        return $this;
    }
}
