<?php

namespace AppBundle\Request;

class ParseJsonRequest
{
    protected function parseRequest(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $this->validateRequest($payload, $request->getMethod());

        $entity = $this->manager->createClass();
        $entityManger = $this->getDoctrine()->getManager();
        $entityMetadata = $entityManger->getClassMetadata(get_class($entity));
        foreach ($payload as $parameter => $value) {
            try {
                $filedTargetClass = $entityMetadata->getAssociationTargetClass($parameter);
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            if ($filedTargetClass === false || $filedTargetClass === null) {
                continue;
            }

            $targetEntity = $entityManger->getRepository($filedTargetClass)->find($value);

            $payload[$parameter] = $targetEntity;
        }


        $error = json_last_error();
		if ($error && $error !== JSON_ERROR_NONE) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, sprintf("Invalid json payload supplied. Error: '%s'", (string) $error));
		}

        if (!is_array($payload) && !is_object($payload)) {
			throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "Invalid json payload supplied. Error: 'JSON must be array or object'. '%s'", $this->get("request")->getContent());
		}

        return $payload;
    }
}
