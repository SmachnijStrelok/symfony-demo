<?php


namespace App\Db\Types\User;

use App\Entity\Role;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class RoleType extends StringType
{

   public const NAME = 'user_role';

   public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
   {
      return $value instanceof Role ? $value->getValue() : $value;
   }

   public function convertToPHPValue($value, AbstractPlatform $platform): ?Role
   {
      return !(empty($value)) ? new Role($value) : null;
   }

   public function getName(): string
   {
      return self::NAME;
   }

   public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
   {
      return true;
   }
}
