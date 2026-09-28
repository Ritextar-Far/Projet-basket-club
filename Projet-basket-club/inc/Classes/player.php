<?php

class Player
{
    private ?int $id = null;
    private string $firstName = '';
    private string $lastName = '';
    private ?DateTime $birthDate = null;
    private string $picture = '';

    public function __construct(
        ?int $id = null,
        string $firstName = '',
        string $lastName = '',
        ?DateTime $birthDate = null,
        string $picture = ''
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDate = $birthDate;
        $this->picture = $picture;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getBirthDate(): ?DateTime { return $this->birthDate; }
    public function setBirthDate(?DateTime $birthDate): self { $this->birthDate = $birthDate; return $this; }

    public function getPicture(): string { return $this->picture; }
    public function setPicture(string $picture): self { $this->picture = $picture; return $this; }
}