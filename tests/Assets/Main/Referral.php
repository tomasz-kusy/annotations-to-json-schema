<?php

namespace TKusy\JSchema\Tests\Assets\Main;

use TKusy\JSchema\Tests\Assets\Description;
use TKusy\JSchema\Tests\Assets\Patient;
use Symfony\Component\Validator\Constraints as Assert;

class Referral
{
    /**
     * @Assert\Length(min="22", max="22", allowEmptyString=false)
     * @var string
     */
    private $id;

    /**
     * @Assert\Date()
     * @var string
     */
    private $date;

    /**
     * @Assert\Choice({"02.10"})
     * @var string
     */
    private $type;

    /**
     * @Assert\Uuid
     * @Assert\NotBlank
     * @var string
     */
    private $organization;

    /**
     * @Assert\Valid
     * @Assert\NotBlank
     * @var Patient
     */
    private $patient;

    /**
     * @Assert\Choice({"UR"}),
     * @var string|null
     */
    private $priorityCode;

    /**
     * @Assert\Regex(pattern="/^\d\d$/")
     * @var string|null
     */
    private $department;

    /**
     * @Assert\NotBlank
     * @var float|null
     */
    private $dose;

    /**
     * @var Description[]|string|null
     */
    private $description;

    /**
     * @var bool
     */
    private $ready;

    /**
     * @var int
     */
    private $number;
}
