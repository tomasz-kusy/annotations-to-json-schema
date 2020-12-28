<?php

namespace TKusy\JSchema\Metadata;

use Symfony\Component\PropertyInfo\Type;

class PropertyMetadata
{
    /**
     * @var string
     */
    private $thisClass;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type[]
     */
    private $type;


    /**
     * @return string
     */
    public function getThisClass(): string
    {
        return $this->thisClass;
    }

    /**
     * @param string $thisClass
     * @return PropertyMetadata
     */
    public function setThisClass(string $thisClass): PropertyMetadata
    {
        $this->thisClass = $thisClass;
        return $this;
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
     * @return PropertyMetadata
     */
    public function setName(string $name): PropertyMetadata
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Type[]
     */
    public function getType(): array
    {
        return $this->type;
    }

    /**
     * @param Type[] $type
     * @return PropertyMetadata
     */
    public function setType(array $type): PropertyMetadata
    {
        $this->type = $type;
        return $this;
    }
}
