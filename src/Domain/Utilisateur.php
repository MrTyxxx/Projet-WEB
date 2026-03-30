<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection; 
use Doctrine\Common\Collections\ArrayCollection; 

#[Entity, Table(name: 'Utilisateurs')]
class Utilisateur {
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_utilisateur;

    #[Column(type: 'string', nullable: false)]
    private string $role;

    #[Column(type: 'string', nullable: false)]
    private string $nom;

    #[Column(type: 'string', nullable: false)]
    private string $prenom;

    #[Column(type: 'string', nullable: false, unique: true)]
    private string $email;

     #[Column(type: 'string',name: 'telephone' ,nullable: false)]
    private string $telephone;

    #[Column(type: 'string', nullable: false)]
    private string $motdepasse;

    #[OneToMany(targetEntity: Candidature::class, mappedBy: 'utilisateur')]
    private Collection $candidatures;

    #[ManyToOne(targetEntity: Campus::class, inversedBy: 'utilisateurs')]
    #[JoinColumn(name: 'id_campus', referencedColumnName: 'id_campus', nullable: true)]
    private ?Campus $campus;


    public function __construct(string $nom, string $prenom, string $email,string $telephone, string $motdepasse, string $role,?Campus $campus = null){
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->setMotdePasse($motdepasse);
        $this->role = $role; 
        $this->campus = $campus;
        $this->candidatures = new ArrayCollection();
    }

    
    public function getCandidatures(): Collection {
        return $this->candidatures;
    }

    public function getIdUtilisateur(): int { return $this->id_utilisateur; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }
    
    public function getTelephone(): string  { return $this->telephone; }
    public function setTelephone(string  $telephone): void { $this->telephone = $telephone; }

    public function getCampus(): ?Campus { return $this->campus; }
public function setCampus(?Campus $campus): void { $this->campus = $campus; }

    public function verifierMotdePasse(string $motdepasse): bool {
        return password_verify($motdepasse, $this->motdepasse);
    } 
    public function setMotdePasse(string $motdepasse): void {
        $this->motdepasse = password_hash($motdepasse, PASSWORD_BCRYPT);
    }
}