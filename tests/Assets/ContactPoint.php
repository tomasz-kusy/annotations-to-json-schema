<?php

namespace TKusy\JSchema\Tests\Assets;

use Symfony\Component\Validator\Constraints as Assert;

class ContactPoint
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    protected $value;
}
