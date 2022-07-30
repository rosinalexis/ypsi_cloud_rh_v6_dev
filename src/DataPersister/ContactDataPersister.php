<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Contact;
use App\Entity\User;
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
        if ($data instanceof Contact && (($context['collection_operation_name'] ?? null) === 'post'))
        {
            $this->initContactItem($data);
            $this->em->persist($data);
        }


        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->em->remove($data);
        $this->em->flush();
    }

    private function initContactItem(Contact $contact)
    {
        $contact->setCompanyId($this->getUserCompany());
        $contact->setStatus("new");
        $this->addManagementToContact($contact);
    }

    private function getUserCompany(): ?int
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        return $user->getCompany()->getId();
    }

    private function addManagementToContact(Contact $contact)
    {
        $defaultManagementConfig = [
            "do_disapprobation" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null,
                "reason" => null,
                "is_proposed_dates_email_sent" => false
            ],
            "do_receipt_confirmation_email" => [
                "state" => null,
                "is_sent" => false,
                "send_at" => null
            ],
            "do_meeting" => [
                "state" => null,
                "supervisor" => null,
                "proposed_dates" => [
                    [
                        "date_id" => null,
                        "data_value" => null,
                        "is_choosed" => false
                    ]
                ],
                "is_proposed_dates_email_sent" => false,
                "is_done" => false,
                "done_at" => null,
                "is_user_validated" => false
            ],
            "do_approbation" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null,
                "supervisor" => null
            ],
            "do_help" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null,
                "help_list" => [
                    [
                        "help_id" => null,
                        "help_name" => null,
                        "help_is_done" => false,
                        "help_asked_at" => null
                    ]
                ]
            ],
            "do_document" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null,
                "document_list" => [
                    [
                        "document_name" => null,
                        "document_is_done" => false,
                        "document_created_at" => null
                    ]
                ]
            ],
            "do_contract" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null
            ],
            "do_equipment" => [
                "state" => null,
                "is_done" => false,
                "done_at" => null,
                "equipment_list" => [
                    [
                        "equipment_ref" => null,
                        "equipment_name" => null,
                        "equipment_status" => null,
                        "equipment_gived_at" => null,
                        "equipment_returned_at" => null
                    ]
                ]
            ],
            "comments" => "",
            "history" => [
                [
                    "date" => null,
                    "action" => null,
                    "author" => null
                ]
            ]
        ];

        $contact->setManagement($defaultManagementConfig);

    }
}