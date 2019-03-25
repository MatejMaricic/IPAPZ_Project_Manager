<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\SubscriptionsRepository")
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(
 *     name="subscriptions",
 *     uniqueConstraints={@Doctrine\ORM\Mapping\UniqueConstraint(
 *     name="subscriptions",
 *      columns={"user_email", "task_id"}
 *     )})

 * @Doctrine\ORM\Mapping\Table(
 *     name="subscriptions",uniqueConstraints={@Doctrine\ORM\Mapping\UniqueConstraint(
 *     name="subscriptions",
 *      columns={"user_email", "discussion_id"}
 *     )})
 **/
class Subscriptions
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
    private $userEmail;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $taskId;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
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
