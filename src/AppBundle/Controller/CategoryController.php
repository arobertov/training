<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * Create Category Controller
     *
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/category/create",name="category_create")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_create');
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/create.html.twig',
            array('categories'=>$categories,'form' => $form->createView()));
    }

    /**
     * Edit Category Controller
     * @Route("category/edit/{id}",name="edit_category")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAuthorAction(Request $request,$id)
    {
        $category =  $this->getDoctrine()->getRepository(Category::class)->find($id);
        if($category===null){
            return $this->redirectToRoute('homepage');
        }
        $form=$this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_create');
        }
        return $this->render('category/edit.html.twig',array(
            'category'=>$category,'form'=>$form->createView()
        ));
    }

    /**
     * Delete Category Controller
     *
     * @Route("/category/delete/{id}",name="category_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function deleteAuthor($id, Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if($category===null)
        {
            return $this->redirectToRoute("homepage");
        }

        $form= $this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();


            return $this->redirectToRoute("homepage");
        }
        return $this->render('category/delete.html.twig',
            array('category'=>$category, 'form'=> $form->createView()) );
    }
}
