<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
    public $message = "User with email address '{{ value }}' already exists.";

    public function validatedBy()
    {
        return 'unique_email';
    }
}
