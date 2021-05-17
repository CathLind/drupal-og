<?php

namespace Drupal\og_market\EventSubscriber;

use Drupal\og\MembershipManagerInterface;
use Drupal\og\OgAccessInterface;
use Drupal\og\OgMembershipInterface;
use Drupal\og\Og;
use Drupal\og\Entity\OgRole;
use Drupal\og\OgRoleInterface;
use Drupal\og_market\Event\CreateWorkgroupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds a log entry when a new workgroup is created.
 * TODO Give the owner the management role.
 */
class CreateWorkgroupManager implements EventSubscriberInterface {

  /**
   * Create a manager for the new workgroup
   *
   * @param \Drupal\og_market\Event\CreateWorkgroupEvent $event
   */
  public function onCreateWorkgroup(CreateWorkgroupEvent $event) {
    $entity = $event->getEntity();
    $entitytype = $entity->getType();
    $workgroup = 'workgroup';
 
      if ($entitytype == $workgroup){
      $owner = $entity->getOwner()->getDisplayName();

      \Drupal::logger('og_market')->notice('New @type: @title. Created by: @owner',
        array(
          '@type' => $entitytype,
          '@title' => $entity->label(),
          '@owner' => $owner
          ));
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CreateWorkgroupEvent::CREATE_WORKGROUP][] = ['onCreateWorkgroup'];
    return $events;
  }
}