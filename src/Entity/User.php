<?php

namespace App\Entity;

use App\Dto\UserDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as JMS;


#[ORM\Entity()]
#[ORM\Table(name: "users")]
#[JMS\ExclusionPolicy("all")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: "bigint")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[JMS\Expose]
    private int $id;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[JMS\Expose]
    #[JMS\SerializedName("first_name")]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[JMS\Expose]
    #[JMS\SerializedName("last_name")]
    private string $surname;

    #[ORM\Column(type: "string", length: 64)]
    private string $password;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[JMS\Expose]
    private string $email;

    #[ORM\Column(type: "user_role")]
    #[JMS\Expose]
    private string $role;

    #[ORM\Column(name: "is_active", type: "boolean")]
    #[JMS\Expose]
    #[JMS\SerializedName("is_active")]
    private bool $isActive;

    /** @var Certificate[] */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: "App\Entity\Certificate")]
    #[JMS\Expose]
    private $certificates;

    public function __construct(string $surname, string $name, string $password, string $email, bool $isActive = true)
    {
        $this->surname = $surname;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->isActive = $isActive;
        $this->role = 'user';
        $this->certificates = new ArrayCollection();
    }

    public static function createFromDto(UserDTO $dto)
    {
        return new self($dto->surname, $dto->name, $dto->password, $dto->email);
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $passwordHash): self
    {
        $this->password = $passwordHash;
        return $this;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCertificates(): Collection
    {
        return $this->certificates;
    }

    public function addCertificate(Certificate $certificate): self
    {
        if (!$this->certificates->contains($certificate)) {
            $this->certificates[] = $certificate;
            $certificate->setUserId($this);
        }

        return $this;
    }

    public function removeCertificate(Certificate $certificate): self
    {
        if ($this->certificates->removeElement($certificate)) {
            // set the owning side to null (unless already changed)
            if ($certificate->getUserId() === $this) {
                $certificate->setUserId(null);
            }
        }

        return $this;
    }


}