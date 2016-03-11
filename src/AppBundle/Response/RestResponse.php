<?php

namespace AppBundle\Response;

use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\Request;

class RestResponse
{
    public $transformerFactory;

    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    public function createResponseArray($data, $entity, Request $request)
    {
        $this->request = $request;
        $fractal = new Manager();
        $fractal->parseIncludes($request->query->get('include', []));
        $fractal->setSerializer(new ArraySerializer());
        $transformer = $this->transformerFactory->get($entity);

        if ($data instanceof \Doctrine\ORM\Query) {
            $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($data);
            $pager = new \Pagerfanta\Pagerfanta($adapter);
            $pager->setMaxPerPage($request->query->get('limit', 10));
            $pager->setCurrentPage($request->query->get('page', 1));
            $results = $pager->getCurrentPageResults();

            $resource = new Collection($results, $transformer);
            $resource->setPaginator(new \League\Fractal\Pagination\PagerfantaPaginatorAdapter($pager, [$this, 'paginationRouter']));
        } elseif (is_array($data)) {
            $resource = new Collection($data, $transformer);
        } else {
            $resource = new Item($data, $transformer);
        }
        $data = $fractal->createData($resource)->toArray();
        return $data;
    }

    public function paginationRouter($page)
    {
        $url = $this->request->getUri();
        $url_parts = parse_url($url);
        parse_str($url_parts['query'], $params);
        $params['page'] =  $page;
        $url_parts['query'] = http_build_query($params);

        $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
        return $url;
    }
}
