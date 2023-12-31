<?php

namespace App\Entity;

use App\Repository\UserSwitchRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: UserSwitchRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    operations: [
        new Get(),
        new Put(
            security: "object.user == user"
        ),
        new Delete(
            security: "object.user == user"
        ),
        new Post(),
        new GetCollection(),
    ]
)]
class UserSwitch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "suscriptions")]
    public $user;
    #[ORM\ManyToOne(targetEntity: Switches::class, inversedBy: "users")]
    private $switch;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getSwitch(): Switches
    {
        return $this->switch;
    }

    public function setSwitch(Switches $switch)
    {
        $this->switch = $switch;
    }

    public function toJson(): string
    {
        // Crear un arreglo asociativo con las propiedades que deseas incluir en el JSON
        $data = [
            'id' => $this->id,
            'user' => [
                "id" => $this->getUser()->getId(),
                "username" => $this->getUser()->getUsername(),
            ],
            'switch' => [
                "id" => $this->getSwitch()->getId(),
                "name" => $this->getSwitch()->getName(),
                "description" => $this->getSwitch()->getDescription(),
            ],
            // Agrega otras propiedades según tus necesidades
        ];

        return json_encode($data);
    }
}
