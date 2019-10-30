<?php

require_once 'tests/phpunit/CiviTest/CiviUnitTestCase.php';

use CRM_Refundmanager_ExtensionUtil as E;
use Civi\Test\EndToEndInterface;
//use CiviUnitTestCase as CU;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - The global variable $_CV has some properties which may be useful, such as:
 *    CMS_URL, ADMIN_USER, ADMIN_PASS, ADMIN_EMAIL, DEMO_USER, DEMO_PASS, DEMO_EMAIL.
 *  - To spawn a new CiviCRM thread and execute an API call or PHP code, use cv(), e.g.
 *      cv('api system.flush');
 *      $data = cv('eval "return Civi::settings()->get(\'foobar\')"');
 *      $dashboardUrl = cv('url civicrm/dashboard');
 *  - This template uses the most generic base-class, but you may want to use a more
 *    powerful base class, such as \PHPUnit_Extensions_SeleniumTestCase or
 *    \PHPUnit_Extensions_Selenium2TestCase.
 *    See also: https://phpunit.de/manual/4.8/en/selenium.html
 *
 * @group e2e
 * @see cv
 */
class CRM_Refundmanager_CreditNoteTest extends \PHPUnit\Framework\TestCase implements EndToEndInterface {
  //use \Civi\Test\Api3DocTrait;
  use \Civi\Test\Api3TestTrait; // conflicts with Api3DocTrait
  use \Civi\Test\ContactTestTrait;
  use \Civi\Test\GenericAssertionsTrait;
  use \Civi\Test\DbTestTrait;

  protected $_individualId;
  protected $_contribution;
  protected $_invoiceNum;
  protected $_financialTypeId = 1;

  public static function setUpBeforeClass() {
    // See: https://docs.civicrm.org/dev/en/latest/testing/phpunit/#civitest

    // Example: Install this extension. Don't care about anything else.
    // \Civi\Test::e2e()->installMe(__DIR__)->apply();

    // Example: Uninstall all extensions except this one.
    // \Civi\Test::e2e()->uninstall('*')->installMe(__DIR__)->apply();

    // Example: Install only core civicrm extensions.
    // \Civi\Test::e2e()->uninstall('*')->install('org.civicrm.*')->apply();
  }

  public function setUp() {
    parent::setUp();
    $this->_apiversion = 3;
    $this->enableTaxAndInvoicing();
    $this->_individualId = $this->individualCreate();
    $this->_invoiceNum   = $this->genRandomInvoiceNum();
    $this->_contribution = $this->createContribution();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testCreateCreditNotes() {
    $creditnotePrefix = CRM_Contribute_BAO_Contribution::checkContributeSettings('credit_notes_prefix', TRUE);

    $creditNote1 = $this->createCreditNote(['total_amount' => -10]);
    $this->assertEquals($creditNote1['invoice_number'], $creditnotePrefix . $this->_invoiceNum);

    $creditNote2 = $this->createCreditNote(['total_amount' => -20]);
    $this->assertEquals($creditNote2['invoice_number'], $creditnotePrefix . $this->_invoiceNum . '_2');

    $creditNote3 = $this->createCreditNote(['total_amount' => -30]);
    $this->assertEquals($creditNote3['invoice_number'], $creditnotePrefix . $this->_invoiceNum . '_3');

    // Try edit note 1 with higher amount which should fail
    $creditNote1EditParams = $this->getCreditNoteParams(['contribution_id' => $creditNote1['id'], 'total_amount' => -70]);
    $creditNote1Edit = $this->callAPIFailure('Contribution', 'create', $creditNote1EditParams);

    // Try edit note 1 with lower amount which should pass
    $creditNote1Edit = $this->createCreditNote(['contribution_id' => $creditNote1['id'], 'total_amount' => -20]);

    // Try edit note 2 with same amount again which should pass
    $creditNote2Edit = $this->createCreditNote(['contribution_id' => $creditNote2['id'], 'total_amount' => -20]);

    // Try creating new credit note with higher amount, which should fail
    $creditNote4Params = $this->getCreditNoteParams(['total_amount' => -70]);
    // Fixme: draft a failure message as pushed by api. Taxed Amount needs to be mentioned in the message.
    $creditNote4 = $this->callAPIFailure('Contribution', 'create', $creditNote4Params);

    // Try creating new credit note with +ve amount, which should fail
    $creditNote5Params = $this->getCreditNoteParams(['total_amount' => 10]);
    $creditNote5 = $this->callAPIFailure('Contribution', 'create', $creditNote5Params, 'Credit Note Amount is expected to be -ve');

    // Try creating new credit note with incorrect source contribution_id, which should fail
    $creditNote6Params = $this->getCreditNoteParams(['total_amount' => -10, 'is_creditnote_for' => 1211212121212]);
    $creditNote6 = $this->callAPIFailure('Contribution', 'create', $creditNote6Params, 'Unable to find any information about original payment for the credit note. Make sure is_creditnote_for specifies the correct original contribution ID.');

    // cleanup
    $this->contributionDelete($creditNote1['id']);
    $this->contributionDelete($creditNote2['id']);
    $this->contributionDelete($creditNote3['id']);
    $this->contributionDelete($this->_contribution['id']);
    $this->contactDelete($this->_individualId);
  }

  public function createContribution() {
    $invoicePrefix = CRM_Contribute_BAO_Contribution::checkContributeSettings('invoice_prefix', TRUE);
    $createParams  = [
      'sequential'   => 1,
      'contact_id'   => $this->_individualId,
      'receive_date' => date('Y-m-d'),
      'total_amount' => 100.00,
      'financial_type_id' => $this->_financialTypeId,
      'non_deductible_amount' => 0.00,
      'fee_amount'   => 15.00,
      'net_amount'   => 90.00,
      'invoice_number' => $invoicePrefix . $this->_invoiceNum,
      'trxn_id'      => 'CNTest_' . uniqid(),
      'invoice_id'   => 'CNTest_' . uniqid(),
      'source'       => 'CNTest',
      'contribution_status_id' => 1,
    ];
    $contribution = $this->callAPISuccess('contribution', 'create', $createParams);
    $contributions = $this->callAPISuccess('contribution', 'get', [
      'contribution_id' => $contribution['id'],
    ]);

    $this->assertEquals(1, $contributions['count']);
    $contribution = $contributions['values'][$contributions['id']];
    $this->assertEquals($contribution['contact_id'], $this->_individualId);
    $this->assertEquals($contribution['financial_type_id'], 1);
    $this->assertEquals($contribution['total_amount'], 105.00);
    $this->assertEquals($contribution['non_deductible_amount'], 0.00);
    $this->assertEquals($contribution['fee_amount'], 15.00);
    $this->assertEquals($contribution['net_amount'], 90.00);
    $this->assertEquals($contribution['invoice_number'], $invoicePrefix . $this->_invoiceNum);
    $this->assertEquals($contribution['contribution_source'], 'CNTest');
    $this->assertEquals($contribution['contribution_status'], 'Completed');
    return $contribution;
  }

  public function getCreditNoteParams($params) {
    $createParams = [
      'sequential'        => 1,
      'financial_type_id' => $this->_financialTypeId,
      'receive_date'      => date('Y-m-d'),
      'contact_id'        => $this->_individualId,
      'is_creditnote_for' => $this->_contribution['id'],
      'source'            => 'CNTest',
      'trxn_id'           => 'CNTest_' . uniqid(),
      'invoice_id'        => 'CNTest_' . uniqid(),
      'contribution_status_id' => 1,
    ];
    return array_merge($createParams, $params);
  }

  public function createCreditNote($params) {
    $createParams = $this->getCreditNoteParams($params);
    $creditNote = civicrm_api3('Contribution', 'create', $createParams);
    $result = $this->callAPISuccess('contribution', 'get', [
      'contribution_id' => $creditNote['id'],
    ]);

    $creditNote = $result['values'][$creditNote['id']];
    $this->assertEquals($creditNote['contact_id'], $this->_individualId);
    $this->assertEquals($creditNote['financial_type_id'], 1);
    $this->assertEquals($creditNote['contribution_source'], 'CNTest');
    $this->assertEquals($creditNote['contribution_status'], 'Completed');

    $this->assertDBQuery(1, 'SELECT count(*) FROM civicrm_credit_note WHERE contribution_id = %1 AND credit_note_id = %2', [
      1 => [$this->_contribution['id'], 'Integer'],
      2 => [$creditNote['id'], 'Integer'],
    ]);
    return $creditNote;
  }

  /**
   * Enable Tax and Invoicing
   */
  protected function enableTaxAndInvoicing($params = array()) {
    // Enable component contribute setting
    $contributeSetting = array_merge($params,
      array(
        'invoicing' => 1,
        'invoice_prefix' => 'INV_',
        'credit_notes_prefix' => 'CN_',
        'due_date' => 10,
        'due_date_period' => 'days',
        'notes' => '',
        'is_email_pdf' => 1,
        'tax_term' => 'Sales Tax',
        'tax_display_settings' => 'Inclusive',
      )
    );
    return Civi::settings()->set('contribution_invoice_settings', $contributeSetting);
  }

  public function genRandomInvoiceNum($length = 5) {
    $result = '';
    for($i = 0; $i < $length; $i++) {
      $result .= mt_rand(0, 9);
    }
    return $result;
  }

  /**
   * Delete contribution.
   *
   * @param int $contributionId
   *
   * @return array|int
   */
  public function contributionDelete($contributionId) {
    $params = array(
      'contribution_id' => $contributionId,
    );
    $result = $this->callAPISuccess('contribution', 'delete', $params);
    return $result;
  }
}
