<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\TaskRepository")
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class Task
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Project", inversedBy="task")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdat;


    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\User", mappedBy="task")
     */
    private $users;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\ProjectStatus", inversedBy="tasks")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @Doctrine\ORM\Mapping\Column(type="text")
     */
    private $content;

    /**
     * @Doctrine\ORM\Mapping\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="App\Entity\Comments",
     *     mappedBy="task", orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     */
    private $comments;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $priority;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $completed;

    /**
     * @Doctrine\ORM\Mapping\Column(type="array", nullable=true)
     */
    private $subscribed = [];

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $estimate;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\HoursOnTask", mappedBy="Task")
     */
    private $hoursOnTask;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $totalHours;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    private $updated;


    public function __construct()
    {
        $this->taskStatuses = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->projectStatuses = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->hoursOnTask = new ArrayCollection();
    }


    /**
     * @Doctrine\ORM\Mapping\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdat = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function addUser(\App\Entity\User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addTask($this);
        }

        return $this;
    }

    public function removeUser(\App\Entity\User $user): self
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

    public function addProjectStatus(\App\Entity\ProjectStatus $projectStatus): self
    {
        if (!$this->projectStatuses->contains($projectStatus)) {
            $this->projectStatuses[] = $projectStatus;
        }

        return $this;
    }

    public function removeProjectStatus(\App\Entity\ProjectStatus $projectStatus): self
    {
        if ($this->projectStatuses->contains($projectStatus)) {
            $this->projectStatuses->removeElement($projectStatus);
        }

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(\App\Entity\ProjectStatus $status): self
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

    public function addComment(\App\Entity\Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTask($this);
        }

        return $this;
    }

    public function removeComment(\App\Entity\Comments $comment): self
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
        return $this->subscribed;
    }

    public function setSubscribed(?array $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function addSubscribed(?array $subscribed): self
    {
        $this->subscribed[] = $subscribed;

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

    public function addHoursOnTask(\App\Entity\HoursOnTask $hoursOnTask): self
    {
        if (!$this->hoursOnTask->contains($hoursOnTask)) {
            $this->hoursOnTask[] = $hoursOnTask;
            $hoursOnTask->setTask($this);
        }

        return $this;
    }

    public function removeHoursOnTask(\App\Entity\HoursOnTask $hoursOnTask): self
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

    public function getUpdated(): ?bool
    {
        return $this->updated;
    }

    public function setUpdated(?bool $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
