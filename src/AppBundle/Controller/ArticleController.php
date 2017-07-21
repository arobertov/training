<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleController extends Controller
{
    /**
     * Create article controller
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @Route("/article/create",name="article_create")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $article=new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $article->getPath();
            $fileName = $this->get('appbundle.article.uploader')->upload($file);
            $article->setPath($fileName);
            $article->setPublishedBy($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('article/create.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * Edit Article controller
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("article/edit/{id}",name="article_edit")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editArticleAction(Request $request,$id)
    {

        $article=$this->getDoctrine()->getRepository(Article::class)->find($id);
        $oldPath = $article->getPath();
        try{
            $article->setPath(
                new File($this->getParameter('appbundle_article_upload').'/'.$article->getPath())
            );
        }
        catch (FileNotFoundException $fileNotFoundException)
        {
             $article->setPath(null);
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $article->getPath();
            if($file===null)
                {
                    $article->setPath($oldPath);
                }
                else
                {
                    $fileName = $this->get('appbundle.article.uploader')->upload($file);
                    $this->get('appbundle.article.uploader')->removeFile($oldPath);
                    $article->setPath($fileName);
                }

            $article->setPublishedBy($this->getUser());
            $article->setDateEdit(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_view',array('id'=>$article->getId()));
        }
        return $this->render('article/edit.html.twig',
            array('article'=>$article,'form' => $form->createView()));
    }

    /**
     * Delete article
     *
     * @Route("/article/delete/{id}",name="article_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function deleteArticle($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $file = $article->getPath();
        if($article===null)
        {
            return $this->redirectToRoute("homepage");
        }
            $article->setPath(
                new File($this->getParameter('appbundle_article_upload').'/'.$article->getPath())
            );

        $form= $this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $this->get('appbundle.article.uploader')->removeFile($file);
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();


            return $this->redirectToRoute("homepage");
        }
        return $this->render('article/delete.html.twig',
            array('article'=>$article, 'form'=> $form->createView()) );
    }

    /**
     * Preview single article
     *
     * @param $id
     * @Route("/article/{id}",name="article_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticle($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('article/view.htm.twig',array('article'=>$article));
    }





}
