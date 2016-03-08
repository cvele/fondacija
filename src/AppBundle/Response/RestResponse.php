<?php

namespace AppBundle\Response;

use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class RestResponse
{
    public $transformerFactory;

    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    public function createResponseArray($data, $entity)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());
        $transformer = $this->transformerFactory->get($entity);
        if (is_array($data)) {
            $resource = new Collection($data, $transformer);
        } else {
            $resource = new Item($data, $transformer);
        }
        $data = $fractal->createData($resource)->toArray();
        return $data;
    }
}
