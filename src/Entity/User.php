<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Hotel;
use App\Enum\UserEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string|null $address;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private \DateTimeInterface $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string|null $bio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string|null $profilePicture;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isActive;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private \DateTimeInterface $registrationDate;

    /**
     * @ORM\OneToMany(targetEntity="Hotel", mappedBy="owner")
     */
    private $ownedHotels;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     */

    private Hotel $hotel;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    private string $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    public function getRoles(): array
    {
        $roles = [];

        foreach ($this->roles as $role) {
            $roles[] = 'ROLE_' . $role;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isOwner(): bool
    {
        return in_array(UserEnum::OWNER_ROLE, $this->roles);
    }

    public function isManager(): bool
    {
        return in_array(UserEnum::MANAGER_ROLE, $this->roles);
    }

    public function isEmployee(): bool
    {
        return in_array(UserEnum::EMPLOYEE_ROLE, $this->roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string|null $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string|null $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getProfilePicture(): string|null
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string|null $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRegistrationDate(): \DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function getRole(): string
    {
        return $this->role ?? '';
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getOwnedHotels()
    {
        return $this->ownedHotels;
    }

    public function addOwnedHotel(Hotel $hotel): void
    {
        $this->ownedHotels[] = $hotel;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(Hotel $hotel): void
    {
        $this->hotel = $hotel;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
