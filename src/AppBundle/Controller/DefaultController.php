<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
	    if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
		    return $this->redirectToRoute('login');
	    }
	    $user = $this->get('security.token_storage')->getToken()->getUser();

	    $articles   = $this->getDoctrine()->getRepository('ArticlesBundle:Article')->findAll();
	    $categories = $this->getDoctrine()->getRepository('ArticlesBundle:Category')->findBy(array(), array('name'=>'ASC'));

	    return array(
		    'articles' => $articles,
		    'categories' => $categories
	    );
    }

	/**
	 * @Route("/views/{viewId}", name="view_template")
	 */
	public function getViewAction($viewId){
		return $this->render('AppBundle:AngularViews:'.$viewId);
	}

}
