<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserLoginDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $username;

    #[Assert\NotBlank]
    public string $password;
}