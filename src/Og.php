<?php

/**
 * @file
 * Contains \Drupal\og\Og.
 */

namespace Drupal\og;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldConfigInterface;

/**
 * A static helper class for OG.
 */
class Og {

  /**
   * Check if the given entity is a group.
   *
   * @param string $entity_type_id
   * @param string $bundle
   *
   * @return bool
   *   True or false if the given entity is group.
   */
  public static function isGroup($entity_type_id, $bundle_id) {
    return static::groupManager()->isGroup($entity_type_id, $bundle_id);
  }

  /**
   * Sets an entity type instance as being an OG group.
   *
   * @param string $entity_type_id
   * @param string $bundle_id
   */
  public static function addGroup($entity_type_id, $bundle_id) {
    return static::groupManager()->addGroup($entity_type_id, $bundle_id);
  }

  /**
   * Removes an entity type instance as being an OG group.
   *
   * @param string $entity_type_id
   * @param string $bundle_id
   */
  public static function removeGroup($entity_type_id, $bundle_id) {
    return static::groupManager()->removeGroup($entity_type_id, $bundle_id);
  }

  /**
   * Return TRUE if field is a group audience type.
   *
   * @param $field_config
   *   The field config object.
   *
   * @return bool
   */
  public static function isGroupAudienceField(FieldConfigInterface $field_config) {
    return $field_config->getType() === 'og_membership_reference';
  }

  /**
   * Returns the group manager instance.
   *
   * @return \Drupal\og\GroupManager
   */
  public static function groupManager() {
    // @todo store static reference for this?
    return \Drupal::service('og.group.manager');
  }

}
