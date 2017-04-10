<?php

namespace UserBundle\Controller;

use ArticlesBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/users")
     */
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }
}
