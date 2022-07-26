<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits\Timestamplable;
use App\Repository\JobAdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobAdRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'job_ads')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
        'post'
    ],
    itemOperations:[
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
        'put',
        'delete'
    ],
    attributes: [
        'security' => "is_granted('ROLE_ADMIN')",
        'order' =>[
            'published'=> 'DESC'
        ]
    ],
    denormalizationContext: ['groups' => ['write:jobAd']],
)]
#[ApiFilter(SearchFilter::class,properties: ['title'=>'partial'])]
class JobAd
{
    use Timestamplable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:jobAd','write:jobAd'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    #[Groups(['write:jobAd'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    #[Groups(['write:jobAd'])]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    #[Groups(['write:jobAd'])]
    private ?string $contractType = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['write:jobAd'])]
    private array $requirements = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['write:jobAd'])]
    private array $tasks = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['write:jobAd'])]
    private ?string $wage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['write:jobAd'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Type('bool')]
    #[Groups(['write:jobAd'])]
    private ?bool $published = null;

    #[ORM\ManyToOne(inversedBy: 'jobAds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups('write:jobAd')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'jobAd', targetEntity: Contact::class, orphanRemoval: true)]
    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    private Collection $contacts;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type('int')]
    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    private ?int $companyId = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function setRequirements(?array $requirements): self
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(?array $tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }

    public function getWage(): ?string
    {
        return $this->wage;
    }

    public function setWage(?string $wage): self
    {
        $this->wage = $wage;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setJobAd($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getJobAd() === $this) {
                $contact->setJobAd(null);
            }
        }

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

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    #[ORM\PreFlush]
    public function updatePublishedDate(): void
    {
        if($this->isPublished()){
            $this->setPublishedAt(new \DateTimeImmutable());
        }
    }

}
