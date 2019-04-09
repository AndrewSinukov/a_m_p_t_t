<?php

namespace App\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 *
 * @ORM\Table(
 *     name="profiles",
 *     indexes={@ORM\Index(name="firstname", columns={"first_name"})},
 *     indexes={@ORM\Index(name="lastname", columns={"last_name"})},
 *     indexes={@ORM\Index(name="phonenumber", columns={"phone_number"})},
 *     )
 * @ORM\Entity(repositoryClass="App\ProfileBundle\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Length(max="128")
     *
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=128)
     */
    private $firstname;

    /**
     * @Assert\Length(max="128")
     *
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=128)
     */
    private $lastname;

    /**
     * @ORM\Column(name="phone_number", type="string")
     *
     * @var string
     */
    protected $phonenumber;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Profile
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Profile
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    /**
     * @param string $phonenumber
     * @return Profile
     */
    public function setPhonenumber(string $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }
}