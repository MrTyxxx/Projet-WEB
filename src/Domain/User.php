<?php

namespace App\Domain;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'Utilisateur')]
class Utilisateur {
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id_utilisateur;
     #[Column(type: 'string', nullable: false)]
    private string $role;
     #[Column(type: 'string', nullable: false)]
    private string $nom;
    #[Column(type: 'string', nullable: false)]
    private string $prenom;
     #[Column(type: 'string', nullable: false)]
    private string $email;
     #[Column(type: 'string', nullable: false)]
    private string $motdepasse;

    public function __construct(string $nom, string $prenom, string $email , string $motdepasse){
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motdepasse = password_hash($motdepasse, PASSWORD_BCRYPT);

    }

    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }
    public function getRole(): string 
    {
        return $this->role;
    }
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email= $email;
    }
    public function verifierMotdePasse(string $motdepasse): bool
    {
     return password_verify($motdepasse, $this->motdepasse);
    }
    public function setMotdePasse(string $motdepasse): void
    {
        $this->motdepasse = password_hash($motdepasse, PASSWORD_BCRYPT);

    }
}
