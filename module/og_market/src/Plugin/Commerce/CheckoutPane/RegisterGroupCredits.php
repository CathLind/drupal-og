<?php

namespace Drupal\og_market\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_store\Entity\Store;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_cart\Event\OrderItemComparisonFieldsEvent;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\transaction\Entity\Transaction;
use Drupal\transaction\Entity\TransactionOperation;
use Drupal\node\Entity\Node;



/**
 * Provides an extra checkout pane for Register group manager.
 *
 * @CommerceCheckoutPane(
 *   id = "register_group_credits",
 *   label = @Translation("Register Group credits"),
 * )
 */
class RegisterGroupCredits extends CheckoutPaneBase {
	
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;


  /**
   * The current order.
   *
   * @var Drupal\commerce_order\Entity\OrderItem
   */
  protected $order;

public function isVisible() {
  // Check whether the order has an item that requires the disclaimer message.

  return TRUE;
}
	
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
     
  $pane_form['group_name'] = [
      '#type' => 'textfield',
      '#title' => 'Group name',
      '#size' => 60,
      '#description' => $this->t('The name of the workgroup you are purchasing credits for.'),
      '#required' => TRUE,
    ]; 
   
  $pane_form['piggy_nid'] = [
      '#type' => 'textfield',
      '#title' => 'Piggy Bank ID.',
      '#size' => 60,
      '#description' => $this->t('The id number for your workgroups Piggy Bank. Can be found on your Piggy Bank page.'),
      '#required' => TRUE,
    ]; 
   
  $pane_form['actions']['register'] = [
      '#type' => 'submit',
      '#value' => $this->t('Transfer Credit to Piggy Bank'),
      '#name' => 'og_market_credits',
    ];

    return $pane_form;  
 	}
 	
 	  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
  	
  	$credits = NULL;
  	
    $currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $values = $form_state->getValue($pane_form['#parents']);
    $order = Order::load($this->order->order_number->value); 

    foreach ($this->order->getItems() as $order_item) {
      $ocredits = 0;
      $purchased_entity = $order_item->getPurchasedEntity();
      $title = $purchased_entity->getTitle();
 
        if ($title == '100 credits') {
        $ocredits = 100;
			}
		elseif ($title == '300 credits') {
        $ocredits = 300;
			}
		elseif ($title == '600 credits') {
        $ocredits = 600;
			}
		elseif ($title == '1200 credits') {
        $ocredits = 1200;
			}
		elseif ($title == '3600 credits') {
        $ocredits = 3600;
			}
 
 	  $credits = $credits + $ocredits;
		};
     
 
	$piggyId = array(
	    'target_id' => $values['piggy_nid'],
        'target_type' => 'node'
			);

	$transaction = Transaction::create(array(      
      'id' => NULL,
      'target_entity' => $piggyId,
      'type' => 'piggy_bank',
      'target_type' => 'workgroup',
      'field_amount' => $credits,
      'field_log_message' => 'Bought credits from store',
      'uid' => $currentUser->id(),
      'status' => TRUE,
      ));
      
    $transaction->save();
 
  }

}
