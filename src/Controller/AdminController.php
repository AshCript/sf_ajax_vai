<?php

namespace App\Controller;

use App\Entity\Notif;
use App\Entity\Pannier;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\CategorieType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin_home")
     */
     public function homeAdmin(CategorieRepository $repCat, ProduitRepository $repProduit): Response
     {
        $user = $this->getUser();
        if($user->getRoles() == ['ROLE_USER']) {
            $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
            return $this->redirectToRoute('user_home');
        }

        //Tous les marques des produits(avec redondance)
        $allMarks = $repProduit->findBy([]);
        $finalUniqMarques = [];
        //Récupération des produits selon la catégorie sélectionné, sans redondance
        for($i = 0 ; $i < sizeof($allMarks) ; $i++){
            if(sizeof($finalUniqMarques) > 0){
                for($j = 0 ; $j < sizeof($finalUniqMarques) ; $j++){
                    if($finalUniqMarques[$j] == $allMarks[$i]->getMarque())
                        break;
                    if($j == sizeof($finalUniqMarques) - 1 )
                        $finalUniqMarques[$j+1] = $allMarks[$i]->getMarque();
                }
            }else
                $finalUniqMarques[0] = $allMarks[0]->getMarque();
        }

         return $this->render('admin/index.html.twig', [
            'categories' => $repCat->findAll(),
            'marques' => $finalUniqMarques,
            'produits' => $repProduit->findAll()
         ]);
     }

     /*************************
     **************************
     **************************
     ***DEBUT CRUD CATEGORIE***
     **************************
     **************************
     **************************/

     /**
     * @Route("/admin/categorie", name="admin_cat")
     */
     public function listCategorie(CategorieRepository $repCat)
     {
         return $this->render('admin/categorie/list.html.twig', [
             'categories' => $repCat->findAll()
         ]);
     }

     /**
     * @Route("/admin/categorie/new", name="admin_create_cat")
     * @Route("/admin/categorie/{id}/edit", name="admin_edit_cat")
     */
    public function formCategorie(Categorie $categorie = null, Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        if($user->getRoles() == ['ROLE_USER']) {
            $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
            return $this->redirectToRoute('user_home');
        }

        if(!$categorie)
            $categorie = new Categorie;

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $categorie->setCreatedAt(new \DateTime());
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'Enregistré avec succès');
            return $this->redirectToRoute('admin_cat');
        }

        return $this->render('admin/categorie/form.html.twig', [
            'form_cat' => $form->createView()
        ]);
    }
 
     /**
      * @Route("/admin/categorie/{id}/delete", name="admin_delete_cat")
      */
     public function deleteCategorie(Categorie $categorie, EntityManagerInterface $manager)
     {
         $manager->remove($categorie);
         $manager->flush();
 
         // $request->getSession()->getFlashBag()->add('success', $categorie->getNom().' supprimé avec succès');
         $this->addFlash('success', $categorie->getNom().' supprimé avec succès');
         return $this->redirectToRoute('list_cat');
     }

     /*************************
     **************************
     **************************
     ****FIN CRUD CATEGORIE****
     **************************
     **************************
     **************************/

     /*************************
     **************************
     **************************
     ***DEBUT CRUD PRODUIT***
     **************************
     **************************
     **************************/

     /**
     * @Route("/admin/produit", name="admin_prod")
     */
    public function listProduit(ProduitRepository $repProduit, CategorieRepository $repCat){
        //Tous les marques des produits(avec redondance)
        $allMarks = $repProduit->findBy([]);
        $finalUniqMarques = [];
        //Récupération des produits selon la catégorie sélectionné, sans redondance
        for($i = 0 ; $i < sizeof($allMarks) ; $i++){
            if(sizeof($finalUniqMarques) > 0){
                for($j = 0 ; $j < sizeof($finalUniqMarques) ; $j++){
                    if($finalUniqMarques[$j] == $allMarks[$i]->getMarque())
                        break;
                    if($j == sizeof($finalUniqMarques) - 1 )
                        $finalUniqMarques[$j+1] = $allMarks[$i]->getMarque();
                }
            }else
                $finalUniqMarques[0] = $allMarks[0]->getMarque();
        }
        return $this->render("/admin/produit/list.html.twig", [
            "produits" => $repProduit->findBy([], ['categorie' => 'ASC']),
            "marques" => $finalUniqMarques,
            "categories" => $repCat->findAll()
        ]);
    }

     /**
     * @Route("/admin/categorie/{id}/produit", name="admin_cat_prod")
     */
    public function listProdParCat(Categorie $categorie, ProduitRepository $repProduit){
        //Tous les marques des produits(avec redondance)
        $allProdMarks = $repProduit->findBy(['categorie' => $categorie]);
        $finalUniqMarques = [];
        //Récupération des produits selon la catégorie sélectionné, sans redondance
        for($i = 0 ; $i < sizeof($allProdMarks) ; $i++){
            if(sizeof($finalUniqMarques) > 0){
                for($j = 0 ; $j < sizeof($finalUniqMarques) ; $j++){
                    if($finalUniqMarques[$j] == $allProdMarks[$i]->getMarque())
                        break;
                    if($j == sizeof($finalUniqMarques) - 1 )
                        $finalUniqMarques[$j+1] = $allProdMarks[$i]->getMarque();
                }
            }else
                $finalUniqMarques[0] = $allProdMarks[0]->getMarque();
        }

        return $this->render("/admin/produit/listByCat.html.twig", [
            "produits" => $repProduit->findBy(['categorie' => $categorie], ['categorie' => 'ASC']),
            "categorie" => $categorie,
            "marques" => $finalUniqMarques,
            "categories" => [],
            "marques" => [],
            "produits" => []
        ]);
    }

    /**
     * @Route("/admin/produit/new", name="admin_create_prod")
     * @Route("/admin/produit/{id}/edit", name="admin_edit_prod")
    */
    public function formProduit(Produit $produit = null, ProduitRepository $repProduit, Request $request, EntityManagerInterface $manager)
    {
        $produitExisteDeja = true;
        $editMode = true;
        if(!$produit){
            $produitExisteDeja = false;
            $produit = new Produit();
            $editMode = false;
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
            return $this->redirectToRoute('admin_prod');
        }
        return $this->render('/admin/produit/form.html.twig', [
            'form_produit' => $form->createView(),
            'editMode' => $editMode,
            'produit' => $produit,
            "categories" => [],
            "marques" => [],
            "produits" => []
        ]);
    }

    /**
    * @Route("/admin/produit/{id}/delete", name="admin_delete_prod")
    */
    public function deleteProduit(Produit $produit, EntityManagerInterface $manager){
        $marqueProd = $produit->getMarque();
        $modelProd = $produit->getModel();
        $manager->remove($produit);
        $manager->flush();
        $this->addFlash('success', " $marqueProd $modelProd supprimé avec succès");
        return $this->redirectToRoute('admin_prod');
    }

    /**
     * @Route("/admin/produit/{id}/detail", name="admin_detail_prod")
     */
     public function detailProduit(Produit $produit){
        return $this->render("/admin/produit/detail.html.twig", [
            "produit" => $produit,
            "categories" => [],
            "marques" => [],
            "produits" => []
        ]);
    }
    /**
     * @Route("/admin/produit/{id}/switch", name="admin_switch_dispo_prod")
     */
    public function switchDispoProduit(Produit $produit, EntityManagerInterface $manager){
        $user = $this->getUser();
        if($user->getRoles() == ['ROLE_USER']) {
            $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
            return $this->redirectToRoute('user_home');
        }
        $produit->setDispo(!$produit->getDispo());
        $manager->persist($produit);
        $manager->flush();
        if($produit->getDispo())
            $this->addFlash('success', "Changement de la disponibilité de ".$produit->getMarque()." ".$produit->getModel()." à \"OUI\"");
        else
            $this->addFlash('success', "Changement de la disponibilité de ".$produit->getMarque()." ".$produit->getModel()." à \"NON\"");

        return $this->redirectToRoute('admin_prod');
    }

     /*************************
     **************************
     **************************
     *****FIN CRUD PRODUIT*****
     **************************
     **************************
     **************************/
     /**
     * @Route("/admin/notif/{id}", name="admin_view_notif")
     */
    public function adminViewNotif(Notif $notif, EntityManagerInterface $manager): Response
    {
        if($notif->getUser() != $this->getUser()){
            $this->addFlash('error', "Une erreur s'est produite...");
            return $this->redirectToRoute('user_home');
        }
        if(!$notif->getSeen())
            $notif->setSeen(true);
        $manager->persist($notif);
        $manager->flush();
        return $this->render('admin/notif/index.html.twig', [
            'notif' => $notif,
        ]);
    }


    /**
     * @Route("/admin/commande/{id}", name="admin_handle_command")
     */
     public function adminHandleCommand(Pannier $pannier): Response
     {
         $user = $this->getUser();
         if($user->getRoles() == ['ROLE_USER'] || !$user) {
             $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
             return $this->redirectToRoute('user_home');
         }

         return $this->render('admin/command/index.html.twig', [
             'controller_name' => 'AdminController',
             'pannier' => $pannier
         ]);
     }



    /**
     * @Route("/admin/validate/{id}", name="admin_validate")
     */
    public function adminValidate(Pannier $pannier = null, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if($user->getRoles() == ['ROLE_USER']) {
            $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
            return $this->redirectToRoute('user_home');
        }
        if($pannier == null){
            $this->addFlash('error', 'Cette commande n\'existe pas');
        }else{
            $pannier->setValidated(true)
                    ->setValidatedAt(new \Datetime());
    
            $manager->persist($pannier);
            $manager->flush();

            $messageClient = 'Nous avons validé la commande de '.$pannier->getProduit()->getCategorie()->getNom().' '.$pannier->getProduit()->getMarque().' '.$pannier->getProduit()->getModel().' effectuée le '.$pannier->getPaidAt()->format("d-m-Y").' vers '.$pannier->getPaidAt()->format("h:i").'! Veuillez patienter, on va livrer votre commande dans quelques instants... Merci';
            
            $notifClient = new Notif();
            $notifClient->setUser($pannier->getUser())
                        ->setTitle("Commande de ".$pannier->getQuantity()." ".$pannier->getProduit()->getMarque()." ".$pannier->getProduit()->getModel()." (".$pannier->getPrix()*$pannier->getQuantity()." Ar).")
                        ->setMessage($messageClient)
                        ->setSeen(false)
                        ->setCreatedAt($pannier->getValidatedAt());
            $manager->persist($notifClient);
            $manager->flush();

            $this->addFlash('success', 'Commande validée!');
            return $this->redirectToRoute('admin_handle_command', [
                'id' => $pannier->getId()
            ]);
        }

        
    }
    /**
     * @Route("/admin/deliver/{id}", name="admin_deliver")
     */
    public function adminDeliver(Pannier $pannier = null, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if($user->getRoles() == ['ROLE_USER']) {
            $this->addFlash('error', 'Vous ne pouvez pas aller à cette adresse');
            return $this->redirectToRoute('user_home');
        }
        if($pannier == null){
            $this->addFlash('error', 'Cette commande n\'existe pas');
        }else{
            if(!$pannier->getValidated()){
                $this->addFlash('error', "Vous devez d'abord valider la commande");
                return $this->redirectToRoute('admin_handle_command', [
                    'id' => $pannier->getId()
                ]);
            }else{
                $pannier->setDelivered(true)
                        ->setDeliveredAt(new \Datetime());
                $manager->persist($pannier);
                $manager->flush();
                $messageClient = 'La commande de '.$pannier->getProduit()->getCategorie()->getNom().' '.$pannier->getProduit()->getMarque().' '.$pannier->getProduit()->getModel().' effectuée le '.$pannier->getPaidAt()->format("d-m-Y").' vers '.$pannier->getPaidAt()->format("h:i").' est en route pour la livraison! Si vous ne recevez pas votre livraison dans 3 heures, veuillez nous contacter... Merci';

                $notifClient = new Notif();
                $notifClient->setUser($pannier->getUser())
                            ->setTitle("Commande de ".$pannier->getQuantity()." ".$pannier->getProduit()->getMarque()." ".$pannier->getProduit()->getModel()." (".$pannier->getPrix()*$pannier->getQuantity()." Ar).")
                            ->setMessage($messageClient)
                            ->setSeen(false)
                            ->setCreatedAt($pannier->getDeliveredAt());
                $manager->persist($notifClient);
                $manager->flush();        
                
                $this->addFlash('success', 'Commande en cours de livraison!');
                return $this->redirectToRoute('admin_handle_command', [
                    'id' => $pannier->getId()
                ]);
            }
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
