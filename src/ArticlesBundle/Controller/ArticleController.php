<?php

namespace ArticlesBundle\Controller;

use ArticlesBundle\Entity\Article;
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
		    $tags = $this->getMetaTags($link);
		    $user = $this->get('security.token_storage')->getToken()->getUser();

		    if($tags['title'] == '' || $tags['image'] == '' || $tags['description'] == ''){
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
			    $category = $this->getDoctrine()->getRepository('ArticlesBundle:Category')->findOneBy(array('name' => 'technology'));
			    $article = new Article();
			    $article->setTitle($tags['title']);
			    $article->setImageUrl($tags['image']);
			    $article->setDescription($tags['description']);
			    $article->setLink($link);
			    $article->setDomain(parse_url($link)['host']);
			    $article->setCategory($category);
			    $article->setUser($user);

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
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('ArticlesBundle:Article')->findAll();

        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
        ));
    }

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
