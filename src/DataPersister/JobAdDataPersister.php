<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\JobAd;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Ulid;

class JobAdDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(Security $security,EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->security =$security;
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
        }

        if ($data instanceof JobAd && (($context['collection_operation_name'] ?? null) === 'put'))
        {
            $this->updatePublishedDate($data);
        }

        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->em->remove($data);
        $this->em->flush();
    }

    private function initNewJobAd(JobAd $jobAd)
    {
        $this->generateUuid($jobAd);

        $jobAd->setCompanyId($this->getCompanyId());

        $this->updatePublishedDate($jobAd);
    }

    private function updatePublishedDate(JobAd $jobAd)
    {
        if($jobAd->isPublished()){
            $jobAd->setPublishedAt(new \DateTimeImmutable());
        }
    }

    private function getCompanyId(): ?int
    {
        /**
         * @var $currentUser User
         */
        $currentUser = $this->security->getUser();
        return $currentUser->getCompany()->getId();
    }

    private function generateUuid(JobAd $jobAd)
    {
        $uuid = new Ulid();

        $jobAd->setReference("ref-".date("Y")."-".$uuid->toRfc4122());
    }

}