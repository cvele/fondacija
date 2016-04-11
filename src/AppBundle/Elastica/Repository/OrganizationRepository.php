<?php

namespace AppBundle\Elastica\Repository;

use FOS\ElasticaBundle\Repository;
use Elastica\Filter;
use Elastica\Query;

class OrganizationRepository extends Repository
{
    public function findWithTenant($searchText, $tenantId, $sortDirection = 'desc')
    {
        $boolFilter = new Filter\Bool();
        $boolFilter->addMust(new Filter\Term(['tenant.id' => $tenantId]));

        $boolQuery = new Query\Bool();
        if ($searchText !== null) {
            $fieldQuery = new Query\MultiMatch();
            $fieldQuery->setQuery("*".$searchText."*");
            $fieldQuery->setFields(['name', 'description']);

            $boolQuery->addMust($fieldQuery);
        }

        $filtered = new Query\Filtered($boolQuery, $boolFilter);

        $query = Query::create($filtered);
        $query->addSort(['created_at' => $sortDirection]);

        return $this->findPaginated($query);
    }
}
