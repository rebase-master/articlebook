<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Template()
     */
    public function indexAction(Request $request)
    {
	    if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
		    return $this->redirectToRoute('login');
	    }
	    $user = $this->get('security.token_storage')->getToken()->getUser();
	    $users = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();

	    return array(
		    'users' => $users,
		    'activeCategory' => 'users'
	    );
    }


}
