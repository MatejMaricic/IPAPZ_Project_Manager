<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectManager", inversedBy="project")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectManager;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectManager(): ?ProjectManager
    {
        return $this->projectManager;
    }

    public function setProjectManager(?ProjectManager $projectManager): self
    {
        $this->projectManager = $projectManager;

        return $this;
    }
}
