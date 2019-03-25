<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Task", inversedBy="comments")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    private $task;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\Column(type="text")
     */
    private $content;

    /**
     * @Doctrine\ORM\Mapping\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Discussion", inversedBy="comments", cascade={"persist"})
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    private $discussion;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\App\Entity\User $user): self
    {
        $this->user = $user;

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

    public function getDiscussion()
    {
        return $this->discussion;
    }

    public function setDiscussion(\App\Entity\Discussion $discussion): self
    {
        $this->discussion = $discussion;

        return $this;
    }
}
