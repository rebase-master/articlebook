<?php

namespace ArticlesBundle\Controller;

use ArticlesBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Article controller.
 *
 * @Route("articles")
 */
class ArticleController extends Controller
{
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
    public function fetchAction(Article $article)
    {

	    $url1 = 'http://www.socialmediatoday.com/content/difference-between-articles-and-blogs';
	    $url2 = 'https://blog.hubspot.com/marketing/how-to-write-blog-post-simple-formula-ht';
	    $url3 = 'http://indianexpress.com/article/business/market/sensex-bounces-125-points-nifty-above-9200-early-on-4606989/';
	    $url4 = 'https://scotch.io/tutorials/build-a-restful-json-api-with-rails-5-part-two';
	    $tags = get_meta_tags($url4);
	    echo "<pre>";
	    print_r($tags);
	    die;
    }


}
