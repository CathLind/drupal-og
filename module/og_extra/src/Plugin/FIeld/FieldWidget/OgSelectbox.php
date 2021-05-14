<?php

declare(strict_types = 1);

namespace Drupal\og\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'og_selectbox' widget.
 *
 * @FieldWidget(
 *   id = "og_selectbox",
 *   label = @Translation("OG context selectbox"),
 *   description = @Translation("A selectbox widget that takes OG group context into account"),
 *   field_types = {
 *     "og_standard_reference",
 *     "og_membership_reference"
 *   },
 *   multiple_values = TRUE
 * )
 */
class OgSelectbox extends OptionsButtonsWidget {

  /**
   * {@inheritdoc}
   */
   
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $parent = parent::formElement($items, $delta, $element, $form, $form_state);
    $parent['target_id']['#selection_handler'] = 'og:default';
    $parent['target_id']['#selection_settings']['field_mode'] = 'default';

    return $parent;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $parent_form = parent::form($items, $form, $form_state, $get_delta);
    $parent_form['other_groups'] = [];
    
    return $parent_form;
  }

}
