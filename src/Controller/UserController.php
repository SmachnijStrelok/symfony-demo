<?php

namespace App\Controller;

use App\Dto\UserDTO;
use App\Dto\UserLoginDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\DtoBuilder\DTOBuilder;
use App\Services\Flusher\Flusher;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;


class UserController extends ApiController
{
    /**
     * @Route(
     *    path="/register",
     *    name="user.register",
     *    methods={"POST"}
     * )
     */
    public function register(
        DTOBuilder $builder,
        UserRepository $repository,
        Flusher $flusher,
        UserPasswordHasherInterface $hasher
    )
    {
        /** @var UserDTO $dto */
        $dto = $builder->buildDTO(UserDTO::class);
        $user = User::createFromDto($dto);
        $user->setPassword($hasher->hashPassword($user, $dto->password));

        $repository->add($user);
        $flusher->flush();
    }

    #[Route(path: "/user/login", name: "user.login", methods: ["POST"])]
    public function login(
        DTOBuilder $builder,
        UserRepository $repository,
        TranslatorInterface $translator,
        JWTTokenManagerInterface $JWTManager,
        UserPasswordHasherInterface $hasher
    )
    {
        /** @var UserLoginDTO $dto */
        $dto = $builder->buildDTO(UserLoginDTO::class);
        $user = $repository->getByEmail($dto->username);

        if (!$hasher->isPasswordValid($user, $dto->password)) {
            throw new UserNotFoundException($translator->trans('failed', [], 'auth'));
        }

        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    #[Route(path: "/user", name: "user.get", methods: ["GET"])]
    public function currentUser(SerializerInterface $serializer, UserRepository $repository)
    {
        $user = $repository->get($this->getUser()->getId());

        return $this->json($serializer->serialize($user, 'json'));
    }
}