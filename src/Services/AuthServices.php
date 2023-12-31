<?php

namespace App\Services;

use App\Entity\Basket;
use App\Entity\User;
use App\Model\LoginUserModel;
use App\Model\RegistrationUserModel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthServices
{

    public function __construct
    (
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface    $JWTTokenManager,
        private readonly UserRepository              $userRepository,
        private readonly EntityManagerInterface      $entityManager,
        private readonly ValidatorInterface          $validator,
        private readonly SerializerInterface         $serializer
    )
    {
    }

    /**
     * @param RegistrationUserModel $registrationUserData
     * @return mixed
     */
    public function registrationUser(RegistrationUserModel $registrationUserData): mixed
    {
        $candidate = $this->userRepository->findOneBy(['email' => $registrationUserData->getEmail()]);

        if ($candidate) {
            return new JsonResponse(['message' => 'User already exist'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();

        $user
            ->setEmail($registrationUserData->getEmail())
            ->setPassword($registrationUserData->getPassword())
            ->setRoles($registrationUserData->getRoles())
            ->setFirstName($registrationUserData->getFirstName())
            ->setLastName($registrationUserData->getLastName())
            ->setBasket(new Basket());

        $errors = $this->validator->validate($user, null, ['registration:user']);

        if (count($errors) > 0) {
            $errorsData = [];

            foreach ($errors as $error) {
                $errorsData[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorsData], JsonResponse::HTTP_BAD_REQUEST);
        }

        $hashPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());

        $user->setPassword($hashPassword);

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $token = $this->JWTTokenManager->createFromPayload($user, [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()]);

        return [
            'token' => $token
        ];
    }

    /**
     * @param LoginUserModel $loginUserData
     * @return JsonResponse
     */
    public function loginUser(LoginUserModel $loginUserData): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $loginUserData->getEmail()]);

        $errors = $this->validator->validate($loginUserData, null, ['login:user']);

        if (count($errors) > 0) {
            $errorsData = [];

            foreach ($errors as $error) {
                $errorsData[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorsData], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return new JsonResponse('User not found', JsonResponse::HTTP_BAD_REQUEST);
        }

        $comparePassword = $this->passwordHasher->isPasswordValid($user, $loginUserData->getPassword());

        if (!$comparePassword) {
            return new JsonResponse('Invalid data', JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = $this->JWTTokenManager->createFromPayload($user, [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()]);

        return new JsonResponse([
            'token' => $token
        ]);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function userProfile(User $user): mixed
    {

        if (!$user) {
            return new JsonResponse('User not found', JsonResponse::HTTP_BAD_REQUEST);
        }

        $userSerializedData = $this->serializer->serialize($user, 'json', [
            'groups' => [
                'user:profile', 'user:response'
            ]
        ]);

        return json_decode($userSerializedData);

    }
}