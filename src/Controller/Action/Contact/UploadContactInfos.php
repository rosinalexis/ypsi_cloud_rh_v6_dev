<?php

namespace App\Controller\Action\Contact;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Contact;
use App\Repository\JobAdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class UploadContactInfos  extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private JobAdRepository $jobAdRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        JobAdRepository $jobAdRepository,
    )
    {
        $this->entityManager =$entityManager;
        $this->validator =$validator;
        $this->jobAdRepository =$jobAdRepository;
    }

    public function __invoke(Request $request): Contact
    {

        $lastname = $request->request->get('lastname');
        $firstname = $request->request->get('firstname');
        $email = $request->request->get('email');
        $message =$request->request->get('message');
        $cvFile = $request->files->get('cvFile');
        $coverLetterFile = $request->files->get('coverLetterFile');

        $job = $this->jobAdRepository->find($request->request->get('jobAd'));

        if(!$job)
        {
            throw new BadRequestException('job ad reference is required.');
        }

        // Création d'un nouveau contact
        $contact = new Contact();

        $contact->setFirstname($firstname);
        $contact->setLastname($lastname);
        $contact->setEmail($email);
        $contact->setMessage($message);
        $contact->setJobAd($job);
        $contact->setCvFile($cvFile);
        $contact->setCoverLetterFile($coverLetterFile);
        $contact->setStatus("new");
        $contact->setCompanyId($job->getCompanyId());
        $this->addManagementToContact($contact);

        // Validation l'entité
        $this->validator->validate($contact,['groups' => ['post:contact']]);

        // Enregistrement en base des informations
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $contact->setCvFile(null);
        $contact->setCoverLetterFile(null);

        return $contact;
    }

    private function addManagementToContact(Contact $contact): void
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