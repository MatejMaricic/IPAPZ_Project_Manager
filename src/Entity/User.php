<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\UserRepository")
 * @Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity
 * (
 *     fields={"email"}, message="There is already an account with this email"
 * )
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Doctrine\ORM\Mapping\Column(type="json")
     */
    private $roles = [];

    /**
     * @var                       string The hashed password
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $password;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\Project", inversedBy="users")
     */
    private $project;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\Task", inversedBy="users")
     */
    private $task;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Comments", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $addedBy;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\HoursOnTask", mappedBy="user")
     */
    private $hoursOnTasks;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(
     *     targetEntity="App\Entity\Collaboration",
     *     mappedBy="user",
     *     cascade={"persist", "remove"}
     *     )
     */
    private $collaboration;


    public function __construct()
    {
        $this->project = new ArrayCollection();
        $this->task = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->hoursOnTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @Doctrine\ORM\Mapping\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @Doctrine\ORM\Mapping\PrePersist
     */
    public function setRolesValue()
    {
        if (!$this->roles) {
            $this->roles = ['ROLE_MANAGER'];
        }
    }

    /**
     * @Doctrine\ORM\Mapping\PrePersist
     */
    public function setFullNameValue()
    {
        $this->fullName = $this->firstName . " " . $this->lastName;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(\App\Entity\Project $project): self
    {
        if (!$this->project->contains($project)) {
            $this->project[] = $project;
        }

        return $this;
    }

    public function removeProject(\App\Entity\Project $project): self
    {
        if ($this->project->contains($project)) {
            $this->project->removeElement($project);
        }

        return $this;
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
        }

        return $this;
    }

    public function removeTask(\App\Entity\Task $task): self
    {
        if ($this->task->contains($task)) {
            $this->task->removeElement($task);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(\App\Entity\Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

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
            $hoursOnTask->setUser($this);
        }

        return $this;
    }

    public function removeHoursOnTask(\App\Entity\HoursOnTask $hoursOnTask): self
    {
        if ($this->hoursOnTasks->contains($hoursOnTask)) {
            $this->hoursOnTasks->removeElement($hoursOnTask);
            // set the owning side to null (unless already changed)
            if ($hoursOnTask->getUser() === $this) {
                $hoursOnTask->setUser(null);
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

        // set the owning side of the relation if necessary
        if ($this !== $collaboration->getUser()) {
            $collaboration->setUser($this);
        }

        return $this;
    }
}
