<?php

namespace Rvdlee\AsanaCLI\Model;

abstract class AbstractModel
{
    public const MAPPINGS = [];

    /**
     * The instanced classname
     * @var AbstractModel|string
     */
    protected string $class;

    public function __construct(array $data)
    {
        if ($data !== []) {
            $this->fill($data);
        }
    }

    public function fill(array $data)
    {
        $this->class = get_class($this);

        if (defined(sprintf('%s::MAPPINGS', $this->class)) && $this->class::MAPPINGS !== []) {
            foreach ($data as $mapping => $value) {
                $setter = sprintf('set%s', ucfirst($this->class::MAPPINGS[$mapping]));
                if (method_exists($this->class, $setter)) {
                    $this->{$setter}($value);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach (self::MAPPINGS as $mapping => $variable) {
            $getter = sprintf('get%s', ucfirst($variable));
            if (method_exists($this, $getter)) {
                $data[$mapping] = $this->{$getter}();
            }
        }

        return $data;
    }
}