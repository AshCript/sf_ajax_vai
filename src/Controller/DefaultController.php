<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{
  /**
   * @Route("/api/produits", methods="GET")
   */
  public function getAllProducts(ProduitRepository $prod_repo, SerializerInterface $serializer): Response
  {
    return $this->json(
      json_decode(
        $serializer->serialize(
          $prod_repo->findBy([], ['id' => 'ASC']),
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['panniers', 'categorie']]
        ),
        JSON_OBJECT_AS_ARRAY
      )
    );
  }

  /**
   * Returns only one product 
   * @Route("/api/produit/{id}", methods="GET")
   */
  public function getProduct(Produit $produit, SerializerInterface $serializer){
    return $this->json(
      json_decode(
        $serializer->serialize(
          $produit,
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['panniers', 'categorie']]
        ),
        JSON_OBJECT_AS_ARRAY
      )
      );
  }

  /**
   * Returns all the categories
   * @Route("/api/categories", methods="GET")
   */
  public function getAllCategories(CategorieRepository $cat_repo, SerializerInterface $serializer): Response
  {
    return $this->json(
      json_decode(
        $serializer->serialize(
          $cat_repo->findBy([], ['id' => 'ASC']),
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['produits', 'panniers']]
        ),
        JSON_OBJECT_AS_ARRAY
      )
    );
  }

  /**
   * Returns a categorie
   * @Route("/api/categorie/{id}", methods="GET")
   */
  public function getCategory(Categorie $categorie, CategorieRepository $cat_repo, SerializerInterface $serializer): Response
  {
    // $req = json_decode($request->getContent(), true);
    return $this->json(
      json_decode(
        $serializer->serialize(
          $categorie,
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['produits', 'panniers']]
        ),
        JSON_OBJECT_AS_ARRAY
      )
    );
  }

  /**
   * Returns the category of each product
   * @Route("/api/categoryProduits", methods="GET")
   */
  public function getCategorProducts(ProduitRepository $prod_repo): Response
  {
    $produits = $prod_repo->findAll();
    $catProd =[];
    foreach($produits as $produit){
      array_push($catProd, array(
        'id_produit' => $produit->getId(),
        'id_category' => $produit->getCategorie()->getId(),
        'nom_category' => $produit->getCategorie()->getNom(),
      ));
    }

    return $this->json($catProd);
  }

  /**
   * Returns the category of a specific product
   * @Route("/api/categoryProduit/{id}", methods="GET")
   */
  public function getCategoryProduct(Produit $produit): Response
  {
    return $this->json(array($produit->getCategorie()->getId(), $produit->getCategorie()->getNom()));
  }

  /**
   * Returns the category of each product
   * @Route("/api/produitsDispo", methods="GET")
   */
  public function getDispoProducts(ProduitRepository $prod_repo): Response
  {
    $produits = $prod_repo->findAll();
    $prodDispo =[];
    foreach($produits as $produit){
      array_push($prodDispo, array(
        'id' => $produit->getId(),
        'isDispo' => $produit->getDispo()
      ));
    }

    return $this->json($prodDispo);
  }

  /**
   * Returns the number of products of each cateogry
   * @Route("/api/countProduitsCategory", methods="GET")
   */
  public function getProductsLengthOfEachCategory(CategorieRepository $cat_repo, ProduitRepository $prod_repo, SerializerInterface $serializer): Response
  {
    $categories = $cat_repo->findAll();
    $countProdsCat =[];
    foreach($categories as $category){
      array_push($countProdsCat, array(
        'id_category' => $category->getId(),
        'count' => count($category->getProduits())
      ));
    }
     
    return $this->json($countProdsCat);
  }

  /**
   * Returns all product marks
   * @Route("/api/productMarks", methods="GET")
   */
  public function getAllProductMarks(ProduitRepository $prod_repo, SerializerInterface $serializer): Response
  {
    $produits = $prod_repo->findAll();
    $marks =[];
    foreach($produits as $produit){
      $existAlready = false;
      for($i = 0 ; $i < count($marks) ; $i++){
        if($marks[$i] == $produit->getMarque()){
          $existAlready = true;
          break;
        }
      }
      if(!$existAlready){
        array_push($marks, $produit->getMarque());
      }
    }
      
    return $this->json($marks);
  }


  /**
   * Updating the disponibility of a product
   * @Route("/api/updateDispoProduct", methods="POST")
   */
  public function updateDispoProduct(Request $request, ProduitRepository $prod_repo, SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    $req = json_decode($request->getContent(), true);
    $produit = $prod_repo->findOneBy(['id' => $req['id']]);
    $produit->setDispo(!$produit->getDispo());
    $dispo = $produit->getDispo();
    $em->persist($produit);
    $em->flush();
    return $this->json($dispo);
  }


  /**
   * Adding a new product
   * @Route("/api/addProduct", methods="POST")
   */
  public function addProduct(Request $request, ProduitRepository $prodRepo, CategorieRepository $catRepo, SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    $produit = new Produit();
    $req = json_decode($request->getContent(), true);
    $produit->setCreatedAt(new \DateTime());

    $produit->setCategorie($catRepo->findOneBy(["id" => $req['id_categorie_produit']]));
    $produit->setMarque($req['marque_produit']);
    $produit->setModel($req['model_produit']);
    $produit->setDescription($req['desc_produit']);
    $produit->setPrevPrix($req['prix_produit']);
    $produit->setPrix($req['prix_produit']);
    $produit->setDispo($req['dispo_produit']);

    $em->persist($produit);
    $em->flush();
    // $this->addFlash('success', 'Enregistré avec succès');

    return $this->json(
      json_decode(
        $serializer->serialize(
          $prodRepo->findOneBy([
            "marque" => $produit->getMarque(),
            "model" => $produit->getModel(),
            "prix" => $produit->getPrix(),
            "prevPrix" => $produit->getPrevPrix()
          ]),
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['categorie', 'panniers']],
          JSON_OBJECT_AS_ARRAY
        ), true
      )
    );
  }

  /**
   * Update a product
   * @Route("/api/updateProduct/{id}", methods="PUT")
   */
  public function updateProduct(Request $request, Produit $produit, CategorieRepository $cat_rep, SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    $req = json_decode($request->getContent(), true);
    $category = $cat_rep->find($req['id_categorie']);
    $produit->setCategorie($category);
    $produit->setMarque($req['marque']);
    $produit->setModel($req['model']);
    $produit->setDescription($req['description']);
    $produit->setPrevPrix($produit->getPrix());
    $produit->setPrix($req['prix']);
    $produit->setDispo($req['dispo']);
    $produit->setUpdatedAt(new \DateTime());
    $em->persist($produit);
    $em->flush($produit);
    return $this->json(
      json_decode(
        $serializer->serialize(
          $produit,
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['categorie', 'panniers']],
          JSON_OBJECT_AS_ARRAY
        ), true
      )
    );
  }

  /**
   * Delete a product
   * @Route("/api/deleteProduct/{id}", methods="DELETE")
   */
  public function deleteProduct(Produit $produit, EntityManagerInterface $em):Response
  {
    $em->remove($produit);
    $em->flush();
    return $this->json(array($produit->getMarque(), $produit->getModel()));
  }



  /**
   * Adding a new category
   * @Route("/api/addCategory", methods="POST")
   */
  public function addCategory(Request $request, CategorieRepository $catRepo, SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    $category = new Categorie();
    $req = json_decode($request->getContent(), true);
    $category->setCreatedAt(new \DateTime());
      
    $category->setNom($req['nom']);

    $em->persist($category);
    $em->flush();
    // $this->addFlash('success', 'Enregistré avec succès');

    return $this->json(
      json_decode(
        $serializer->serialize(
          $catRepo->findOneBy([
            "nom" => $category->getNom()
          ]),
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['categorie', 'panniers']],
          JSON_OBJECT_AS_ARRAY
        ), true
      )
    );
  }

  /**
   * Updating a new category
   * @Route("/api/updateCategory/{id}", methods="POST")
   */
  public function updateCategory(Request $request, Categorie $category, CategorieRepository $catRepo, SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    $req = json_decode($request->getContent(), true);
      
    $category->setNom($req['nom']);

    $em->persist($category);
    $em->flush();
    // $this->addFlash('success', 'Enregistré avec succès');

    return $this->json(
      json_decode(
        $serializer->serialize(
          $catRepo->findOneBy([
            "id" => $category->getId()
          ]),
          'json',
          [AbstractNormalizer::IGNORED_ATTRIBUTES => ['categorie', 'panniers']],
          JSON_OBJECT_AS_ARRAY
        ), true
      )
    );
  }

  /**
   * Deleting a category
   * @Route("/api/deleteCategory/{id}", methods="DELETE")
   */
  public function deleteCategory(Categorie $category, EntityManagerInterface $em):Response
  {
    foreach($category->getProduits() as $produit){
      $em->remove($produit);
    }
    $em->remove($category);
    $em->flush();
    return $this->json("La catégorie ".$category->getNom()." est supprimé avec succès!");
  }


  // /**
  // * @Route("/testJson", name="test_json")
  // */
  // public function testJson(ProduitRepository $produit_repo){
  //     return $this->render('default/index.html.twig', [
  //         'all_product' => ($produit_repo->find(1))->__toString()
  //     ]);
  // }
}