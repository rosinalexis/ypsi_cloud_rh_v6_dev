<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class ContactDataPersister implements ContextAwareDataPersisterInterface
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
        return $data instanceof Contact;
    }

    public function persist($data, array $context = [])
    {
        if ($data instanceof Contact && (($context['collection_operation_name'] ?? null) === 'put'))
        {
            $this->em->persist($data);
            $this->em->flush();
        }

    }

    public function remove($data, array $context = [])
    {
        $this->em->remove($data);
        $this->em->flush();
    }



}