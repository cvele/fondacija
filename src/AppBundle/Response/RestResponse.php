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

    public function createResponseArray($data, $entity, $include, $pagerOptions = [])
    {
        $fractal = new Manager();
        $fractal->parseIncludes($include);
        $fractal->setSerializer(new ArraySerializer());
        $transformer = $this->transformerFactory->get($entity);

        if ($data instanceof \Doctrine\ORM\Query) {
            $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($data);
            $pager = new \Pagerfanta\Pagerfanta($adapter);
            $pager->setMaxPerPage($pagerOptions['limit']);
            $pager->setCurrentPage($pagerOptions['page']);
            $results = $pager->getCurrentPageResults();

            $resource = new Collection($results, $transformer);
            $resource->setPaginator(new \League\Fractal\Pagination\PagerfantaPaginatorAdapter($pager, function($page){
                $url = $_SERVER['REQUEST_URI'];
                $url = str_replace("page=" . $_GET["page"], "page=" . $page, $url);
                $url = preg_replace("#&+#", "&", $url);
                return $url;
            }));
        } elseif (is_array($data)) {
            $resource = new Collection($data, $transformer);
        } else {
            $resource = new Item($data, $transformer);
        }
        $data = $fractal->createData($resource)->toArray();
        return $data;
    }
}
