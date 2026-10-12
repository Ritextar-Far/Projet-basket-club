<?php

class Staff
{
    private int $id;
    private string $firstName ;
    private string $lastName ;
    private string $picture ;
    private StaffRole $role ;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $picture,
        StaffRole $role,
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->picture = $picture;
        $this->role = $role;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getPicture(): string { return $this->picture; }
    public function setPicture(string $picture): self { $this->picture = $picture; return $this; }

    public function getRole(): StaffRole { return $this->role; }
    public function setRole(StaffRole $role): self { $this->role = $role; return $this; }
}