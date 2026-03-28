<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany; 
use Doctrine\Common\Collections\Collection; 
use Doctrine\Common\Collections\ArrayCollection; 


#[Entity]
#[Table(name: 'WISHLIST')]
class Wishlist
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(name: 'id_wishlist', type: 'integer')]
    private int $id_wishlist;

    // FK vers UTILISATEUR (id_utilisateur)
    #[ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'wishlists')]
    #[JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $utilisateur;

    // FK vers OFFRE_STAGE (id_offre)
    #[ManyToOne(targetEntity: OffreStage::class, inversedBy: 'wishlists')]
    #[JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre', nullable: false)]
    private OffreStage $offre;

    #[Column(name: 'date_ajout', type: 'date', nullable: false)]
    private DateTimeInterface $dateAjout;

    public function __construct(Utilisateur $utilisateur, OffreStage $offre, DateTimeInterface $dateAjout)
    {
        $this->utilisateur = $utilisateur;
        $this->offre       = $offre;
        $this->dateAjout   = $dateAjout;
    }

    public function getIdWishlist(): int
    {
        return $this->id_wishlist;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    public function getOffre(): OffreStage
    {
        return $this->offre;
    }

    public function setOffre(OffreStage $offre): void
    {
        $this->offre = $offre;
    }

    public function getDateAjout(): DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(DateTimeInterface $dateAjout): void
    {
        $this->dateAjout = $dateAjout;
    }
}
