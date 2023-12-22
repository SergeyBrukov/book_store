<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\LoginUserModel;
use App\Model\RegistrationUserModel;
use App\Services\AuthServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct
    (
        private AuthServices $authServices,
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/registration', name: 'app_user_registration', methods: ['POST'])]
    public function registration(Request $request): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);

        $registrationUserData = new RegistrationUserModel();

        $registrationUserData
            ->setRoles('ROLE_USER')
            ->setEmail($userData['email'])
            ->setPassword($userData['password'])
            ->setFirstName($userData['firstName'])
            ->setLastName($userData['lastName']);

        return new JsonResponse($this->authServices->registrationUser($registrationUserData), JsonResponse::HTTP_CREATED);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/login', name: 'app_user_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);

        $loginUserData = new LoginUserModel();

        $loginUserData
            ->setEmail($userData['email'])
            ->setPassword($userData['password']);

        return $this->authServices->loginUser($loginUserData);
    }

    #[Route('/api/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->authServices->userProfile($user), JsonResponse::HTTP_OK);
    }
}