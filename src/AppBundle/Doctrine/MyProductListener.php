<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\MyProduct;
use Psr\Log\LoggerInterface;

/**
 * MyProductListener ist a registred Service in services.yml
 * Demo of EventListener: http://symfony.com/doc/current/doctrine/event_listeners_subscribers.html
 */
class MyProductListener
{
    /**
     * Logs where saved in var/logs/dev.log
     * @var LoggerInterface
     */
    private $logger;

    //using TypeHint for autowire
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Listening on a single Event: in this case: Doctrine...LifecycleEventArgs
     * whenever a persist() on an object ist called
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        // only act on some "Product" entity
        if (!$object instanceof MyProduct) {
            return;
        }

        //$objectManager = $args->getObjectManager();
        // ... do something with the Product
        // $meta = $objectManager->getClassMetadata($object);
        $entity = $args->getEntity();
        $this->logger->info(sprintf('MyProductListener: EventListener: postPersist(): New MyProduct created: %s, %s ', $entity->getName(), $entity->getDescription()));
        //TODO some more stuff with the Product...
    }

}