<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Tests\A;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdat;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProjectStatus", inversedBy="projects", cascade={"persist"})
     */
    private $projectStatus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project", orphanRemoval=true)
     */
    private $task;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="project")
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deadline;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $completed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discussion", mappedBy="project", orphanRemoval=true)
     */
    private $discussions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HoursOnTask", mappedBy="project")
     */
    private $hoursOnTasks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Collaboration", inversedBy="projects")
     */
    private $collaboration;


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
     * @ORM\PrePersist()
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

    public function addProjectStatus(ProjectStatus $projectStatus): self
    {
        if (!$this->projectStatus->contains($projectStatus)) {
            $this->projectStatus[] = $projectStatus;
        }

        return $this;
    }

    public function removeProjectStatus(ProjectStatus $projectStatus): self
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

    public function addTask(Task $task): self
    {
        if (!$this->task->contains($task)) {
            $this->task[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
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

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addProject($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
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

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions[] = $discussion;
            $discussion->setProject($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
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

    public function addHoursOnTask(HoursOnTask $hoursOnTask): self
    {
        if (!$this->hoursOnTasks->contains($hoursOnTask)) {
            $this->hoursOnTasks[] = $hoursOnTask;
            $hoursOnTask->setProject($this);
        }

        return $this;
    }

    public function removeHoursOnTask(HoursOnTask $hoursOnTask): self
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

    public function getCollaboration(): ?Collaboration
    {
        return $this->collaboration;
    }

    public function setCollaboration(?Collaboration $collaboration): self
    {
        $this->collaboration = $collaboration;

        return $this;
    }
}
