<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'competences')]
class Competence
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_competence;

    #[Column(name: 'nom_competence', type: 'string', nullable: false)]
    private string $nom;

    #[Column(name: 'niveau_requis', type: 'string', nullable: true)]
    private ?string $niveauRequis;

    #[ManyToOne(targetEntity: Offrestage::class, inversedBy: 'competences')]
    #[JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre')]
    private Offrestage $offre;

    public function __construct(string $nom, Offrestage $offre, ?string $niveauRequis = null)
    {
        $this->nom         = $nom;
        $this->offre       = $offre;
        $this->niveauRequis = $niveauRequis;
    }

    public function getIdCompetence(): int { return $this->id_competence; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function getNiveauRequis(): ?string { return $this->niveauRequis; }
    public function setNiveauRequis(?string $niveauRequis): void { $this->niveauRequis = $niveauRequis; }
    public function getOffre(): Offrestage { return $this->offre; }
}