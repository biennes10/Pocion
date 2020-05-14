<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Handrail", mappedBy="link")
     */
    private $handrails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agenda", mappedBy="link")
     */
    private $agendas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="link")
     */
    private $files;

    public function __construct()
    {
        $this->handrails = new ArrayCollection();
        $this->agendas = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Handrail[]
     */
    public function getHandrails(): Collection
    {
        return $this->handrails;
    }

    public function addHandrail(Handrail $handrail): self
    {
        if (!$this->handrails->contains($handrail)) {
            $this->handrails[] = $handrail;
            $handrail->setLink($this);
        }

        return $this;
    }

    public function removeHandrail(Handrail $handrail): self
    {
        if ($this->handrails->contains($handrail)) {
            $this->handrails->removeElement($handrail);
            // set the owning side to null (unless already changed)
            if ($handrail->getLink() === $this) {
                $handrail->setLink(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Agenda[]
     */
    public function getAgendas(): Collection
    {
        return $this->agendas;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->agendas->contains($agenda)) {
            $this->agendas[] = $agenda;
            $agenda->setLink($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->contains($agenda)) {
            $this->agendas->removeElement($agenda);
            // set the owning side to null (unless already changed)
            if ($agenda->getLink() === $this) {
                $agenda->setLink(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setLink($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getLink() === $this) {
                $file->setLink(null);
            }
        }

        return $this;
    }
}
