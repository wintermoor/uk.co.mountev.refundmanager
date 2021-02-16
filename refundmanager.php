<?php

require_once 'refundmanager.civix.php';
//use CRM_Refundmanager_ExtensionUtil as E;
use CRM_Refundmanager_CreditNote as CN;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function refundmanager_civicrm_config(&$config) {
  _refundmanager_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function refundmanager_civicrm_xmlMenu(&$files) {
  _refundmanager_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function refundmanager_civicrm_install() {
  _refundmanager_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function refundmanager_civicrm_postInstall() {
  _refundmanager_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function refundmanager_civicrm_uninstall() {
  _refundmanager_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function refundmanager_civicrm_enable() {
  _refundmanager_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function refundmanager_civicrm_disable() {
  _refundmanager_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function refundmanager_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _refundmanager_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function refundmanager_civicrm_managed(&$entities) {
  _refundmanager_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function refundmanager_civicrm_caseTypes(&$caseTypes) {
  _refundmanager_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function refundmanager_civicrm_angularModules(&$angularModules) {
  _refundmanager_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function refundmanager_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _refundmanager_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function refundmanager_civicrm_entityTypes(&$entityTypes) {
  _refundmanager_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function refundmanager_civicrm_themes(&$themes) {
  _refundmanager_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
 * function refundmanager_civicrm_preProcess($formName, &$form) {
 *
} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
 * function refundmanager_civicrm_navigationMenu(&$menu) {
 * _refundmanager_civix_insert_navigation_menu($menu, 'Mailings', array(
 * 'label' => E::ts('New subliminal message'),
 * 'name' => 'mailing_subliminal_message',
 * 'url' => 'civicrm/mailing/subliminal',
 * 'permission' => 'access CiviMail',
 * 'operator' => 'OR',
 * 'separator' => 0,
 * ));
 * _refundmanager_civix_navigationMenu($menu);
} // */

/**
 * Implements hook_civicrm_buildForm().
 *
 * Set a default value for an event price set field.
 *
 */
function refundmanager_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contribute_Form_Contribution') {
    $cnForId = CRM_Utils_Request::retrieve('cnforid', 'Positive', $form);
    if ($cnForId) {
      $form->add('hidden', 'is_creditnote_for', $cnForId);
      $invoiceNum = CRM_Contribute_BAO_Contribution::getInvoiceNumber($cnForId);
      if ($form->getAction() == CRM_Core_Action::ADD) {
        $form->setTitle(E::ts("Create Credit Note for Payment '%1'", [1 => $invoiceNum]));
      }
    }
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 */
function refundmanager_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  $result = [];
  if ($formName == 'CRM_Contribute_Form_Contribution' && $form->getAction() == CRM_Core_Action::ADD && !empty($fields['is_creditnote_for'])) {
    $totalTaxAmount = CN::addTax($fields['total_amount'], $fields['financial_type_id']);
    $result = CN::validateAmount($totalTaxAmount, $fields['is_creditnote_for']);
  }
  if ($formName == 'CRM_Contribute_Form_Contribution' && $form->getAction() == CRM_Core_Action::UPDATE) {
    $totalTaxAmount = CN::addTax($fields['total_amount'], $fields['financial_type_id']);
    $sourceContributionId = CN::isaCreditNote($fields['id']);
    if ($sourceContributionId) {
      $result = CN::validateAmount($totalTaxAmount, $sourceContributionId, $fields['id']);
    }
  }
  if (!empty($result) && !empty($result['is_error'])) {
    if (!empty($errors['_qf_default'])) {
      $errors['_qf_default'] .= $result['error'];
    }
    else {
      $errors['_qf_default'] = $result['error'];
    }
  }
}

function refundmanager_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
  if ($objectName == 'Contribution' && $op == 'contribution.selector.row') {
    $creditnotePrefix = CRM_Contribute_BAO_Contribution::checkContributeSettings('credit_notes_prefix', TRUE);
    if ($creditnotePrefix && !empty($values['id'])) {
      $contributionTotalAmount = CRM_Core_DAO::getFieldValue('CRM_Contribute_BAO_Contribution', $values['id'], 'total_amount');
      if ($contributionTotalAmount > 0) {
        $links[] = array(
          'name'  => E::ts('Create Credit Note'),
          'url'   => 'civicrm/contact/view/contribution',
          'qs'    => 'reset=1&action=add&cnforid=%%id%%&cid=%%cid%%&context=%%cxt%%',
          'title' => E::ts('Create a Credit Note'),
          'class' => 'no-popup',
        );
      }
    }
  }
}

function refundmanager_civicrm_pre($op, $objectName, $id, &$params) {
  // For credit note create - validate and populate invoice number
  if ($objectName == 'Contribution' && $op == 'create' && !empty($params['is_creditnote_for'])) {
    $result = CN::getNextInvoiceNum($params['total_amount'], $params['is_creditnote_for']);
    if (!empty($result['is_error'])) {
      throw new CRM_Core_Exception($result['error']);
    }
    elseif (!empty($result['invoice_number'])) {
      $params['invoice_number'] = $result['invoice_number'];
      // use creditnote_id column temporarily to store source contribution id.
      // So later in post process we could properly store it, when we have the contribution id as well.
      $params['creditnote_id'] = $params['is_creditnote_for'];
    }
  }
  // For credit note update - just validate
  if ($objectName == 'Contribution' && $op == 'edit') {
    $sourceContributionId = CN::isaCreditNote($id);
    if ($sourceContributionId) {
      $result = CN::getNextInvoiceNum($params['total_amount'], $sourceContributionId, $id);
      if (!empty($result['is_error'])) {
        throw new CRM_Core_Exception($result['error']);
      }
    }
  }
}

function refundmanager_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Contribution' && $op == 'create') {
    if (preg_match('/^CN_/', $objectRef->invoice_number) && !empty($objectRef->creditnote_id)) {
      CN::addEntry($objectRef->creditnote_id, $objectId);
      // As we dealing with credit note itself, set the core creditnote_id column back to null,
      // as it's intended for storing credit note, not otherway round.
      CRM_Core_DAO::setFieldValue('CRM_Contribute_DAO_Contribution', $objectId, 'creditnote_id', 'NULL');
    }
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 *
 */
function refundmanager_civicrm_alterMailParams(&$params, $context) {
  if ($context == 'messageTemplate' && $params['valueName'] == 'contribution_invoice_receipt') {
    $tplParams =& $params['tplParams'];
    $contributionID = $tplParams['id'];
    if (CN::isaCreditNote($contributionID)) {
      $tplParams['isCreditNote'] = 1;
    }
  }
}
