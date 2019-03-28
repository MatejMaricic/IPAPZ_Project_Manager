<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class Project
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
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdat;


    /**
     * @Doctrine\ORM\Mapping\ManyToMany(
     *     targetEntity="App\Entity\ProjectStatus",
     *     inversedBy="projects",
     *     cascade={"persist"}
     *     )
     */
    private $projectStatus;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Task", mappedBy="project", orphanRemoval=true)
     */
    private $task;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\User", mappedBy="project")
     */
    private $users;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $deadline;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    private $completed;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Discussion", mappedBy="project", orphanRemoval=true)
     */
    private $discussions;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\HoursOnTask", mappedBy="project")
     */
    private $hoursOnTasks;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Collaboration", inversedBy="projects")
     */
    private $collaboration;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $addedBy;


    public function __construct()
    {

        $this->projectStatus = new ArrayCollection();
        $this->task = new ArrayCollection();
        $this->developers = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->hoursOnTasks = new ArrayCollection();
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


    public function getProjectStatus()
    {
        return $this->projectStatus;
    }

    public function addProjectStatus(\App\Entity\ProjectStatus $projectStatus): self
    {
        if (!$this->projectStatus->contains($projectStatus)) {
            $this->projectStatus[] = $projectStatus;
        }

        return $this;
    }

    public function removeProjectStatus(\App\Entity\ProjectStatus $projectStatus): self
    {
        if ($this->projectStatus->contains($projectStatus)) {
            $this->projectStatus->removeElement($projectStatus);
        }

        return $this;
    }

    /**
     * @param mixed $projectStatus
     */
    public function setProjectStatus($projectStatus): void
    {
        $this->projectStatus = $projectStatus;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTask(): Collection
    {
        return $this->task;
    }

    public function addTask(\App\Entity\Task $task): self
    {
        if (!$this->task->contains($task)) {
            $this->task[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(\App\Entity\Task $task): self
    {
        if ($this->task->contains($task)) {
            $this->task->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
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

    public function addUser(\App\Entity\User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addProject($this);
        }

        return $this;
    }

    public function removeUser(\App\Entity\User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeProject($this);
        }

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(?bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return Collection|Discussion[]
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(\App\Entity\Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions[] = $discussion;
            $discussion->setProject($this);
        }

        return $this;
    }

    public function removeDiscussion(\App\Entity\Discussion $discussion): self
    {
        if ($this->discussions->contains($discussion)) {
            $this->discussions->removeElement($discussion);
            // set the owning side to null (unless already changed)
            if ($discussion->getProject() === $this) {
                $discussion->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HoursOnTask[]
     */
    public function getHoursOnTasks(): Collection
    {
        return $this->hoursOnTasks;
    }

    public function addHoursOnTask(\App\Entity\HoursOnTask $hoursOnTask): self
    {
        if (!$this->hoursOnTasks->contains($hoursOnTask)) {
            $this->hoursOnTasks[] = $hoursOnTask;
            $hoursOnTask->setProject($this);
        }

        return $this;
    }

    public function removeHoursOnTask(\App\Entity\HoursOnTask $hoursOnTask): self
    {
        if ($this->hoursOnTasks->contains($hoursOnTask)) {
            $this->hoursOnTasks->removeElement($hoursOnTask);
            // set the owning side to null (unless already changed)
            if ($hoursOnTask->getProject() === $this) {
                $hoursOnTask->setProject(null);
            }
        }

        return $this;
    }

    public function getCollaboration()
    {
        return $this->collaboration;
    }

    public function setCollaboration(\App\Entity\Collaboration $collaboration): self
    {
        $this->collaboration = $collaboration;

        return $this;
    }

    public function getAddedBy(): ?int
    {
        return $this->addedBy;
    }

    public function setAddedBy(int $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }
}
