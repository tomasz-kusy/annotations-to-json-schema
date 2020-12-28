<?php

namespace TKusy\JSchema\Tests\Assets;

use Symfony\Component\Validator\Constraints as Assert;

class Patient
{
    /**
     * @Assert\Valid
     * @Assert\NotNull
     * @var Identifier[]
     */
    private $identifier = [];

    /**
     * @Assert\Valid
     * @var HumanName[]
     */
    private $name = [];

    /**
     * @Assert\Valid
     * @var ContactPoint[]
     */
    private $telecom = [];

    /**
     * @Assert\Valid
     * @var Address[]
     */
    private $address = [];

    /**
     * @Assert\NotBlank
     * @Assert\Choice({"M", "F", "UN"})
     * @var string
     */
    private $gender = 'UN';

    /**
     * @Assert\Date()
     * @var string|null
     */
    private $birthDate;
}
