<?php

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'entreprises')]
class Entreprise
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $nom;

    #[Column(type: 'string', nullable: false)]
    private string $secteur;

    #[Column(name: 'tel_entreprise', type: "string", nullable: false)]
    private ?string $contact;

    #[Column(name : 'email_entreprise', type: "string" ,nullable: false)]
    private string $email;
    #[Column(type: 'string', nullable: false)]
    private ?string $description;

    public function __construct(string $nom, string $secteur, string $campus, string $contact, string $email)
    {
        $this->nom = $nom;
        $this->secteur = $secteur;
        $this->contact = $contact;
        $this->email = $email;
        $this->description = $description;
    }

    public function getId(): int
    {  return $this->id; }

    public function getNom(): string
    {  return $this->nom;}

    public function setNom(string $nom): void
    { $this->nom = $nom;}

    public function getSecteur(): string
    { return $this->secteur;}

    public function setSecteur(string $secteur): void
    { $this->secteur = $secteur;}

    public function getContact(): ?string
    { return $this->contact;}

    public function setContact(?string $contact): void
    { $this->contact = $contact; }

    public function getDescription(): ?string
    { return $this-> description; }
    
    public function setDescription(?string $description): void
    { $this->description = $description;}
}
