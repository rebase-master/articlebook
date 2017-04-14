<?php

namespace UserBundle\Controller;

use Symfony\Component\Security\Core\Security;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use UserBundle\Form\UserRegistrationType;



/**
 * Session controller.
 *
 */
class SessionController extends Controller
{
	/**
	 * @Route("/login", name="login")
	 * @Template()
	 */
	public function loginAction(Request $request)
	{
		$helper = $this->get('security.authentication_utils');

		return array(
				'last_username' => $helper->getLastUsername(),
				'error'         => $helper->getLastAuthenticationError(),
				);
	}

	/**
	 * @Route("/login_check", name="security_login_check")
	 */
	public function loginCheckAction()
	{

	}

	/** @Route("/logout", name="logout")  */
	public function logoutAction(){}

	private function encodePassword($user, $plainPassword)
	{
		/**
		 * @var User $user
		 */
		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		return $encoder->encodePassword($plainPassword, $user->getSalt());
	}

	private function authenticateUser(UserInterface $user)
	{
		$providerKey = 'secured_area';
		$token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
		$this->get('security.context')->setToken($token);
	}



	/**
	 * @Route("/register", name="register")
	 * @Template()
	 */
	public function registerAction(Request $request){
		$url = $this->generateUrl('homepage');
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
			return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
		}

		$sampleEmails = [];
		for($i=1;$i<=10;$i++){
			array_push($sampleEmails, 'user'.$i.'@example.com');
		}

		$defaultUser = new User();
		$form = $this->createForm(UserRegistrationType::class, $defaultUser );

		/**
		 * @var User $user
		 */
		if($request->isMethod('POST')){
			$form->handleRequest($request);
			if($form->isValid()){
				$user = $form->getData();
				$encoder = $this->get('security.password_encoder');
				$password = $encoder->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
				$user->setRegistrationKey();
				$em = $this->getDoctrine()->getManager();
				$email = $user->getEmail();

				if(in_array($email, array('user1@example.com', 'user2@example.com'))){
					$user->setRoles(array('ROLE_ADMIN'));
				}else{
					$user->setRoles(array('ROLE_USER'));
				}

				if(in_array($email, $sampleEmails)){
					$message = "Congratulations! You're registered. You can log into your account.";
					$user->setIsActive(true);
					$em->persist($user);
					$em->flush();
					$request->getSession()->getFlashBag()->add('event', $message);
					return $this->redirect($url);
				}else{
					$em->persist($user);
					$em->flush();
//					$message = $this->sendActivationEmailAction($user->getEmail());
					$request->getSession()->getFlashBag()->add('event', "Follow the instructions sent to your email address.");
					return $this->redirect($url);
				}
			}
		}

		return array('form' => $form->createView());
	}

	private function sendActivationEmailAction($email){
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('UserBundle:User');
		/**
		 * @var User $user
		 */
		$user = $repo->findOneBy(array(
			'email' => $email
		));
		if($user){
			if($user->getFirstName() == '' || $user->getLastName() == '')
				$name = $user->getUsername();
			else
				$name = $user->getFirstName()." ".$user->getLastName();

			$name = trim($name);
			$activationLink = "http://www.articlebook.com/verify?email=".$user->getEmail()."&ktg=".$user->getRegistrationKey();
			$message = \Swift_Message::newInstance()
				->setSubject('ArticleBook: Please confirm your registration')
				->setFrom(array('no-reply@articlebook.com' => 'ArticleBook'))
				->setTo($email)
				->setBody($this->renderView( 'UserBundle:Email:email.html.twig',array('name' => $name,'email' => $email,'verifyUrl' => $activationLink,)), 'text/html');
			$result = $this->get('mailer')->send($message);
			if($result)
				$displayMessage = "An activation link has been sent to ". $email.". Follow the instructions
                in the mail to activate your account.";
			else
				$displayMessage="Oops! Sorry, there seems to be a problem sending an email at the moment. Please try again in some time.";
		}else{
			$displayMessage = "Oops! Sorry, we couldn't find any user associated with the given email.";
		}
		return $displayMessage;
	}

}
