<?php

namespace App\Domain;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'offrestages')]
class Entreprise {
   #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;
   #[Column(name : 'titre_offre',type: "string", nullabse : false)]
   private string $nom;
}