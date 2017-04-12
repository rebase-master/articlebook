<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_index")
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

	/**
	 * Deletes a user entity.
	 *
	 * @Route("/admin/user/{id}", name="user_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, User $user)
	{
		$form = $this->createDeleteForm($user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($user);
			$em->flush();
		}

		return $this->redirectToRoute('admin_index');
	}

	/**
	 * Creates a form to delete a user entity.
	 *
	 * @param User $user The user entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(User $user)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
			->setMethod('DELETE')
			->getForm()
			;
	}


}
