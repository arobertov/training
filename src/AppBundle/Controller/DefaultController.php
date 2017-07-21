<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([],['id'=>'DESC'],4);
        return $this->render('default/index.html.twig',['articles'=>$articles]);
    }
}
