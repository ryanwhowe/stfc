<?php

namespace App\Entity;

use App\Repository\ResourceDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResourceDetailRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"scopley_id"})})
 */
class ResourceDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $scopleyId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $requestUrl;

    /**
     * @ORM\Column(type="json")
     */
    private $json = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_updated;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=ResourceType::class, inversedBy="resourceDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScopleyId(): ?int
    {
        return $this->scopleyId;
    }

    public function setScopleyId(int $scopleyId): self
    {
        $this->scopleyId = $scopleyId;

        return $this;
    }

    public function getRequestUrl(): ?string
    {
        return $this->requestUrl;
    }

    public function setRequestUrl(string $requestUrl): self
    {
        $this->requestUrl = $requestUrl;

        return $this;
    }

    public function getJson(): ?array
    {
        return $this->json;
    }

    public function setJson(array $json): self
    {
        $this->json = $json;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeInterface $last_updated): self
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getType(): ?ResourceType
    {
        return $this->type;
    }

    public function setType(?ResourceType $type): self
    {
        $this->type = $type;

        return $this;
    }

}
