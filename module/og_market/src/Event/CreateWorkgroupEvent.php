<?php

namespace Drupal\og_market\Event;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Defines the create group event.
 *
 */
 
class CreateWorkgroupEvent extends Event {

  const CREATE_WORKGROUP = 'create_workgroup.node.insert';

  /**
   * The node type entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;


  /**
   * Constructs a node insertion event object.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   *   The workgroup node.
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }
  
  /**
   * Get the group node.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  public function getEntity() {
   return $this->entity;
   }
}