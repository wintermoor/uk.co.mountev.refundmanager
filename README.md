# uk.co.mountev.refundmanager

Extension to manage partial or full refunds in CiviCRM through credit notes. Credit notes are nothing but -ve payments with a -ve invoice and a different invoice prefix.

The extension provides following support:
* Provides "create credit note" option for all types of payments.
* Supports multiple credit notes.
* Supports and validates credit note creations or updates through FORMs or APIs, to make sure amount matches with that of original payment.
* Modifies invoice-number column in core contribution table, which also makes invoice receipts display invoice-number of that of the credit note e.g CN_234
* Uses Civi's existing invoice settings for credit note prefix. For multiple credit notes invoice numbers are of the format CN_765, CN_765_2, CN_765_3 etc.
* Provides a smarty variable ($isCreditNote) which could be used in invoice receipt template for adjustments per credit note.

You might issue a credit note to your contact / customer if:
* Your customer wasn’t happy with your service and you’re giving them a full or partial refund.
* You overcharged your customer by mistake.
* You issued an invoice by mistake and are happy that your customer doesn’t have to pay.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.6+
* CiviCRM v5.0+

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl uk.co.mountev.refundmanager@https://github.com/mountev/uk.co.mountev.refundmanager/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/mountev/uk.co.mountev.refundmanager.git
cv en refundmanager
```

## Usage

* Make sure "Tax and Invoicing" is enabled - https://docs.civicrm.org/user/en/latest/contributions/sales-tax-and-vat.

* A new option to 'create credit note' would appear on payments listings. Option would only appear if invoicing is turned on and payment has an amount more than zero. Use it to create credit notes.
![Screenshot](/images/refund-tab-option.png)

* Form validation will make sure that the amount entered is -ve and within credit limit.
![Screenshot](/images/refund-form-validation1.png)
![Screenshot](/images/refund-form-validation2.png)

* Once saved, an invoice number of that of credit note format should appear e.g CN_765.
![Screenshot](/images/refund-on-save1.png)

* To create credit note from API, specify 'is_creditnote_for' parameter.
![Screenshot](/images/refund-api-create.png)

* API validation pass through same set of validations and would thow any errors if applicable.
![Screenshot](/images/refund-api-error.png)

* Tweak "Contributions - Invoice" message template to make any adjustments per credit note. E.g:
![Screenshot](/images/refund-msg-tpl-tweak1.png)
![Screenshot](/images/refund-invoice2.png)

## Future Improvements

* Initiate refunds by payment processor if supported.
