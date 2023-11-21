<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class LoginUserModel
{
    #[Email(groups: ['login:user'])]
    private string $email;
    #[Length(min: 5, max: 30, groups: ['login:user'])]
    private string $password;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return LoginUserModel
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return LoginUserModel
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}