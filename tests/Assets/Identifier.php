<?php

namespace TKusy\JSchema\Tests\Assets;

use Symfony\Component\Validator\Constraints as Assert;

class Identifier
{
    /**
     * @Assert\NotBlank
     */
    protected $type;

    protected $value;


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
