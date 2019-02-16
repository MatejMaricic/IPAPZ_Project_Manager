<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\project", inversedBy="task")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdat;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TaskStatus", mappedBy="tasks")
     */
    private $taskStatuses;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="task")
     */
    private $users;

    public function __construct()
    {
        $this->taskStatuses = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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
     * @return Collection|TaskStatus[]
     */
    public function getTaskStatuses(): Collection
    {
        return $this->taskStatuses;
    }

    public function addTaskStatus(TaskStatus $taskStatus): self
    {
        if (!$this->taskStatuses->contains($taskStatus)) {
            $this->taskStatuses[] = $taskStatus;
            $taskStatus->addTask($this);
        }

        return $this;
    }

    public function removeTaskStatus(TaskStatus $taskStatus): self
    {
        if ($this->taskStatuses->contains($taskStatus)) {
            $this->taskStatuses->removeElement($taskStatus);
            $taskStatus->removeTask($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addTask($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTask($this);
        }

        return $this;
    }
}
