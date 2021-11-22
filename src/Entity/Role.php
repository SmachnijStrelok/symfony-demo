<?php


namespace App\Entity;

use Webmozart\Assert\Assert;

class Role
{
    protected $value;

    public const USER = 'user';
    public const ADMIN = 'admin';

    public function __construct(string $role)
    {
        Assert::notEmpty($role);
        Assert::oneOf($role, [
            self::USER,
            self::ADMIN,
        ]);

        $this->value = $role;
    }

    public static function admin()
    {
        return new self(self::ADMIN);
    }

    public static function user()
    {
        return new self(self::USER);
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value ?: '';
    }

    public function isAdmin(): bool
    {
        return $this->value === self::ADMIN;
    }

    public function isUser()
    {
        return $this->value === self::USER;
    }
}
