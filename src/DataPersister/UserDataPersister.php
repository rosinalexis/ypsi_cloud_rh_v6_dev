<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\MailerService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $_em;
    private UserPasswordHasherInterface $_passwordHaser;
    private  Security $security;
    private TokenGeneratorService $tokenGeneratorService;
    private MailerService $mailer;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        TokenGeneratorService $tokenGeneratorService,
        MailerService $mailer,
    )
    {
        $this->security =$security;
        $this->_em = $em;
        $this->_passwordHaser =$passwordHasher;
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->mailer = $mailer;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @throws InternalErrorException
     */
    public function persist($data, array $context = [])
    {
        if ($data instanceof User && (($context['collection_operation_name'] ?? null) === 'post')) {

            //initialisation de l'utilisateur
            $this->initUserAccount($data);

            //enregistrement de la donnée
            $this->_em->persist($data);
        }

        $this->_em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }


    /**
     * @throws InternalErrorException
     */
    private function initUserAccount(User $user): void
    {
        $user->setBlocked(false);
        $user->setConfirmed(false);
        $user->setConfirmationToken($this->tokenGeneratorService->getRandomSecureToken());

        $this->setDefaultUserCompany($user);
        $this->hashUserPassword($user);
        $this->mailer->sendAccountConfirmationEmail($user);

    }

    private function setDefaultUserCompany(User $user)
    {
        /**
         * @var $adminUser User
         */
        $adminUser = $this->security->getUser();
        $user->setCompany($adminUser->getCompany());
    }

    private function hashUserPassword(User $user)
    {
        // hash du mot de passe de l'utilisateur
        // un mot de passe par defaut pour le moment
        $user->setPassword(
            $this->_passwordHaser->hashPassword(
                $user,
                "e34g#52kNRtL"
            )
        );
    }

}