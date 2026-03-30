<?php

namespace App\Domain;
use Doctrine\ORM\Mapping\OneToMany; 
use Doctrine\Common\Collections\Collection; 
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'Campus')]
class Campus
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_localite;

    #[Column(type: 'string', nullable: false)]
    private string $ville;

    #[Column(name: 'code_postal', type: 'string', nullable: false)]
    private string $code_postal;

    #[OneToMany(targetEntity: Utilisateur::class, mappedBy: 'campus')]
    private Collection $utilisateurs;

    public function __construct(string $ville, string $code_postal,)
    {
        $this->ville = $ville;
        $this->code_postal = $code_postal;
        $this->utilisateurs = new ArrayCollection();
    }

    public function getIdLocalite(): int 
    { 
        return $this->id_localite; 
    }

    public function getVille(): string 
    { 
        return $this->ville; 
    }
    
    public function setVille(string $ville): void 
    { 
        $this->ville = $ville; 
    }

    public function getCodePostal(): string 
    { 
        return $this->code_postal; 
    }
    
    public function setCodePostal(string $code_postal): void 
    { 
        $this->code_postal = $code_postal; 
    }

    public function getUtilsateurs(): Collection { 
        return $this->utilisateurs;
         }

    public function getEtudiants(): Collection {
        return $this->utilisateurs->filter(fn($u) => $u->getRole() === 'etudiant');
    }

    public function getPilotes(): Collection {
        return $this->utilisateurs->filter(fn($u) => $u->getRole() === 'pilote');
    }
}
