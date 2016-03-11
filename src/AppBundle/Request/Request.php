<?php

namespace AppBundle\Request;

use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class Request extends BaseRequest
{
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
          parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function getJsonPayload()
    {
        $payload = json_decode($this->getContent(), true);

        $error = json_last_error();
		if ($error && $error !== JSON_ERROR_NONE) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, sprintf("Invalid json payload supplied. Error: '%s'", (string) $error));
		}

        if (!is_array($payload) && !is_object($payload)) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, sprintf("Invalid json payload supplied. Error: 'JSON must be array or object'. '%s'", $this->getContent()));
		}

        return $payload;
    }
}
