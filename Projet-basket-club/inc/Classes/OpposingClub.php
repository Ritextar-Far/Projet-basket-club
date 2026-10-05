<?php

class OpposingClub
{
    private int $id;
    private string $name = ''; // Indiqué dans les spécifications fonctionnelles
    private string $address = '';
    private string $city = '';

    public function __construct(
        int $id,
        string $name = '',
        string $address = '',
        string $city = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getAddress(): string { return $this->address; }
    public function setAddress(string $address): self { $this->address = $address; return $this; }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }
}