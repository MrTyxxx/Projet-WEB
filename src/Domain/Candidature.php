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
use DateTimeInterface;


#[Entity]
#[Table(name: 'CANDIDATURE')]
class Candidature
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(name: 'id_candidature', type: 'integer')]
    private int $id_candidature;

    // relation vers la table UTILISATEUR (id_utilisateur)
    #[ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'candidatures')]
    #[JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $utilisateur;

    // relation vers la table OFFRE_STAGE (id_offre)
    #[ManyToOne(targetEntity: Offrestage::class, inversedBy: 'candidatures')]
    #[JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre', nullable: false)]
    private OffreStage $offre;

    #[Column(name: 'chemin_cv', type: 'string', length: 255, nullable: false)]
    private string $cheminCv;

    #[Column(name: 'date_postulation', type: 'date', nullable: false)]
    private DateTimeInterface $datePostulation;

    #[Column(name: 'statut', type: 'string', length: 50, nullable: false)]
    private string $statut;

    public function __construct(
        Utilisateur $utilisateur,
        OffreStage $offre,
        string $cheminCv,
        DateTimeInterface $datePostulation,
        string $statut = 'en_attente'
    ) {
        $this->utilisateur     = $utilisateur;
        $this->offre           = $offre;
        $this->cheminCv        = $cheminCv;
        $this->datePostulation = $datePostulation;
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

    public function getCheminCv(): string
    {
        return $this->cheminCv;
    }

    public function setCheminCv(string $cheminCv): void
    {
        $this->cheminCv = $cheminCv;
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
