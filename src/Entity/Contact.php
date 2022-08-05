<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Action\Contact\UploadContactInfos;
use App\Entity\Traits\Timestamplable;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'contacts')]
#[Vich\Uploadable]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' =>[
            'method' => 'POST',
            'path'=>'/apply',
            'deserialize' => false,
            'controller'=>UploadContactInfos::class,
            //'defaults' =>['_api_receive' => false],
            'security' => "is_granted('PUBLIC_ACCESS')",
            'denormalization_context' => [
                'groups' => ['post:contact'],
            ],
            'openapi_context'=>[
                'requestBody'=>[
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'lastname' =>[
                                        'type' => 'string',
                                        'exemple'=> 'Doe',
                                    ],
                                    'firstname' =>[
                                        'type' => 'string',
                                        'exemple'=> 'John'
                                    ],
                                    'email' =>[
                                        'type' => 'email',
                                        'exemple'=> 'jhondoe@test.fr'
                                    ],
                                    'message' =>[
                                        'type' => 'string',
                                    ],
                                    'jobAd' =>[
                                        'type' => 'interger',
                                        'exemple'=> '1'
                                    ],
                                    'cvFile' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ],
                                    'coverLetterFile'=>[
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ],
    itemOperations:[
      'get',
      'put' =>[
          'denormalization_context' => [
          'groups' => ['put:contact'],
      ],

      ],
      'delete'
    ],
    attributes: [
        'security' => "is_granted('ROLE_ADMIN')"
    ]
)]
class Contact
{
    use Timestamplable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Assert\Length(min: 2,max: 255,groups: ['post:contact'])]
    #[Groups(['post:contact'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Assert\Length(min: 2,max: 255,groups: ['post:contact'])]
    #[Groups(['post:contact'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Assert\Email(groups: ['post:contact'])]
    #[Assert\Length(min: 5,max: 255,groups: ['post:contact'])]
    #[Groups(['post:contact'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 255,groups: ['post:contact'])]
    #[Groups(['post:contact'])]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['put:contact'])]
    private array $management = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['put:contact'])]
    #[Groups(['put:contact'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Groups(['post:contact'])]
    private ?JobAd $jobAd = null;

    #[ORM\Column]
    #[Assert\type('int')]
    private ?int $companyId = null;

    //la partie upload du cv
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Assert\File(
        maxSize: '8M',
        mimeTypes: ['application/pdf', 'application/x-pdf'],
        mimeTypesMessage: 'Please upload a valid PDF',
        groups: ['post:contact']
    )]
    #[Vich\UploadableField(mapping: 'contacts',fileNameProperty: 'cvFileName',size: 'cvFileSize')]
    #[Groups(['post:contact'])]
    private ?File $cvFile = null;

    #[ORM\Column(type: Types::STRING,nullable: true)]
    private ?string $cvFileName = null;

    #[ORM\Column(nullable: true)]
    private ?string $cvFileUrl = null;

    #[ORM\Column(type: Types::INTEGER,nullable: true)]
    private ?string $cvFileSize = null;

    //la partie upload de la lettre de motivation
    #[Assert\NotBlank(groups: ['post:contact'])]
    #[Assert\File(
        maxSize: '8M',
        mimeTypes: ['application/pdf', 'application/x-pdf'],
        mimeTypesMessage: 'Please upload a valid PDF',
        groups: ['post:contact']
    )]
    #[Vich\UploadableField(mapping: 'contacts',fileNameProperty: 'coverLetterFileName',size: 'coverLetterFileSize')]
    #[Groups(['post:contact'])]
    private ?File $coverLetterFile = null;

    #[ORM\Column(type: Types::STRING,nullable: true)]
    private ?string $coverLetterFileName = null;

    #[ORM\Column(nullable: true)]
    private ?string $coverLetterFileUrl = null;

    #[ORM\Column(type: Types::INTEGER,nullable: true)]
    private ?string $coverLetterFileSize = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getManagement(): array
    {
        return $this->management;
    }

    public function setManagement(?array $management): self
    {
        $this->management = $management;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getJobAd(): ?JobAd
    {
        return $this->jobAd;
    }

    public function setJobAd(?JobAd $jobAd): self
    {
        $this->jobAd = $jobAd;

        return $this;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getCvFile(): ?File
    {
        return $this->cvFile;
    }

    /**
     * @param File|null $cvFile
     */
    public function setCvFile(?File $cvFile): void
    {
        $this->cvFile = $cvFile;

        if(null !==$cvFile)
        {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getCvFileName(): ?string
    {
        return $this->cvFileName;
    }

    public function setCvFileName(?string $cvFileName): void
    {
        $this->cvFileName = $cvFileName;
    }

    public function getCvFileSize(): ?string
    {
        return $this->cvFileSize;
    }

    public function setCvFileSize(?string $cvFileSize): void
    {
        $this->cvFileSize = $cvFileSize;
    }

    public function getCvFileUrl(): ?string
    {
        return $this->cvFileUrl;
    }

    #[ORM\PreFlush]
    public function setCvFileUrl(): self
    {

        if($this->getCvFileName()){

            $this->cvFileUrl = $_ENV['AWS_S3_FILE_URL']."/".$this->getCvFileName();
        } else{
            $this->cvFileUrl = null;
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getCoverLetterFile(): ?File
    {
        return $this->coverLetterFile;
    }

    /**
     * @param File|null $coverLetterFile
     */
    public function setCoverLetterFile(?File $coverLetterFile): void
    {
        $this->coverLetterFile = $coverLetterFile;

        if(null !== $coverLetterFile)
        {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getCoverLetterFileName(): ?string
    {
        return $this->coverLetterFileName;
    }

    public function setCoverLetterFileName(?string $coverLetterFileName): void
    {
        $this->coverLetterFileName = $coverLetterFileName;
    }


    public function getCoverLetterFileUrl(): ?string
    {
        return $this->coverLetterFileUrl;
    }

    #[ORM\PreFlush]
    public function setCoverLetterFileUrl(): self
    {
        if($this->getCoverLetterFileName())
        {
            $this->coverLetterFileUrl = $_ENV['AWS_S3_FILE_URL']."/".$this->getCoverLetterFileName();
        }else{
            $this->coverLetterFileUrl = null;
        }
        return $this;
    }

    public function getCoverLetterFileSize(): ?string
    {
        return $this->coverLetterFileSize;
    }

    public function setCoverLetterFileSize(?string $coverLetterFileSize): self
    {
        $this->coverLetterFileSize = $coverLetterFileSize;

        return $this;
    }

}
