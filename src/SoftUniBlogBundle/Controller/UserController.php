<?php

namespace SoftUniBlogBundle\Controller;

use SoftUniBlogBundle\Entity\Role;
use SoftUniBlogBundle\Entity\User;
use SoftUniBlogBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $unique = $this->getDoctrine()->getRepository(User::class)->
            findOneBy(['email'=>$form->getData()->getEmail()]);
            if($unique!=null){
                $email = $form->getData()->getEmail();
                $this->addFlash("message", "email ${email} already exist");
                return $this->render('user/register.html.twig');
            }
            if ($form->getData()->getPassword() != $form->getData()->getConfirmPassword()){
                $this->addFlash("message", "passwords does not match");
                return $this->render('user/register.html.twig');
            }


            $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name'=>'ROLE_USER']);
            $user->setRoles($role);
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("security_login");
        }
        return $this->render("user/register.html.twig");
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(){
        $userId = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        return $this->render('user/profile.html.twig', ['user'=>$user]);
    }
}
