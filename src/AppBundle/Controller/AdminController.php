<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		    'activeCategory' => 'users',
	    );
    }

	/**
	 * Activate/Deactivate User
	 *
	 * @Route("/admin/users/{id}/activate", name="user_activate")
	 */
	public function activateAction($id, Request $request)
	{
			$mode = intval($request->get('mode'));
			/**
			 * @var \UserBundle\Entity\User $user
			 */
			$user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('id' => $id));

			if($user){
				$em = $this->getDoctrine()->getManager();
				if($mode == 1){
					$user->setIsActive(true);
				}else{
					$user->setIsActive(false);
				}
				$em->persist($user);
				$em->flush();

				return new JsonResponse(
					array(
						'status' => 'SUCCESS',
						'code'   => 1
					)
				);
			}else{
				return new JsonResponse(
					array(
						'status' => 'ERROR',
						'code'   => -1
					)
				);
			}
	}//activate

	/**
	 * Block/Unblock User
	 *
	 * @Route("/admin/users/{id}/block", name="user_block")
	 */
	public function blockAction($id, Request $request)
	{
			$mode = intval($request->get('mode'));
			/**
			 * @var \UserBundle\Entity\User $user
			 */
			$user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('id' => $id));

			if($user){
				$em = $this->getDoctrine()->getManager();
				if($mode == 1){
					$user->setIsBlocked(true);
				}else{
					$user->setIsBlocked(false);
				}
				$em->persist($user);
				$em->flush();

				return new JsonResponse(
					array(
						'status' => 'SUCCESS',
						'code'   => 1
					)
				);
			}else{
				return new JsonResponse(
					array(
						'status' => 'ERROR',
						'code'   => -1
					)
				);
			}
	}//block

	/**
	 * Make Admin/Remove as admin
	 *
	 * @Route("/admin/users/{id}/grant-admin", name="user_grant_admin")
	 */
	public function makeAdminAction($id, Request $request)
	{
			$mode = intval($request->get('mode'));
			/**
			 * @var \UserBundle\Entity\User $user
			 */
			$user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('id' => $id));

			if($user){
				$em = $this->getDoctrine()->getManager();
				if($mode == 1){
					$user->setRoles(array('ROLE_USER','ROLE_ADMIN'));
				}else{
					$user->setRoles(array('ROLE_USER'));
				}
				$em->persist($user);
				$em->flush();

				return new JsonResponse(
					array(
						'status' => 'SUCCESS',
						'code'   => 1
					)
				);
			}else{
				return new JsonResponse(
					array(
						'status' => 'ERROR',
						'code'   => -1
					)
				);
			}
	}//block

	/**
	 * Finds and displays a user entity.
	 *
	 * @Route("/admin/users/{id}", name="user_show")
	 * @Method("GET")
	 */
	public function showAction(User $user)
	{
		$deleteForm = $this->createDeleteForm($user);

		return $this->render('user/show.html.twig', array(
			'user' => $user,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing user entity.
	 *
	 * @Route("/admin/users/{id}/delete", name="user_delete_confirm")
	 * @Method({"GET", "POST"})
	 */
	public function deleteConfirmAction(Request $request, User $user)
	{
		$deleteForm = $this->createDeleteForm($user);

		return $this->render('user/delete-confirm.html.twig', array(
			'user' => $user,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing user entity.
	 *
	 * @Route("/admin/users/{id}/edit", name="user_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, User $user)
	{
		$deleteForm = $this->createDeleteForm($user);
		$editForm = $this->createForm('UserBundle\Form\UserRegistrationType', $user);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted()) {
			if($editForm->isValid()){
				//			$this->getDoctrine()->getManager()->flush();
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();

				$request->getSession()->getFlashBag()->add('event', "All changes saved.");
				return $this->redirectToRoute('user_show', array('id' => $user->getId()));
			}else{
				print_r($editForm->getErrors(false));
			}
		}

		return $this->render('user/edit.html.twig', array(
			'user' => $user,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}
	/**
	 * Deletes a user entity.
	 *
	 * @Route("/admin/users/{id}", name="user_delete")
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
