<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Produit
{
    use Timestampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $marque;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $model;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="bigint")
     */
    private $prix;

    /**
     * @ORM\Column(type="bigint")
     */
    private $prevPrix;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dispo;

    public function __toString(){
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPrevPrix(): ?string
    {
        return $this->prevPrix;
    }

    public function setPrevPrix(string $prevPrix): self
    {
        $this->prevPrix = $prevPrix;

        return $this;
    }

    public function getDispo(): ?bool
    {
        return $this->dispo;
    }

    public function setDispo(bool $dispo): self
    {
        $this->dispo = $dispo;

        return $this;
    }

   
    /**
     * @return Collection|Pannier[]
     */
    public function getPanniers(): Collection
    {
        return $this->panniers;
    }

    public function addPannier(Pannier $pannier): self
    {
        if (!$this->panniers->contains($pannier)) {
            $this->panniers[] = $pannier;
            $pannier->setProduit($this);
        }

        return $this;
    }

    public function removePannier(Pannier $pannier): self
    {
        if ($this->panniers->removeElement($pannier)) {
            // set the owning side to null (unless already changed)
            if ($pannier->getProduit() === $this) {
                $pannier->setProduit(null);
            }
        }

        return $this;
    }

}
