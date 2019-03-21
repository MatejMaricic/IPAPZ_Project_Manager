<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionsRepository")
 * @Entity
 * @Table(name="subscriptions",uniqueConstraints={@UniqueConstraint(name="subscriptions",
 *      columns={"user_email", "task_id"})})

 * @Table(name="subscriptions",uniqueConstraints={@UniqueConstraint(name="subscriptions",
 *      columns={"user_email", "discussion_id"})})
 **/
class Subscriptions
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
    private $userEmail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $taskId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discussionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    public function setTaskId(int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }

    public function getDiscussionId(): ?int
    {
        return $this->discussionId;
    }

    public function setDiscussionId(?int $discussionId): self
    {
        $this->discussionId = $discussionId;

        return $this;
    }
}
