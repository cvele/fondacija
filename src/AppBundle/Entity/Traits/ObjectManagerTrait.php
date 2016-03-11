<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Cvele\MultiTenantBundle\Model\TenantAwareEntityInterface;
use Cvele\MultiTenantBundle\Model\TenantAwareUserInterface;
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

    public function getClassName()
    {
        return $this->className;
    }

    public function createClass()
    {
        $className = $this->className;
        $instance = new $className();

        return $instance;
    }

    public function findByTenant(Tenant $tenant)
    {
        return $this->getRepo()->findAllWithTenant(['tenantId' => $tenant]);
    }

    public function findAll()
    {
        $class = $this->createClass();
        if ($class instanceof TenantAwareEntityInterface || $class instanceof TenantAwareUserInterface) {
            $tenant = $this->tenantHelper->getCurrentTenant();
            if ($tenant === null) {
              throw new \Exception('User not authorized or missing tenant.');
            }
            return $this->getRepo()->findAllWithTenant($tenant->getId());
        } else {
            return $this->getRepo()->findAll();
        }
    }

    public function findById($id)
    {
        $class = $this->createClass();
        if ($class instanceof TenantAwareEntityInterface || $class instanceof TenantAwareUserInterface) {
            $tenant = $this->tenantHelper->getCurrentTenant();
            if ($tenant === null) {
              throw new \Exception('User not authorized or missing tenant.');
            }
            $query = $this->getRepo()->findByIdAndTenant((int) $id, $tenant->getId());
        } else {
            $query = $this->getRepo()->findById($id);
        }


        return $query;
    }


    public function applyPayloadToEntity($entity, $payload)
    {
        $entityInstance = new $entity;
        foreach ($payload as $name => $value) {
            if ($name !== 'id' && $name !== 'tenant' && $name != 'user') {
                $setter = 'set' . ucfirst($name);
                if (method_exists($entity, $setter)) {
                    $entityInstance->$setter($value);
                }
            }
        }

        return $entityInstance;
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
