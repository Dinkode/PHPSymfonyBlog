<?php

namespace SoftUniBlogBundle\Controller;

use Exception;
use SoftUniBlogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SoftUniBlogBundle\Entity\Article;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
class ApiController extends Controller
{
    /**
     * @Route("/api/articles", name="rest_api_articles", methods={"GET"})
     */
    public function articlesAction()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        $json = [];

        foreach ($articles as $article){
            $api= [];
            $api['title'] = $article->getTitle();
            $api['content'] = $article->getContent();
            $json[]=$api;
        }
        return new JsonResponse($json);
    }

    /**
     * @Route("/api/articles/{id}", name="rest_api_article", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function articleAction($id){
        /** @var Article $article $article */
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if(null === $article) {
            $json['error'] = 'resource not found';
            return new JsonResponse($json);
        }
        $json= [];
        $json['title'] = $article->getTitle();
        $json['content'] = $article->getContent();
        return new JsonResponse($json);

    }

    /**
     * @Route("/api/articles/create", name="rest_api_article_create", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $article = new Article();
        $data = json_decode($request->getContent(),true);
        try {
            $article->setTitle($data["title"]);
            $article->setContent($data["content"]);
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($data["authorId"]);
            $article->setAuthor($user);


            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return new Response(null, Response::HTTP_CREATED);
        } catch (Exception $exept){
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @Route("/api/articles/{id}", name="rest_api_article_edit", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request,$id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if($article===null){
            return new Response(null,Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(),true);
        $article->setTitle($data["title"]);
        $article->setContent($data["content"]);
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($data["authorId"]);
        $article->setAuthor($user);


        $em = $this->getDoctrine()->getManager();
        $em->merge($article);
        $em->flush();
        return new Response(null,Response::HTTP_CREATED);

    }

    /**
     * @Route("/api/articles/{id}", name="rest_api_article_edit", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request,$id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if($article===null){
            return new Response(null,Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        return new Response(null,Response::HTTP_NO_CONTENT);

    }




}