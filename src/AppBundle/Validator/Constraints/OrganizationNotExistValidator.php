<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrganizationNotExistValidator extends ConstraintValidator
{
    protected $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        $exists = $this->manager->findById($value);
        if ($exists === null) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ value }}' => $value
                ]
            );
        }
    }
}
