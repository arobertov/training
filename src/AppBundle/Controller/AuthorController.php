<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends Controller
{
    /**
     * Create Author controller
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/author/create",name="author_create")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public  function createAuthorAction(Request $request)
    {
        $author= new Author();
        $form=$this->createForm(AuthorType::class,$author);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('author_create');
        }
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->render('author/create.html.twig',array(
             'authors'=>$authors,'form'=>$form->createView()
        )) ;
    }

    /**
     * Edit Authors Controller
     * @Route("author/edit/{id}",name="edit_author")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAuthorAction(Request $request,$id)
    {
        $author =  $this->getDoctrine()->getRepository(Author::class)->find($id);
        if($author===null){
            return $this->redirectToRoute('homepage');
        }
         $form=$this->createForm(AuthorType::class,$author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_create');
        }
        return $this->render('author/edit.html.twig',array(
            'author'=>$author,'form'=>$form->createView()
        ));
    }

    /**
     * Delete Author Controller
     *
     * @Route("/author/delete/{id}",name="author_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function deleteAuthor($id, Request $request)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);
        if($author===null)
        {
            return $this->redirectToRoute("homepage");
        }

        $form= $this->createForm(AuthorType::class,$author);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();


            return $this->redirectToRoute("homepage");
        }
        return $this->render('author/delete.html.twig',
            array('article'=>$author, 'form'=> $form->createView()) );
    }
}
