<?php

namespace Rvdlee\AsanaCLI\Model;

class Workspace extends AbstractModel
{
    public const MAPPINGS = [
        'gid' => 'gid',
        'name' => 'name',
        'resource_type' => 'resourceType',
    ];

    /**
     * @var string
     */
    protected string $gid;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $resourceType;

    /**
     * @return string
     */
    public function getGid(): string
    {
        return $this->gid;
    }

    /**
     * @param string $gid
     */
    public function setGid(string $gid): void
    {
        $this->gid = $gid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @param string $resourceType
     */
    public function setResourceType(string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }
}