<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    protected $class;

    public function __construct(EventDispatcherInterface $dispatcher, ObjectManager $om, $class)
    {
        $this->dispatcher = $dispatcher;
        $this->om         = $om;
        $this->class      = $class;
        $this->repo       = $om->getRepository($class);
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
        $class    = $this->class;
        $instance = new $class();

        return $instance;
    }

    public function findByTenant(Tenant $tenant)
    {
        return $this->getRepo()->findBy(['tenant'=>$tenant]);
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
