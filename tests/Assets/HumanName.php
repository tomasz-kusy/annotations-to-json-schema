<?php

namespace TKusy\JSchema\Tests\Assets;

use Symfony\Component\Validator\Constraints as Assert;


class HumanName
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    protected $family;

    /**
     * @Assert\Count(min=1)
     * @var string[]
     */
    protected $given = [];
}
