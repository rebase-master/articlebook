<?php

namespace UserBundle\Entity;

use AppBundle\Util\StringOperations;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email already exists!")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="username", type="string", length=15)
	 */
	private $username;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="gender", type="boolean")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="salt", type="string", length=255, nullable=true)
	 */
	private $salt;

	/**
	 * @var array
	 *
	 * @ORM\Column(name="roles", type="array")
	 */
	private $roles = array();
	/**
	 * @var bool
	 *
	 * @ORM\Column(name="isActive", type="boolean", nullable=true)
	 */
	private $isActive = true;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="isBlocked", type="boolean", nullable=true)
	 */
	private $isBlocked = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="registrationKey", type="string", length=255)
	 */
	private $registrationKey;

	/**
	 * @var \Datetime $createdAt
	 *
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(name="createdAt", type="datetime")
	 */
	private $createdAt;

	/**
	 * @var \Datetime $updatedAt
	 *
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(name="updatedAt", type="datetime")
	 */
	private $updatedAt;

	/**
	 * @ORM\OneToMany(targetEntity="ArticlesBundle\Entity\Article", mappedBy="user")
	 * @ORM\OrderBy({"created" = "DESC"})
	 *
	 */
	private $articles;

	/**
	 * @var string
	 *
	 * @ORM\OneToMany(targetEntity="ArticlesBundle\Entity\Category", mappedBy="user")
	 * @ORM\OrderBy({"created" = "DESC"})
	 */
	private $interests;

	public function __construct(){
		$this->salt     = base_convert(sha1(uniqid(mt_rand(),true)),16,36);
		$this->articles   = new ArrayCollection();
		$this->interests   = new ArrayCollection();
	}


	/**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set interests
     *
     * @param string $interests
     *
     * @return User
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;

        return $this;
    }

    /**
     * Get interests
     *
     * @return string
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

	/**
	 * Get salt
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Get the role for the current user
	 *
	 * @return array
	 */
	public function getRoles()
	{
		$roles = $this->roles;
		return array_unique($roles);
	}

	/**
	 * Set the roles
	 *
	 * @param array $roles
	 */
	public function setRoles(array $roles = ['ROLE_USER']){
		$this->roles = $roles;
	}

    /**
     * Set isActive
     *
     * @param string $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isBlocked
     *
     * @param string $isBlocked
     *
     * @return User
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return string
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

	/**
	 * Set registrationKey
	 *
	 * @param string $registrationKey
	 * @return User
	 */
	public function setRegistrationKey($userId)
	{
		$this->registrationKey = StringOperations::generate_random_string(10).'-'.md5($userId);
		return $this;
	}

	/**
	 * Get registrationKey
	 *
	 * @return string
	 */
	public function getRegistrationKey()
	{
		return $this->registrationKey;
	}

	/**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}
}

