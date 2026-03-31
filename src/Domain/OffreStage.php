<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;

#[Entity, Table(name: 'offrestages')]
class Offrestage
{
    #[Id, Column(name: 'id_offre', type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_offre;

    #[Column(name: 'titre_offre', type: 'string', nullable: false)]
    private string $titre;

    #[Column(name: 'description', type: 'text', nullable: false)]
    private string $description;

    #[Column(name: 'remuneration', type: 'string', nullable: false)]
    private string $remuneration;

    #[Column(name: 'date_offre', type: 'date_immutable', nullable: false)]
    private DateTimeImmutable $dateOffre;

    #[ManyToOne(targetEntity: Campus::class)]
    #[JoinColumn(name: 'id_campus', referencedColumnName: 'id_campus', nullable: false)]
    private Campus $campus;

    #[ManyToOne(targetEntity: Entreprise::class, inversedBy: 'offres')]
    #[JoinColumn(name: 'id_entreprise', referencedColumnName: 'id_entreprise', nullable: false)]
    private Entreprise $entreprise;

    #[OneToMany(targetEntity: Competence::class, mappedBy: 'offre')]
    private Collection $competences;

    #[OneToMany(targetEntity: Candidature::class, mappedBy: 'offre')]
    private Collection $candidatures;

    public function __construct(
        string $titre, 
        string $description, 
        string $remuneration, 
        Entreprise $entreprise, 
        Campus $campus,
    ) {
        $this->titre = $titre;
        $this->description = $description;
        $this->remuneration = $remuneration;
        $this->entreprise = $entreprise;
        $this->campus = $campus;
        $this->dateOffre = new DateTimeImmutable(); 
        $this->candidatures = new ArrayCollection();
        $this->competences = new ArrayCollection();
    }


    public function getIdOffre(): int { return $this->id_offre; }

    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): void { $this->titre = $titre; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }

    public function getRemuneration(): string { return $this->remuneration; }
    public function setRemuneration(string $remuneration): void { $this->remuneration = $remuneration; }

    public function getDateOffre(): DateTimeImmutable { return $this->dateOffre; }
    public function setDateOffre(DateTimeImmutable $dateOffre): void { $this->dateOffre = $dateOffre; }

    public function getCampus(): Campus { return $this->campus; }
    public function setCampus(Campus $campus): void { $this->campus = $campus; }

    public function getEntreprise(): Entreprise { return $this->entreprise; }
    public function setEntreprise(Entreprise $entreprise): void { $this->entreprise = $entreprise; }

    public function getCandidatures(): Collection { return $this->candidatures; }
    public function getNombreCandidatures(): int { return $this->candidatures->count(); }

    public function getCompetences(): Collection { return $this->competences; }
}