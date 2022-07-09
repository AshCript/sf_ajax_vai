<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if ($user) {
            if($user->getRoles() == ['ROLE_ADMIN']){
                $this->addFlash('error', "Vous êtes déjà connecté en tant qu'administrateur");
                return $this->redirectToRoute('admin_home');
            }elseif($user->getRoles() == ['ROLE_USER']) {
                $this->addFlash('error', 'Vous êtes déjà connecté');
                return $this->redirectToRoute('user_home');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/accueil", name="user_home")
     */
    public function userHome(ProduitRepository $repProduit, CategorieRepository $repCategorie)
    {
        $user = $this->getUser();
        if(!$user){
            $this->addFlash('error', 'Vous devez vous connecter');
            return $this->redirectToRoute('main');
        }
        if($user->getRoles() == ['ROLE_ADMIN']){
            $this->addFlash('error', "Vous êtes déjà connecté en tant qu'admin");
            return $this->redirectToRoute('admin_home');
        }


        return $this->render('security/index.html.twig',[

        ]);
    }

    /**
     * @Route("/user/info", name="user_info")
     */
    public function userInfo(ProduitRepository $repProduit, CategorieRepository $repCategorie)
    {
        $user = $this->getUser();
        if(!$user){
            $this->addFlash('error', 'Vous devez vous connecter');
            return $this->redirectToRoute('main');
        }
        if($user->getRoles() == ['ROLE_ADMIN']){
            $this->addFlash('error', "Vous êtes déjà connecté en tant qu'admin");
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('security/userInfo.html.twig',[
            'user' => $this->getUser()
        ]);
    }

    // public function checkRoles($user){
    //     if($user->getRoles() == ['ROLE_ADMIN']){
    //         $this->addFlash('error', "Vous êtes déjà connecté en tant qu'admin");
    //         return $this->redirectToRoute('admin_home');
    //     }elseif($user->getRoles() == ['ROLE_USER']) {
    //         $this->addFlash('error', 'Vous êtes déjà connecté');
    //         return $this->redirectToRoute('user_home');
    //     }
    // }
}
