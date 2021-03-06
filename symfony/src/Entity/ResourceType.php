<?php

namespace App\Entity;

use App\Repository\ResourceTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=ResourceTypeRepository::class)
 */
class ResourceType
{

    public function __construct() {
        $this->created = new DateTime();
        $this->resources = new ArrayCollection();
        $this->resourceDetails = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="type", orphanRemoval=true)
     */
    private $resources;

    /**
     * @ORM\Column(type="integer")
     */
    private $urlType;

    /**
     * @ORM\OneToMany(targetEntity=ResourceDetail::class, mappedBy="typeId", orphanRemoval=true)
     */
    private $resourceDetails;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

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

    /**
     * @return Collection|Resource[]
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): self
    {
        if (!$this->resources->contains($resource)) {
            $this->resources[] = $resource;
            $resource->setType($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->removeElement($resource)) {
            // set the owning side to null (unless already changed)
            if ($resource->getType() === $this) {
                $resource->setType(null);
            }
        }

        return $this;
    }

    public function getUrlType(): ?int
    {
        return $this->urlType;
    }

    public function setUrlType(int $urlType): self
    {
        $this->urlType = $urlType;

        return $this;
    }

    /**
     * @return Collection|ResourceDetail[]
     */
    public function getResourceDetails(): Collection
    {
        return $this->resourceDetails;
    }

    public function addResourceDetail(ResourceDetail $resourceDetail): self
    {
        if (!$this->resourceDetails->contains($resourceDetail)) {
            $this->resourceDetails[] = $resourceDetail;
            $resourceDetail->setTypeId($this);
        }

        return $this;
    }

    public function removeResourceDetail(ResourceDetail $resourceDetail): self
    {
        if ($this->resourceDetails->removeElement($resourceDetail)) {
            // set the owning side to null (unless already changed)
            if ($resourceDetail->getTypeId() === $this) {
                $resourceDetail->setTypeId(null);
            }
        }

        return $this;
    }
}
