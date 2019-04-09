<?php

namespace App\ProfileBundle\EventSubscriber;

use App\ProfileBundle\Event\ProfileEvent;
use App\ProfileBundle\Events as ProfileEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    /**
     * EventSubscriber constructor.
     *
     */
    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ProfileEvents::CREATE => 'onCreate',
            ProfileEvents::CREATE => 'onDelete',
            ProfileEvents::UPDATE => 'onUpdate',
        ];
    }

    /**
     * @param ProfileEvent $event
     */
    public function onCreate(ProfileEvent $event)
    {

    }

    /**
     * @param ProfileEvent $event
     */
    public function onDelete(ProfileEvent $event)
    {

    }

    /**
     * @param ProfileEvent $event
     */
    public function onUpdate(ProfileEvent $event)
    {

    }
}