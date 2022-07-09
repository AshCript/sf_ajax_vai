<?php

namespace App\Controller;

use App\Entity\Notif;
use App\Entity\Pannier;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\UserRepository;
use App\Repository\PannierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(ProduitRepository $repProduit, EntityManagerInterface $manager): Response
    {

        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

    /**
     * @Route("/produit/new", name="create_produit")
     * @Route("/produit/{id}/edit", name="edit_produit")
     */
    public function formProduit(Produit $produit = null, ProduitRepository $repProduit, Request $request, EntityManagerInterface $manager)
    {
        $produitExisteDeja = true;
        if(!$produit){
            $produitExisteDeja = false;
            $produit = new Produit();
        }
        if($produitExisteDeja){
            $produit->setPrevPrix(($repProduit->find($produit->getId()))->getPrix());
        }
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $produit->updateTimestamps();
            if(!$produitExisteDeja){
                $produit->setPrevPrix($produit->getPrix());
            }
            $manager->persist($produit);
            $manager->flush();
            if($produitExisteDeja)
                $this->addFlash('success', $produit->getMarque().' '.$produit->getModel().' modifié avec succès');
            else
                $this->addFlash('success', $produit->getMarque().' '.$produit->getModel().' ajouté avec succès');
            return $this->redirectToRoute('list_produit');
        }
        return $this->render('produit/form.html.twig', [
            'form_produit' => $form->createView(),
        ]);
    }

    /**
    * @Route("/produit/{id}/delete", name="delete_produit")
    */
    public function deleteProduit(Produit $produit, EntityManagerInterface $manager){
        $manager->remove($produit);
        $manager->flush();
        $this->addFlash('success', "$produit->getMarque() $produit->getModel() supprimé avec succès");
        return $this->redirectToRoute('list_produit');
    }

    /**
     * @Route("/produit/list", name="list_produit")
     */
    public function listProduit(ProduitRepository $repProduit){
        return $this->render("produit/list.html.twig", [
            "produits" => $repProduit->findAll()
        ]);
    }

    /**
     * @Route("/produit/{id}/detail", name="detail_produit")
     */
    public function detailProduit(Produit $produit){
        return $this->render("produit/detail.html.twig", [
            "produit" => $produit
        ]);
    }
   
    /**
     * @Route("/produit/{id}/buy/{qte}", name="buy_produit")
     */
    public function buy_produit(Produit $produit, $qte, EntityManagerInterface $pannierManager, EntityManagerInterface $notifManager, UserRepository $repAdmin, PannierRepository $repPannier){
        $pannier = new Pannier();
        $notifClient = new Notif();
        $notifAdmin = new Notif();
        
        $admin = $repAdmin->findBy(['email' => 'admin2@gmail.com']);

        $pannier->setUser($this->getUser())
                ->setProduit($produit)
                ->setQuantity($qte)
                ->setPrix($produit->getPrix())
                ->setPayStatus(true)
                ->setPaidAt(new \Datetime())
                ->setValidated(false)
                ->setDelivered(false);

        $pannierManager->persist($pannier);
        $pannierManager->flush();
        $messageClient = 'Le  '.$produit->getCategorie()->getNom().' '.$produit->getMarque().' '.$produit->getModel().' est ajouté dans votre pannier. Veuillez patienter car nous sommes en train de le traiter... Merci';
        $notifClient->setUser($this->getUser())
                    ->setTitle("Commande de ".$qte." ".$produit->getMarque()." ".$produit->getModel()." (".$produit->getPrix()*$qte." Ar).")
                    ->setMessage($messageClient)
                    ->setSeen(false)
                    ->setCreatedAt($pannier->getPaidAt());

        $commande = $repPannier->findOneBy([
            'user' => $this->getUser(),
            'produit' => $produit,
            'paidAt' => $pannier->getPaidAt(),
            'quantity' => $pannier->getQuantity(),
        ]);
        $notifAdmin->setUser($admin[0])
                   ->setTitle($this->getUser()->getNom()." ".$this->getUser()->getPrenom(). " a effectué une commande")
                   ->setMessage("
                        <table class=\"ui inverted black table\">
                            <thead>
                            <tr>
                              <th>N°</th>
                              <th>Date</th>
                              <th>Marque et Modèle</th>
                              <th>Qté</th>
                              <th>P.U</th>
                              <th>Montant</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr class='notif-small-text'>
                              <td>".$commande->getId()."</td>
                              <td>".$commande->getPaidAt()->format('d-m-Y H:i')."</td>
                              <td>".$produit->getMarque()." ".$produit->getModel()."</td>
                              <td>".$pannier->getQuantity()."</td>
                              <td>".$pannier->getPrix()."</td>
                              <td>".$qte*$pannier->getPrix()."</td>
                              <td>
                                <a class=\"ui teal circular button\" href=\"/admin/commande/".$commande->getId()."\">Voir</a>
                              </td> 
                            </tr>
                          </tbody>
                        </table>
                    ")
                   ->setSeen(false)
                   ->setCreatedAt($notifClient->getCreatedAt());
        $notifManager->persist($notifClient);
        $notifManager->persist($notifAdmin);

        $notifManager->flush();
        $this->addFlash('success', $messageClient);
        return $this->redirectToRoute('categorie', [
            'id' => $produit->getCategorie()->getId()
        ]);
    }
    
}