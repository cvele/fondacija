<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
    public $message = "User with email address '{{ value }}' already exists.";
    public $httpMethod = null;

    public function __construct($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    public function validatedBy()
    {
        return 'unique_email';
    }
}
