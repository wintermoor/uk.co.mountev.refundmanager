<?php

class CRM_Refundmanager_CreditNote {

  public static function getTotalCreditAmount($contributionId, $creditNoteId = NULL) {
    if (!empty($contributionId)) {
      $query = "SELECT COALESCE(SUM(cc.total_amount),0) as total 
        FROM  civicrm_contribution cc
        JOIN  civicrm_credit_note cn on cc.id = cn.credit_note_id
        WHERE cn.contribution_id = %1";
      $params = [1 => [$contributionId, 'Integer']];
      if (!empty($creditNoteId)) {
        $query .= ' AND cn.credit_note_id != %2';
        $params[2] = [$creditNoteId, 'Integer'];
      }
      return CRM_Core_DAO::singleValueQuery($query, $params);
    }
    return 0;
  }

  public static function getNextCreditInvoiceNum($contributionId, $invoiceNum, $creditNoteId = NULL) {
    $invoicePrefix = CRM_Contribute_BAO_Contribution::checkContributeSettings('invoice_prefix');
    $creditnotePrefix = CRM_Contribute_BAO_Contribution::checkContributeSettings('credit_notes_prefix');
    $suffix = '';
    $num = self::getNumberOfCredits($contributionId, $creditNoteId);
    if ($num > 0) {
      $suffix = '_' . ++$num;
    }
    return $creditnotePrefix . str_replace($invoicePrefix, '', $invoiceNum) . $suffix;
  }

  public static function getNumberOfCredits($contributionId, $creditNoteId = NULL) {
    if (!empty($contributionId)) {
      $query = "SELECT count(*)
        FROM  civicrm_contribution cc
        JOIN  civicrm_credit_note cn on cc.id = cn.credit_note_id
        WHERE cn.contribution_id = %1";
      $params = [1 => [$contributionId, 'Integer']];
      if (!empty($creditNoteId)) {
        $query .= ' AND cn.credit_note_id != %2';
        $params[2] = [$creditNoteId, 'Integer'];
      }
      return CRM_Core_DAO::singleValueQuery($query, $params);
    }
    return 0;
  }

  public static function isaCreditNote($creditNoteId) {
    $query = "SELECT contribution_id FROM civicrm_credit_note WHERE credit_note_id = %1";
    return CRM_Core_DAO::singleValueQuery($query, [1 => [$creditNoteId, 'Integer']]);
  }

  public static function addTax($amount, $financialTypeId) {
    $taxRates = CRM_Core_PseudoConstant::getTaxRates();
    $taxRate  = CRM_Utils_Array::value($financialTypeId, $taxRates, 0);
    $totalTaxAmount = $amount;
    if ($taxRate) {
      $taxAmount = ($taxRate / 100) * $amount;
      $totalTaxAmount = $amount + $taxAmount;
    }
    return $totalTaxAmount;
  }

  public static function addEntry($contributionId, $creditNoteId) {
    if ($contributionId && $creditNoteId) {
      $sql = "INSERT INTO civicrm_credit_note (contribution_id, credit_note_id) 
        VALUES (%1, %2)
        ON DUPLICATE KEY UPDATE credit_note_id = credit_note_id";
      CRM_Core_DAO::executeQuery($sql, [
        1 => [$contributionId, 'Integer'],
        2 => [$creditNoteId, 'Integer'],
      ]);
    }
  }

  /**
   * wrapper around getNextInvoiceNum
   */
  public static function validateAmount($creditNoteAmount, $sourceContributionId, $creditNoteId = NULL) {
    return self::getNextInvoiceNum($creditNoteAmount, $sourceContributionId, $creditNoteId);
  }

  public static function getNextInvoiceNum($creditNoteAmount, $sourceContributionId, $creditNoteId = NULL) {
    $isError = 1;
    $error = ts('Required parameters for a credit note missing or empty');
    $creditNoteInvoiceNum = '';
    if (!empty($creditNoteAmount) && !empty($sourceContributionId)) {
      if (!empty($sourceContributionId)) {
        if ($creditNoteAmount < 0) {
          $result = civicrm_api3('Contribution', 'get', [
            'sequential' => 1,
            'id' => $sourceContributionId,
          ]);
          if (!empty($result['values']) && $result['id'] == $sourceContributionId) {
            $sourceTotalAmount = $result['values'][0]['total_amount'];
            $invoiceNum   = $result['values'][0]['invoice_number'];
            if ($invoiceNum) {
              $creditTotals = self::getTotalCreditAmount($sourceContributionId, $creditNoteId);
              if ($sourceTotalAmount > 0) {
                $creditTotals = (($creditNoteAmount * -1) + ($creditTotals * -1));
                if ($creditTotals <= $sourceTotalAmount) {
                  $creditNoteInvoiceNum = self::getNextCreditInvoiceNum($sourceContributionId, $invoiceNum, $creditNoteId);
                  $error = '';
                  $isError = 0;
                }
                else {
                  $error = "Cedit note amount ({$creditNoteAmount}) makes total credits so far (-{$creditTotals}) which exceeds the original payment amount ({$sourceTotalAmount}). You may have to consider any taxes. If amount entered is correct, there may be other credit notes for same original payment.";
                }
              }
              else {
                $error = "Can't create Credit Note for a payment with -ve amount.";
              }
            }
            else {
              $error = "Original payment doesn't have an invoice number, which credit note can infer from.";
            }
          }
          else {
            $error = "Unable to find any information about original payment for the credit note. Make sure is_creditnote_for specifies the correct original contribution ID.";
          }
        }
        else {
          $error = "Credit Note Amount is expected to be -ve";
        }
      }
    }
    return [
      'is_error' => $isError,
      'error'    => $error,
      'invoice_number' => $creditNoteInvoiceNum,
    ];
  }

}
