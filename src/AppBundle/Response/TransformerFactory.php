<?php

namespace AppBundle\Response;

class TransformerFactory
{

    public function get($entity)
    {
        $function = new \ReflectionClass($entity);
        $className = $function->getShortName();

        $transformerClassName = sprintf("AppBundle\\Response\\Transformer\\%sTransformer", $className);
        if (class_exists($transformerClassName) === false) {
            throw new \Exception(sprintf("Transformer class for entity %s does not exist.", $function->inNamespace()));
        }

        $transformerClass = new $transformerClassName();
        return $transformerClass;
    }
}
