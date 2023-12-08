<?php

namespace App\Services;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenService
{

    private string $id;
    private string $email;
    private array $roles;

    /**
     * @param TokenStorageInterface $tokenStorageInterface
     * @param JWTTokenManagerInterface $jwtManager
     * @throws JWTDecodeFailureException
     */
    public function __construct
    (
        private readonly TokenStorageInterface $tokenStorageInterface,
        private readonly JWTTokenManagerInterface $jwtManager
    )
    {
        /**
         * @param array{
         *     id: string,
         *     email: string,
         *     roles: Array<string>
         * } $decodedJwtToken
         */
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