<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PannierController extends AbstractController
{
    /**
     * @Route("/pannier/{id}/add", name="add_produit_to_pannier")
     */
    public function index(Produit $produit): Response
    {

        return $this->redirectToRoute("");
    }
}
