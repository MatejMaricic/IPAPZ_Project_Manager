<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\HoursOnTaskRepository")
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class HoursOnTask
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="hoursOnTasks")
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Task", inversedBy="hoursOnTask")
     */
    private $task;

    /**
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    private $billable;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $addedAt;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $hours;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Project", inversedBy="hoursOnTasks")
     */
    private $project;


    /**
     * @Doctrine\ORM\Mapping\PrePersist
     */
    public function setAddedAtValue()
    {
        $this->addedAt = new \DateTime();
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

    public function getTask()
    {
        return $this->task;
    }

    public function setTask(\App\Entity\Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getBillable(): ?bool
    {
        return $this->billable;
    }

    public function setBillable(?bool $billable): self
    {
        $this->billable = $billable;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;

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
}
