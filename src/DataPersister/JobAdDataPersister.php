<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\JobAd;
use App\Entity\User;
use App\Service\ReferenceGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class JobAdDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $em;
    private Security $security;
    private ReferenceGeneratorService $referenceGeneratorService;

    public function __construct(Security $security,EntityManagerInterface $em,ReferenceGeneratorService $referenceGeneratorService)
    {
        $this->em = $em;
        $this->security =$security;
        $this->referenceGeneratorService = $referenceGeneratorService;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof JobAd;
    }

    public function persist($data, array $context = [])
    {
        if ($data instanceof JobAd && (($context['collection_operation_name'] ?? null) === 'post'))
        {
            $this->initNewJobAd($data);
            $this->em->persist($data);
        }

        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->em->remove($data);
        $this->em->flush();
    }

    private function initNewJobAd(JobAd $jobAd)
    {
        $jobAd->setReference($this->referenceGeneratorService->getRandomSecureReference());

        $jobAd->setCompanyId($this->getCompanyId());

    }

    private function getCompanyId(): ?int
    {
        /**
         * @var $currentUser User
         */
        $currentUser = $this->security->getUser();

        if(!$currentUser)
        {
            throw new UnauthorizedHttpException("Pls do Authentication Or go away.");
        }

        return $currentUser->getCompany()->getId();
    }

}