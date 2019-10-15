<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank(message="The login must be defined.")
     * @Assert\NotNull(message="The login can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     *      min = 3,
     *      max = 18,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex("#[A-Za-z0-9]#")
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="The password must be defined.")
     * @Assert\NotNull(message="The password can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     *      min = 12,
     *      minMessage = "Your first name must be at least {{ limit }} characters long"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="The email must be defined.")
     * @Assert\NotNull(message="The email can't be null.")
     * @Assert\Email(message="The email '{{ value }}' is not a valid email.")
     */
    private $mail;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="The date of birth must be defined.")
     * @Assert\NotNull(message="The date of birth can't be null.")
     * @Assert\Date
     * @var string A "d-m-Y" formatted value
     */
    private $birth_date;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="The date of inscription must be defined.")
     * @Assert\NotNull(message="The date of inscription can't be null.")
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     * @Assert\GreaterThanOrEqual("-16 years")
     */
    private $inscription_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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
        return (string) $this->password;
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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getInscriptionDate(): ?\DateTimeInterface
    {
        return $this->inscription_date;
    }

    public function setInscriptionDate(\DateTimeInterface $inscription_date): self
    {
        $this->inscription_date = $inscription_date;


        return $this;
    }
}
