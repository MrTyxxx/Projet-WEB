<?php

namespace App\Domain;
use App\Domain\Utilisateur;
use App\Domain\Offrestage;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use DateTimeImmutable;
use DateTimeInterface;


#[Entity, Table(name: 'Candidature')]
class Candidature
{
    #[Id, Column(name: 'id_candidature', type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_candidature;

    #[ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'candidatures')]
    #[JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $utilisateur;

    #[ManyToOne(targetEntity: Offrestage::class, inversedBy: 'candidatures')]
    #[JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre', nullable: false)]
    private OffreStage $offre;

    #[Column(name: 'date_postulation', type: 'date_immutable', nullable: false)]
    private DateTimeImmutable $datePostulation;
    
    #[Column(name: 'statut', type: 'string', length: 50, nullable: false)]
    private string $statut;

    public function __construct(
        Utilisateur $utilisateur,
        OffreStage $offre,
        string $statut = 'en_attente'
    ) {
        $this->utilisateur     = $utilisateur;
        $this->offre           = $offre;
        $this->datePostulation = new DateTimeImmutable();
        $this->statut          = $statut;
    }

    public function getIdCandidature(): int
    {
        return $this->id_candidature;
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

    public function getDatePostulation(): DateTimeInterface
    {
        return $this->datePostulation;
    }

    public function setDatePostulation(DateTimeInterface $datePostulation): void
    {
        $this->datePostulation = $datePostulation;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}
