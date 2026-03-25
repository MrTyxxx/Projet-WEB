<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'Entreprise')]
class Entreprise
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_entreprise;

    #[Column(name: 'nom_entreprise', type: 'string', nullable: false)]
    private string $nom;

    #[Column(type: 'string', nullable: false)]
    private string $secteur;

    #[Column(name: 'email_contact', type: 'string', nullable: false)]
    private string $email;

    #[Column(name: 'tel_contact', type: 'string', nullable: true)]
    private ?string $telephone;

    #[Column(type: 'text', nullable: true)]
    private ?string $description;
    
    #[OneToMany(targetEntity: OffreStage::class, mappedBy: 'entreprise')]
    private Collection $offres;

    public function __construct(string $nom, string $secteur, string $email, ?string $telephone = null, ?string $description = null)
    {
        $this->nom         = $nom;
        $this->secteur     = $secteur;
        $this->email       = $email;
        $this->telephone   = $telephone;
        $this->description = $description;
        $this->offres      = new ArrayCollection();
    }

    public function getIdEntreprise(): int { return $this->id_entreprise; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getSecteur(): string { return $this->secteur; }
    public function setSecteur(string $secteur): void { $this->secteur = $secteur; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): void { $this->telephone = $telephone; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getOffres(): Collection { return $this->offres; }
    public function getNombreOffres(): int { return $this->offres->count(); }
}