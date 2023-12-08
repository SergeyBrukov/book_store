<?php

namespace App\Services;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenService
{

    private string $id;
    private string $email;
    private array $roles;

    public function __construct
    (
        private TokenStorageInterface $tokenStorageInterface,
        private JWTTokenManagerInterface $jwtManager
    )
    {
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        $this->id = $decodedJwtToken['id'];
        $this->email = $decodedJwtToken['email'];
        $this->roles = $decodedJwtToken['roles'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}