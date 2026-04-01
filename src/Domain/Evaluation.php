<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

#[Entity, Table(name: 'Evaluations')]
class Evaluation
{
    #[Id, Column(name: 'id_eval', type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_eval;

    #[Column(name: 'note', type: 'integer', nullable: false)]
    private int $note;

    #[ManyToOne(targetEntity: Utilisateur::class)]
    #[JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $utilisateur;

    #[ManyToOne(targetEntity: Entreprise::class, inversedBy: 'evaluations')]
    #[JoinColumn(name: 'id_entreprise', referencedColumnName: 'id_entreprise', nullable: false)]
    private Entreprise $entreprise;

    public function __construct(int $note,Utilisateur $utilisateur,Entreprise $entreprise ) 
    {
        $this->note = $note;
        $this->utilisateur = $utilisateur;
        $this->entreprise = $entreprise;
    }

    public function getIdEvaluation(): int
    {
        return $this->id_evaluation;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $note): void
    {
        $this->note = $note;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    public function getEntreprise(): Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;
    }
}