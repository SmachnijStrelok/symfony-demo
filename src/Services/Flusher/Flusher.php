<?php

namespace App\Services\Flusher;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
   /**
    * @var EntityManagerInterface
    */
   private EntityManagerInterface $em;

   public function __construct(EntityManagerInterface $em)
   {
      $this->em = $em;
   }

   public function flush()
   {
      $this->em->flush();
   }
}
