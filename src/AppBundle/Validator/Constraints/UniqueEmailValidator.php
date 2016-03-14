<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    protected $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        $isEmailUnique = $this->manager->findUserByEmail($value);
        if ($isEmailUnique !== null) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ value }}' => $value
                ]
            );
        }
    }
}
