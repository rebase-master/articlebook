<?php

namespace ArticlesBundle\Controller;

use ArticlesBundle\Entity\Article;
use ArticlesBundle\Entity\Comment;
use ArticlesBundle\Entity\Likes;
use ArticlesBundle\Entity\Tag;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{

    /**
     * Create article
     *
     * @Route("/new", name="create_article")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
	    if($request->isXmlHttpRequest()){
		    $link = $request->get('link');
		    $articleTags = json_decode($request->get('tags'));
		    $articleCategory = $request->get('category');

		    $metaTags = $this->getMetaTags($link);
		    $user = $this->get('security.token_storage')->getToken()->getUser();

		    if($metaTags['title'] == '' || $metaTags['image'] == '' || $metaTags['description'] == ''){
			    $response = array(
				    'status' => 'ERROR',
				    'code'   => -1,
				    'message' => 'Oops! The article could not be added.'
			    );
		    }else{
			    $encoders = array(new JsonEncoder());
			    $normalizers = array(new ObjectNormalizer());
			    $serializer = new Serializer($normalizers, $encoders);
			    
			    $em = $this->getDoctrine()->getManager();
			    $category = $this->getDoctrine()->getRepository('ArticlesBundle:Category')->findOneBy(array('id' => $articleCategory));
			    $article = new Article();
			    $article->setTitle($metaTags['title']);
			    $article->setImageUrl($metaTags['image']);
			    $article->setDescription($metaTags['description']);
			    $article->setLink($link);
			    $article->setDomain(parse_url($link)['host']);
			    $article->setCategory($category);
			    $article->setUser($user);
			    $article->setIsDeleted(false);

			    foreach ($articleTags as $tag) {
				    $Tag = $this->getDoctrine()->getRepository('ArticlesBundle:Tag')->findOneBy(array('name' => $tag->name));

				    if(!$Tag){
					    $Tag = new Tag();
					    $Tag->setName($tag->name);
					    $em->persist($Tag);
					    $em->flush();
				    }
				    $article->addTag($Tag);
			    }


			    $em->persist($article);
			    $em->flush();

			    $response = array(
				    'status' => 'SUCCESS',
				    'code'   => 1,
				    'message' => $serializer->serialize($article, 'json')

		    );
		    }

		    return new JsonResponse($response);
	    }
	    return array();
    }

    /**
     * Lists all article entities.
     *
     * @Route("/", name="article_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()->getRepository('ArticlesBundle:Article')->findAll();

	    $articles = $this->prepareArticlesForApi($articles);
        return new JsonResponse(array(
            'articles' => $articles,
        ));
    }

	/**
	 * Prepare quotes data
	 * @param $quotes
	 * @return mixed
	 */
	private function prepareArticlesForApi($articles){
		$result = [];
		$ctr = 0;
		$user = $this->get('security.token_storage')->getToken()->getUser();

		foreach ($articles as $article) {
			/**
			 * @var \ArticlesBundle\Entity\Article $article
			 * @var \UserBundle\Entity\User $user
			 */
//			var_dump($article->getTags());
//			die;
			$result[$ctr]['id']          = $article->getId();
			$result[$ctr]['username']    = $article->getUser()->getFirstName()." ".$article->getUser()->getLastName();
			$result[$ctr]['userProfileLink']    = $this->generateUrl('user_profile', array('username' => $article->getUser()->getUsername()));
			$result[$ctr]['title']       = $article->getTitle();
			$result[$ctr]['imageUrl']    = $article->getImageUrl();
			$result[$ctr]['link']        = $article->getLink();
			$result[$ctr]['description'] = $article->getDescription();
			$result[$ctr]['domain']      = $article->getDomain();
			$result[$ctr]['category']    = $article->getCategory()->getName();
			$result[$ctr]['createdAt']   = $article->getCreatedAt()->format('Y-m-d H:i:s');

			$likeUserIds = [];
			foreach ($article->getLikes() as $Like) {
				/** @var \ArticlesBundle\Entity\Likes $Like */
				array_push($likeUserIds, $Like->getUser()->getId());
			}

			$result[$ctr]['likeIds'] = $likeUserIds;
			$result[$ctr]['userLikes'] = in_array($user->getId(),$likeUserIds);

			$comments = array();
			foreach ($article->getComments() as $key => $Comment) {
				/** @var \ArticlesBundle\Entity\Comment $Comment */
				$comments[$key]['id']       = $Comment->getId();
				$comments[$key]['userId']   = $Comment->getUser()->getId();
				$comments[$key]['comment']  = $Comment->getComment();
				$comments[$key]['createdAt']  = $Comment->getCreatedAt()->format('s');
				$comments[$key]['username'] = $Comment->getUser()->getUsername();
				$comments[$key]['userProfileLink']    = $this->generateUrl('user_profile',
													array('username' => $Comment->getUser()->getUsername()));
			}

			$tags = array();
			foreach ($article->getTags() as $key => $Tag) {
				/** @var \ArticlesBundle\Entity\Tag $Tag */
				$tags[$key]['id']   = $Tag->getId();
				$tags[$key]['name'] = $Tag->getName();
			}

			$result[$ctr]['comments'] = $comments;
			$result[$ctr]['tags']     = $tags;

			$ctr++;
		}//for loop

		return $result;

	}//Preparearticles

	/**
     * Finds and displays a article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     */
    public function showAction(Article $article)
    {

        return $this->render('article/show.html.twig', array(
            'article' => $article,
        ));
    }

	/**
     * Add Like to Article
     *
     * @Route("/{id}/like", name="article_post_like")
     * @Method("POST")
     */
    public function likeAction(Article $article, Request $request)
    {
		if($request->isXmlHttpRequest() && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

		    $user = $this->get('security.token_storage')->getToken()->getUser();

		    if(!empty($article)){

			    $em = $this->getDoctrine()->getManager();
			    $Like = new Likes();
			    $Like->setUser($user);
			    $Like->setArticle($article);
			    $em->persist($Like);

			    try{
				    $em->flush();
				    $responseData = array('code' => 1, 'status' => 'OK');
			    }catch (Exception $e){
				    $responseData = array('code' => -1, 'status' => 'ERROR');
			    }

		    }else{
			    $responseData = array('code' => -1, 'status' => 'ERROR');
		    }
	    }else{
		    $responseData = array('code' => -2, 'status' => 'ERROR');
	    }
	    return new JsonResponse( $responseData );

    }

	/**
     * Remove Like from Article
     *
     * @Route("/{id}/unlike", name="article_post_unlike")
     * @Method("POST")
     */
    public function unlikeAction(Article $article, Request $request)
    {
	    if($request->isXmlHttpRequest() && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

		    /** @var \UserBundle\Entity\User $user */
		    $user = $this->get('security.token_storage')->getToken()->getUser();

		    if(!empty($article)){

			    $em = $this->getDoctrine()->getManager();
			    $Like = $this->getDoctrine()->getRepository('ArticlesBundle:Likes')
				    ->findOneBy(
					    array(
						    'article' => $article->getId(),
						    'user'    => $user->getId()
					    )
				    );

			    if($Like){
				    try{
					    $em->remove($Like);
					    $em->flush();
					    $responseData = array('code' => 1, 'status' => 'OK');
				    }catch (Exception $e){
					    $responseData = array('code' => -1, 'status' => 'ERROR');
				    }
			    }else{
				    $responseData = array('code' => -1, 'status' => 'ERROR');
			    }

		    }else{
			    $responseData = array('code' => -1, 'status' => 'ERROR');
		    }
	    }else{
		    $responseData = array('code' => -2, 'status' => 'ERROR');
	    }
	    return new JsonResponse( $responseData );
    }

	/**
     * Add comment to Article
     *
     * @Route("/{id}/comments/add", name="article_post_comment")
     * @Method("POST")
     */
    public function addCommentAction(Article $article, Request $request)
    {
	    if($request->isXmlHttpRequest() && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

		    /** @var \UserBundle\Entity\User $user */
		    $user = $this->get('security.token_storage')->getToken()->getUser();
		    $postComment = json_decode($request->get('comment'));

		    if(!empty($article)){

			    $em = $this->getDoctrine()->getManager();
			    $Comment = new Comment();
			    $Comment->setComment($postComment);
			    $Comment->setUser($user);
			    $Comment->setArticle($article);
			    $em->persist($Comment);

			    try{
				    $em->flush();
				    $responseData = array(
					    'code' => 1,
					    'status' => 'OK',
					    'comment'   => array(
						    'userProfileLink' => $this->generateUrl('user_profile', array('username' => $user->getUsername())),
						    'comment' => $postComment,
						    'username' => $user->getUsername(),
						    'id'        => $Comment->getId(),
						    'userId'    => $user->getId()
					    )
				    );
			    }catch (Exception $e){
				    $responseData = array('code' => -1, 'status' => 'ERROR');
			    }

		    }else{
			    $responseData = array('code' => -1, 'status' => 'ERROR');
		    }
	    }else{
		    $responseData = array('code' => -2, 'status' => 'ERROR');
	    }
	    return new JsonResponse( $responseData );
    }

	/**
	 * Remove Comment from Article
	 *
	 * @Route("/{id}/comments/delete{commentId}", name="article_remove_comment")
	 * @Method("DELETE")
	 */
	public function removeCommentAction(Article $article, Request $request, $commentId)
	{
		if($request->isXmlHttpRequest() && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

			/** @var \UserBundle\Entity\User $user */
			$user = $this->get('security.token_storage')->getToken()->getUser();

			if(!empty($article)){

				$em = $this->getDoctrine()->getManager();
				$Comment = $this->getDoctrine()->getRepository('ArticlesBundle:Comment')
								->findOneBy(array('id' => $commentId));

				if($Comment){
					try{
						$em->remove($Comment);
						$em->flush();
						$responseData = array('code' => 1, 'status' => 'OK');
					}catch (Exception $e){
						$responseData = array('code' => -1, 'status' => 'ERROR');
					}
				}else{
					$responseData = array('code' => -1, 'status' => 'ERROR');
				}

			}else{
				$responseData = array('code' => -1, 'status' => 'ERROR');
			}
		}else{
			$responseData = array('code' => -2, 'status' => 'ERROR');
		}
		return new JsonResponse( $responseData );
	}

	/**
     * Finds and displays a article entity.
     *
     * @Route("/fetch", name="article_show")
     * @Method("GET")
     */
    public function fetchAction()
    {

	    $url1 = 'http://www.socialmediatoday.com/content/difference-between-articles-and-blogs';
	    $url2 = 'https://blog.hubspot.com/marketing/how-to-write-blog-post-simple-formula-ht';
	    $url3 = 'http://indianexpress.com/article/business/market/sensex-bounces-125-points-nifty-above-9200-early-on-4606989/';
	    $url4 = 'https://scotch.io/tutorials/build-a-restful-json-api-with-rails-5-part-two';
	    $url5 = 'https://journal.thriveglobal.com/8-things-every-person-should-do-before-8-a-m-dab757641ed4';
	    $url6 = 'http://wethementors.com';
	    $tags = get_meta_tags($url6);
	    echo "<pre>";
	    print_r($tags);
	    echo "</pre><br />OG TAGS:<pre>";
	    $ogTags = $this->getOgMetaTags($url6);
	    var_dump($ogTags);
	    echo "</pre>";
	    die;
    }

//	private function getOgMetaTags($url){
//
//	}
	private function getMetaTags($url){
		$sites_html = file_get_contents($url);

		$html = new \DOMDocument();
		@$html->loadHTML($sites_html);
		$image = $title = $description = null;
		$metaTitleList = ['title', 'twitter:title', 'og:title'];
		$metaImageList = ['image', 'twitter:image', 'og:image'];
		$metaDescriptionList = ['description', 'twitter:description', 'og:description'];

		//Get all meta tags and loop through them.
		foreach($html->getElementsByTagName('meta') as $meta) {

			$metaName       =   $meta->getAttribute('name');
			$metaProperty   =   $meta->getAttribute('property');

			if(in_array($metaName, $metaTitleList) || in_array($metaProperty, $metaTitleList)){
				$title = $meta->getAttribute('content');
			}
			if(in_array($metaName, $metaImageList) || in_array($metaProperty, $metaImageList)){
				$image = $meta->getAttribute('content');
			}
			if(in_array($metaName, $metaDescriptionList) || in_array($metaProperty, $metaDescriptionList)){
				$description = $meta->getAttribute('content');
			}
		}
		return ['title' => $title, 'image' => $image, 'description' => $description];
	}


}
