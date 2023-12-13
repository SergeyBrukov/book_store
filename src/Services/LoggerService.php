<?php

namespace App\Services;

use App\Entity\Logger;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LoggerService
{
    public const LOG__SUCCESS = 'SUCCESS';
    public const LOG_WARNING = 'WARNING';
    public const LOG_INFO = 'INFO';
    public const LOG_ERROR = 'ERROR';

    private string $userId;
    private string $userEmail;
    private array $userRoles;
    private Request $request;

    /**
     * @param TokenService $tokenService
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $requestStack
     */
    public function __construct
    (
       TokenService $tokenService,
       private readonly EntityManagerInterface $entityManager,
       private readonly RequestStack $requestStack
    )
    {
        $this->userId = $tokenService->getId();
        $this->userEmail = $tokenService->getEmail();
        $this->userRoles = $tokenService->getRoles();
        $this->request = $this->requestStack->getCurrentRequest();
    }


    /**
     * @param string $message
     * @param string $logType
     * @return void
     */
    public function createLog(string $message, string $logType): void
    {
        $ipAddress = $this->request->getClientIp();

        $log = new Logger();
        $log
            ->setUserId($this->userId)
            ->setUserEmail($this->userEmail)
            ->setUserRoles($this->userRoles)
            ->setLogType($logType)
            ->setMessage($message)
            ->setIpAddress($ipAddress);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}