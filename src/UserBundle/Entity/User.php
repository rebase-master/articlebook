<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
class User implements UserInterface, AdvancedUserInterface, \Serializable
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
	 * @Assert\NotBlank()
	 * @Assert\Length(max=4096)
	 */
	private $plainPassword;

	/**
	 * The below length depends on the "algorithm" you use for encoding
	 * the password, but this works well with bcrypt.
	 *
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;

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

	public function __construct(){
		$this->articles   = new ArrayCollection();
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

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword($password)
	{
		$this->plainPassword = $password;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;
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
	 * @return string
	 */
	public function setRegistrationKey()
	{
		$this->registrationKey = $this->generate_random_string(10).'-'.md5(date('s'));
		return $this;
	}

	/**
	 * Return the randomly generated seed for registration key
	 *
	 * @param int $length
	 * @return string
	 */
	public static function generate_random_string($length=32){
		//Allowed random string characters
		$seeds='abcdefghijklmnopqrstuvwxyz0123456789';

		//generate the random string
		$str="";
		$count=strlen($seeds);
		for($i=0;$i<$length;$i++){
			$str.=$seeds[mt_rand(0,$count-1)];
		}
		return $str;
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

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials()
	{
		// TODO: Implement eraseCredentials() method.
	}

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * This can return null if the password was not encoded using a salt.
	 *
	 * @return string|null The salt
	 */
	public function getSalt()
	{
		return null;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 */
	public function serialize()
	{
		// TODO: Implement serialize() method.
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Constructs the object
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized <p>
	 * The string representation of the object.
	 * </p>
	 * @return void
	 */
	public function unserialize($serialized)
	{
		// TODO: Implement unserialize() method.
	}

	/**
	 * Checks whether the user's account has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw an AccountExpiredException and prevent login.
	 *
	 * @return bool true if the user's account is non expired, false otherwise
	 *
	 * @see AccountExpiredException
	 */
	public function isAccountNonExpired()
	{
		// TODO: Implement isAccountNonExpired() method.
	}

	/**
	 * Checks whether the user is locked.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a LockedException and prevent login.
	 *
	 * @return bool true if the user is not locked, false otherwise
	 *
	 * @see LockedException
	 */
	public function isAccountNonLocked()
	{
		// TODO: Implement isAccountNonLocked() method.
	}

	/**
	 * Checks whether the user's credentials (password) has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a CredentialsExpiredException and prevent login.
	 *
	 * @return bool true if the user's credentials are non expired, false otherwise
	 *
	 * @see CredentialsExpiredException
	 */
	public function isCredentialsNonExpired()
	{
		// TODO: Implement isCredentialsNonExpired() method.
	}

	/**
	 * Checks whether the user is enabled.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a DisabledException and prevent login.
	 *
	 * @return bool true if the user is enabled, false otherwise
	 *
	 * @see DisabledException
	 */
	public function isEnabled()
	{
		// TODO: Implement isEnabled() method.
	}
}

