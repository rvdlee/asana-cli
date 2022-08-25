<?php

namespace Rvdlee\AsanaCLI\Model;

class User extends AbstractModel
{
    public const MAPPINGS = [
        'gid' => 'gid',
        'name' => 'name',
        'resource_type' => 'resourceType',
    ];

    /**
     * @var string|null
     */
    protected ?string $gid;

    /**
     * @var string|null
     */
    protected ?string $name;

    /**
     * @var string|null
     */
    protected ?string $resourceType;

    public function __construct(array $data)
    {
        $this->gid = null;
        $this->name = null;
        $this->resourceType = null;

        parent::__construct($data);
    }

    public function toArray(): array
    {
        return [
            'gid' => $this->getGid(),
            'name' => $this->getName(),
            'resource_type' => $this->getResourceType(),
        ];
    }

    /**
     * @return string|null
     */
    public function getGid(): ?string
    {
        return $this->gid;
    }

    /**
     * @param string|null $gid
     */
    public function setGid(?string $gid): void
    {
        $this->gid = $gid;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    /**
     * @param string|null $resourceType
     */
    public function setResourceType(?string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }
}