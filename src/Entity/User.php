<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use function in_array;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email is already taken.")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    private const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Email(strict=true)
     *
     * @ORM\Column(name="email", type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @Assert\NotNull(groups={"create"})
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your password must be at least {{ limit }} characters long.",
     * )
     */
    protected $plainPassword;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    protected $roles = [self::ROLE_DEFAULT];

    public static function create(string $email, string $password, string $role): self
    {
        $user = new self();

        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->addRole($role);

        return $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if ($role === self::ROLE_DEFAULT) {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (($key = array_search(strtoupper($role), $this->roles, true)) !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    public function serialize()
    {
        return serialize([
            $this->password,
            $this->id,
            $this->email,
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->password,
            $this->id,
            $this->email,
            ) = unserialize($serialized, ['allowed_classes' => [self::class]]);
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->email !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function __toString(): string
    {
        return $this->email ?? 'User';
    }
}
