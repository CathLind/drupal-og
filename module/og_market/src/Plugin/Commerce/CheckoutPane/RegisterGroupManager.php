<?php

namespace Drupal\og_market\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Provides an extra checkout pane for Register group manager.
 *
 * @CommerceCheckoutPane(
 *   id = "register_group_manager",
 *   label = @Translation("Register Group manager"),
 * )
 */
class RegisterGroupManager extends CheckoutPaneBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    // This pane can only be shown at the end of checkout.
 
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    
 
  /** Get the group name from the user **/
      
    $pane_form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Group name',
      '#size' => 60,
      '#description' => $this->t('Provide a name for your workgroup. This can not be changed.'),
      '#required' => TRUE,
    ];
    
    $pane_form['actions'] = [
      '#type' => 'actions',
    ];
       
    $pane_form['actions']['register'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create group'),
      '#name' => 'og_market_create_group',
    ];

      
    return $pane_form;
 
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {

    $rid = 'group_ma';
    $rid2 = 'group_member';  
    $order = Order::load($this->order->order_number->value);
    
    foreach ($this->order->getItems() as $order_item) {
      $product_variation = $order_item->getPurchasedEntity();
      $ordertitle = $product_variation->getTitle();
         
      if ($ordertitle == '1 month') {
        $duration = 1;
			}
		  if ($ordertitle == '3 month') {
        $duration = 3;
			}
		  if ($ordertitle == '6 month') {
        $duration = 6;
			}
		  if ($ordertitle == '12 month') {
        $duration = 12;
			}
		}
     
    $startdate = new \DateTime();
    $expiration = $startdate->add(new \DateInterval('P' . $duration . 'M'));
  
    $currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); 
    $values = $form_state->getValue($pane_form['#parents']);
    $currentUser->field_expiration_date = $expiration->format('Y-m-d H:i:s');

    /** Add correct role to current user **/
 
    $currentUser->addRole($rid);
    $currentUser->addRole($rid2);
    $currentUser->save();  
   
    $node = Node::create(array(
      'nid' => NULL,
      'type' => 'workgroup',
      'title' => $values['title'],
      'uid' => 1,
      'status' => TRUE,
    ));

    $node->save();
    
  }
}
