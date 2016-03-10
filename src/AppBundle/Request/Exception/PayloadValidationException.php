<?php

namespace AppBundle\Request\Exception;

class PayloadValidationException extends \Exception
{
    protected $violations;

    public function __construct($violations)
    {
        $this->violations = $violations;
    }

    public function getErrors() {
        $requestViolations = [];
        foreach($this->violations as $violation) {
            $requestViolations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'invalidValue' => $violation->getInvalidValue()
            ];
        }

        return $requestViolations;
    }
}
