<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\Tenant;
use League\Fractal\TransformerAbstract;

class TenantTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Tenant $tenant)
    {
        return [
            'id'        => (int) $tenant->getId(),
            'name'      => $tenant->getName()
        ];
    }
}
