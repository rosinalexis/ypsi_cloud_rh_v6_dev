<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $_em;
    private UserPasswordHasherInterface $_passwordHaser;
    private  Security $security;
    private TokenGeneratorService $tokenGeneratorService;
    private MailerInterface $mailer;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        TokenGeneratorService $tokenGeneratorService,
        MailerInterface $mailer,
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

    public function persist($data, array $context = [])
    {

        if ($data instanceof User && (($context['collection_operation_name'] ?? null) === 'post')) {

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

    private function initUserAccount(User $user)
    {
        $user->setBlocked(false);
        $user->setConfirmed(false);
        $user->setConfirmationToken($this->tokenGeneratorService->getRandomSecureToken());

        $this->setDefaultUserCompany($user);
        $this->hashUserPassword($user);

        $this->sendEmail();
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

    private function sendEmail()
    {
        $email = (new Email())
            ->from('noreply-ypsi-cloud-rh@bob.com')
            ->to('alexisbotdev@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->html('<p>See Twig integration for better HTML integration!</p>','text\html');

        $this->mailer->send($email);
    }
}