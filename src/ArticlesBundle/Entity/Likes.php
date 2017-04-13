<?php

namespace ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Likes
 *
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="ArticlesBundle\Repository\LikesRepository")
 */
class Likes
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
	 * @ORM\ManyToOne(targetEntity="ArticlesBundle\Entity\Article", inversedBy="comments")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $article;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $user;

	/**
	 * @return mixed
	 */
	public function getArticle()
	{
		return $this->article;
	}

	/**
	 * @param \ArticlesBundle\Entity\Article $article
	 * @internal param mixed $quotes
	 */
	public function setArticle(Article $article)
	{
		$this->article = $article;
	}

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param \UserBundle\Entity\User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
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

}

