<?php

namespace App\Controller;

use App\Entity\Notif;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotifController extends AbstractController
{
    /**
     * @Route("/notif/{id}", name="view_notif")
     */
    public function viewNotif(Notif $notif, EntityManagerInterface $manager): Response
    {
        if($notif->getUser() != $this->getUser()){
            $this->addFlash('error', "Une erreur s'est produite...");
            return $this->redirectToRoute('user_home');
        }
        if(!$notif->getSeen())
            $notif->setSeen(true);
        $manager->persist($notif);
        $manager->flush();
        return $this->render('notif/index.html.twig', [
            'notif' => $notif,
        ]);
    }
}
