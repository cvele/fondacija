<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileNotExistValidator extends ConstraintValidator
{
    protected $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        $file_exists = $this->manager->findById($value);
        if ($file_exists === null) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ value }}' => $value
                ]
            );
        }
    }
}
