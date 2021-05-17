<?php

namespace Drupal\og_market\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\og\Og;
use Drupal\og\Entity\OgRole;
use Drupal\og\MembershipManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\transaction\Entity\Transaction;
use Drupal\transaction\Entity\TransactionOperation;

/**
 * An example action covering most of the possible options.
 *
 * If type is left empty, action will be selectable for all
 * entity types.
 *
 * @Action(
 *   id = "renew_license_one_month",
 *   label = @Translation("Renew - one month"),
 *   type = "commerce_license",
 *   confirm = TRUE,
 *   requirements = {
 *     "_permission" = "some permission",
 *     "_custom_access" = TRUE,
 *   },
 * )
 */

class RenewLicenseOneMonth extends ViewsBulkOperationsActionBase {
	
  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

   /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /*
     * All config resides in $this->configuration.
     * Passed view rows will be available in $this->context.
     * Data about the view used to select results and optionally
     * the batch context are available in $this->context or externally
     * through the public getContext() method.
     * The entire ViewExecutable object  with selected result
     * rows is available in $this->view or externally through
     * the public getView() method.
     */


	$currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	$node = \Drupal::routeMatch()->getParameter('node');
  $nid = $node->id();
  $title = $this->$node->title();
	$rid = 'group_member'; 
	
	$piggyId = array(
	  'target_id' => $nid,
    'target_type' => 'node'
	);
	
	$role_name = 'node-workgroup-content_creator';
	$credits = -100;
	$transaction_type = 'piggy_bank';
	
	$balance = $this->transactionService->getLastExecutedTransaction($title, $transaction_type)->getBalance();
	
	
	if ($balance < 100){
		echo '<script>alert("You do not have enough credits on you account to make this transaction. <br> You can buy more credits from your group page.<br><em>It costs 100 credits to extend the membership with one month for one person.</em>")</script>';	
	}
	else {
		$transaction = Transaction::create(array(      
    	'id' => NULL,
    	'target_entity' => $piggyId,
    	'type' => 'piggy_bank',
    	'target_type' => 'workgroup',
    	'field_amount' => $credits,
    	'field_log_message' => 'Activated member - 1 month',
    	'uid' => $currentUser->id(),
    	'status' => TRUE,
    ));
      
  	$transaction->save();		
  	
  	$expiredatestamp = $entity->get('field_expiration')->getString();
		$expiredate = date('d-m-Y H:i:s', $expiredatestamp);
		$today = date('d-m-Y H:i:s'); 
  	
  	if ($expiredate < $today) {
		
		$newdatestamp = strtotime($today."+ 1 month");
						
		} else {
		
		$newdatestamp = strtotime($expiredate."+ 1 month");
				
		}
		
		$entity->field_expiration = $newdatestamp;
		$entity->save();
		
		$role_id = implode('-', [
      $this->$entity->$membership->getGroupEntityType(),
      $this->$entity->$membership->getGroupBundle(),
      $role_name,
    ]);
    // Only add the role if it is valid and doesn't exist yet.
    $role = OgRole::load($role_id);
    if ($membership->isRoleValid($role) && !$membership->hasRole($role_id)) 			{
      $membership->addRole($role)->save();
    	}
		
		$this->$entity->$user->addRole($rid);
		$entity->save();
		}
	}
	


  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
	  
	  return TRUE;
   
  }

}
