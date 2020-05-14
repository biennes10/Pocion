<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email","username"},
 *     message="L'email ou le nom d'utilisateur existe déjà"
 * )
 */
class User implements UserInterface
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
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Handrail", mappedBy="user")
     */
    private $handrails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectUser", mappedBy="user")
     */
    private $projectUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="user")
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agenda", mappedBy="user")
     */
    private $agendas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="author", orphanRemoval=true)
     */
    private $leadingProjects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NotifUser", mappedBy="user")
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    private $notifUsers;



    public function __construct()
    {
        $this->handrails = new ArrayCollection();
        $this->projectUsers = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->agendas = new ArrayCollection();
        $this->leadingProjects = new ArrayCollection();
        $this->notifUsers = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
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
            $handrail->setUser($this);
        }

        return $this;
    }

    public function removeHandrail(Handrail $handrail): self
    {
        if ($this->handrails->contains($handrail)) {
            $this->handrails->removeElement($handrail);
            // set the owning side to null (unless already changed)
            if ($handrail->getUser() === $this) {
                $handrail->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectUser[]
     */
    public function getProjectUsers(): Collection
    {
        return $this->projectUsers;
    }

    public function addProjectUser(ProjectUser $projectUser): self
    {
        if (!$this->projectUsers->contains($projectUser)) {
            $this->projectUsers[] = $projectUser;
            $projectUser->setUser($this);
        }

        return $this;
    }

    public function removeProjectUser(ProjectUser $projectUser): self
    {
        if ($this->projectUsers->contains($projectUser)) {
            $this->projectUsers->removeElement($projectUser);
            // set the owning side to null (unless already changed)
            if ($projectUser->getUser() === $this) {
                $projectUser->setUser(null);
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
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getUser() === $this) {
                $file->setUser(null);
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
            $agenda->setUser($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agendas->contains($agenda)) {
            $this->agendas->removeElement($agenda);
            // set the owning side to null (unless already changed)
            if ($agenda->getUser() === $this) {
                $agenda->setUser(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getLeadingProjects(): Collection
    {
        return $this->leadingProjects;
    }

    public function addLeadingProject(Project $leadingProject): self
    {
        if (!$this->leadingProjects->contains($leadingProject)) {
            $this->leadingProjects[] = $leadingProject;
            $leadingProject->setAuthor($this);
        }

        return $this;
    }

    public function removeLeadingProject(Project $leadingProject): self
    {
        if ($this->leadingProjects->contains($leadingProject)) {
            $this->leadingProjects->removeElement($leadingProject);
            // set the owning side to null (unless already changed)
            if ($leadingProject->getAuthor() === $this) {
                $leadingProject->setAuthor(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|NotifUser[]
     */
    public function getNotifUsers(): Collection
    {
        return $this->notifUsers;
    }

    /**
     * @return Collection|NotifUser[]
     */
    public function getNotifUsersDesc(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gte(''))
            ->orderBy(['createdAt', 'DESC']);
        return $this->notifUsers->matching($criteria);
    }

    public function addNotifUser(NotifUser $notifUser): self
    {
        if (!$this->notifUsers->contains($notifUser)) {
            $this->notifUsers[] = $notifUser;
            $notifUser->setUser($this);
        }

        return $this;
    }

    public function removeNotifUser(NotifUser $notifUser): self
    {
        if ($this->notifUsers->contains($notifUser)) {
            $this->notifUsers->removeElement($notifUser);
            // set the owning side to null (unless already changed)
            if ($notifUser->getUser() === $this) {
                $notifUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NotifUser[]
     */
    public function getNbUnopenedNotif(): Collection
    {

        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gte('opened', 1));
        return $this->notifUsers->matching($criteria);
    }

}
