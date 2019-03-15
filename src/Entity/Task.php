<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="task")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectStatus", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="task", orphanRemoval=true, cascade={"persist"})
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priority;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $Subscribed = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $estimate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HoursOnTask", mappedBy="Task")
     */
    private $hoursOnTask;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalHours;




    public function __construct()
    {
        $this->taskStatuses = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->projectStatuses = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->hoursOnTask = new ArrayCollection();

    }


    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdat = new \DateTime('now');
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

    /**
     * @return Collection|ProjectStatus[]
     */
    public function getProjectStatuses(): Collection
    {
        return $this->projectStatuses;
    }

    public function addProjectStatus(ProjectStatus $projectStatus): self
    {
        if (!$this->projectStatuses->contains($projectStatus)) {
            $this->projectStatuses[] = $projectStatus;
        }

        return $this;
    }

    public function removeProjectStatus(ProjectStatus $projectStatus): self
    {
        if ($this->projectStatuses->contains($projectStatus)) {
            $this->projectStatuses->removeElement($projectStatus);
        }

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): self
    {
        $this->status = $status;

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

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTask($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTask() === $this) {
                $comment->setTask(null);
            }
        }

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getSubscribed(): ?array
    {
        return $this->Subscribed;
    }

    public function setSubscribed(?array $Subscribed): self
    {
        $this->Subscribed = $Subscribed;

        return $this;
    }

    public function addSubscribed(?array $Subscribed): self
    {
        $this->Subscribed[] = $Subscribed;

        return $this;
    }

    public function getEstimate(): ?int
    {
        return $this->estimate;
    }

    public function setEstimate(?int $estimate): self
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * @return Collection|HoursOnTask[]
     */
    public function getHoursOnTask(): Collection
    {
        return $this->hoursOnTask;
    }

    public function addHoursOnTask(HoursOnTask $hoursOnTask): self
    {
        if (!$this->hoursOnTask->contains($hoursOnTask)) {
            $this->hoursOnTask[] = $hoursOnTask;
            $hoursOnTask->setTask($this);
        }

        return $this;
    }

    public function removeHoursOnTask(HoursOnTask $hoursOnTask): self
    {
        if ($this->hoursOnTask->contains($hoursOnTask)) {
            $this->hoursOnTask->removeElement($hoursOnTask);
            // set the owning side to null (unless already changed)
            if ($hoursOnTask->getTask() === $this) {
                $hoursOnTask->setTask(null);
            }
        }

        return $this;
    }

    public function getTotalHours(): ?int
    {
        return $this->totalHours;
    }

    public function setTotalHours(?int $totalHours): self
    {
        $this->totalHours = $totalHours;

        return $this;
    }

}
