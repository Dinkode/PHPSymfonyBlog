<?php

namespace SoftUniBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\User;
use SoftUniBlogBundle\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * @Route("/article_create", name="article_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function articleCreate(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $file */
            $file = $form->getData()->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('image_directory'), $fileName);
            } catch (\Exception $exception){

            }
            $currentUser = $this->getUser();
            $article->setAuthor($currentUser);
            $article->setImage($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute("blog_index");
        }
        return $this->render("article/create.html.twig", ['form'=>$form->createView()]);

    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticle($id){
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('article/details.html.twig', ['article'=>$article]);

    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function articleEdit(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $oldImage = $article->getImage();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        /** @var User $currentUser */
        $currentUser=$this->getUser();

        if($article === null){
            return $this->redirectToRoute('blog_index');
        }

        if(!($currentUser->isAuthor($article) || $currentUser->isAdmin())){
            return $this->redirectToRoute('blog_index');
        }


        if($form->isSubmitted() && $form->isValid()){


            if($form->getData()->getImage()!=null){
                if($oldImage!=null) {
                    $fs = new Filesystem();
                    $fs->remove($this->getParameter('image_directory').$oldImage);
                }
                /** @var UploadedFile $file */
                $file = $form->getData()->getImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $article->setImage($fileName);

            } else {
                $article->setImage($oldImage);
            }
            try{
                $file->move($this->getParameter('image_directory'), $fileName);
            } catch (\Exception $exception){

            }

            $currentUser = $this->getUser();
            $article->setAuthor($currentUser);
            $em = $this->getDoctrine()->getManager();
            $em->merge($article);
            $em->flush();
            return $this->redirectToRoute("blog_index");
        }
        return $this->render("article/edit.html.twig", ['form'=>$form->createView(), 'article'=>$article]);

    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function articleDelete(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $oldImage = $article->getImage();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        /** @var User $currentUser */
        $currentUser=$this->getUser();

        if($article === null){
            return $this->redirectToRoute('blog_index');
        }
        if(!$currentUser->isAuthor($article) && !$currentUser->isAdmin()){
            return $this->redirectToRoute('blog_index');
        }


        if($form->isSubmitted() && $form->isValid()){
            if($oldImage!=null) {
                $fs = new Filesystem();
                $fs->remove($this->getParameter('image_directory').$oldImage);
            }
            $currentUser = $this->getUser();
            $article->setAuthor($currentUser);
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
            return $this->redirectToRoute("blog_index");
        }
        return $this->render("article/delete.html.twig", ['form'=>$form->createView(), 'article'=>$article]);

    }
}
