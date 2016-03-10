<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use Cvele\MultiTenantBundle\Helper\TenantHelper;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Event\EntityEvent;

trait ObjectManagerTrait
{
	 /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine object manager for database interaction
     * @var ObjectManager
     */
    protected $om;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $className;

    /**
     * MultiTenantBundle helper service
     * @var TenantHelper
     */
    protected $tenantHelper;

    public function __construct(EventDispatcherInterface $dispatcher, ObjectManager $om, TenantHelper $tenantHelper, $className)
    {
        $this->dispatcher   = $dispatcher;
        $this->om           = $om;
        $this->tenantHelper = $tenantHelper;
        $this->className    = $className;
        $this->repo         = $om->getRepository($className);
    }

    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    public function getObjectManager()
    {
      return $this->om;
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function createClass()
    {
        $className = $this->className;
        $instance = new $className();

        return $instance;
    }

    public function findByTenant(Tenant $tenant)
    {
        return $this->getRepo()->findBy(['tenant'=>$tenant]);
    }

    public function findById($id)
    {
        $filters = ['id' => $id];
        if ($this->createClass() instanceof TenantAwareEntityInterface) {
            $tenant = $this->tenantHelper->getCurrentTenant();
            if ($tenant === null) {
              throw new \Exception('User not authorized or missing tenant.');
            }
            $filters['tenant'] = $tenant->getId();
        }

        $entity = $this->getRepo()->findOneBy($filters);
        if ($entity === null) {
            return false;
        }

        return $entity;
    }

    public function save($entity, $event_name = 'app.entity.saved')
    {
        $this->om->persist($entity);
        $this->om->flush();
        $this->dispatch($event_name, $entity);
    }

    public function delete($entity, $event_name = 'app.entity.deleted')
    {
        $this->om->remove($entity);
        $this->om->flush();
        $this->dispatch($event_name, $entity);
    }

    private function dispatch($event_name, $entity)
    {
        if ($event_name !== false)
        {
            $this->dispatcher->dispatch($event_name, new EntityEvent($entity));
        }
    }

}
