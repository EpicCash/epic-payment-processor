# Epic Payment Processor
## Methodology for Using Epic Cash to Settle Merchant Invoicing

A typical Point of Sale System is an inventory control program with the ability to generate a sales invoice, customized for each merchant's specific situation. It does not function as a Payment Processor to 'settle' or pay the generated Invoice.

Settling the Invoice is the job of a separate Payment Processing System. This could be as simple as a cash drawer attached to a computer or an integrated cash register that settles the transaction with cash. For 95% of most transactions, they are settled using a bank's Merchant Terminal to process a CC, DC, or GC and settle the invoice with plastic.

Payment Processing is separate from Point of Sale, but they can have hooks into each other (POS sends PPS the invoice and amt, PPS sends POS confirmation of payment to settle Invoice). The Epic PPS allows the merchant to manually generate the parts required for Epic wallet to settle the transaction with Epic and would also require a manual query of the wallet to verify the amount was received and then mark the Invoice in the POS as 'settled/paid'.

The required parts (Epic Receive Address, Invoice # or Transaction ID plus Local Currency Code:Currency Amount, Calculated Epic Amount) can be generated from a merchant's POS system with minor coding and present the info as a QRcode for the Epic Pay Wallet to scan and process (like https://epipay.epicnet.us but fully automated). More POS code would be needed to query the Epic wallet to confirm the transaction amount was received in order to settle the Invoice and mark as paid.


## Epic Pay Mobile Wallet 'Pay' Button

For Epic Pay, the following steps are required to process a payment:

* Scan QRcode from Merchant 'Terminal' - (tablet or PC second mini monitor) in person or online checkout.
* Parse string into 3 data elements by delimeter (*) : Epic Receive address * Transaction Data * Amount
* Second QRcode element (Transaction Data) contains Invoice/ID and Currency Code:Currency Amount for On-Chain Memo
* Jump to Send page
* Autofill the Send Address, On-Chain Memo, Amount
* User verifies info on Send page then taps Send to make Payment

## Using epipay at epicnet.us for Payment QRcodes

You use a browser to access the epipay QRcode generator. It is configured to accept the following URL formats:

https://epipay.epicnet.us - allows manual entry of receive address, invoice/memo,  amount, and local currency. These values will reset after you generate the QRcode.  Currency defaults to USD.

https://epipay.epicnet.us?receive_address - use this form of URL with a query string and it will make your receive address persistent between code generations. This is good for merchants to have running on a spare phone or tablet at checkout so all they have to do between transactions is update the invoice and amount without re-entering the receive address. Currency defaults to USD.

https://epipay.epicnet.us?receive_address*inv/memo*amount*local_currency - this will populate all data fields from the URL delimited with '*' and all will be persistent. To reset, just use https://epipay.epicnet.us.

https://epipay.epicnet.us?receive_address***local_currency for persistent address and currency or ?***local_currency for just that.

Notes:
* The amount field should bring up the Numeric Keypad on mobiles. Default local currency is USD.
* Select EPIC to skip conversion of local currency to Epic.
* The QRcode second output field now contains ID plus currency and currency amount.

Example Output (rounded to 4 dec places from 8):
`esYMG6XY8YBn5jmFGW6JeN8xyyat2MWUK6r4sGmrgiAJ7voL2itW@epicbox.epic.tech*ID: 54611 USD: 6.55*2.9573`

## Using GUI for Payment Processing via Merchant Payment Request

Merchant would provide not only a QRcode for EP but also the same data string as a 'Payment Request' on their Checkout screen that can be copied from the website and pasted into a Pay screen on GUI that follows the same processing steps as EP with an additional first step to paste the Payment Request string into an input field with a 'Process' button next to it. GUI could then stay on Pay screen to review processed data and verify amount then Send via a Pay Button or could jump to the standard Send screen with data autofilled (to be determined).

php modules required: mb-string gd curl
