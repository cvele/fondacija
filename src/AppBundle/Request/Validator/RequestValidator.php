<?php

namespace AppBundle\Request\Validator;

use Symfony\Component\Validator\Constraints;
use AppBundle\Validator\Constraints\FileNotExist;
use AppBundle\Request\Exception\PayloadValidationException;

abstract class RequestValidator
{
    protected $validator;
    protected $payload;
    protected $httpMethod;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    public function validate()
    {
        if (in_array(strtolower($this->httpMethod), ['post', 'put', 'patch'])) {
            $errors = $this->validator->validate($this->payload, $this->rules($this->httpMethod));
            if (count($errors) !== 0) {
                throw new PayloadValidationException($errors);
            }
        }
    }

    abstract function rules($httpMethod);
}
