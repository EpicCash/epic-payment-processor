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
* Customer wallet will temporarily switch to Merchant's epicbox for fast pass-through transaction

## Using epipay at epiccash.com for Payment QRcodes

You use a browser to access the epipay QRcode generator. It is configured to accept the following URL formats:

https://epipay.epiccash.com - allows manual entry of receive address, invoice/memo,  amount, and local currency. These values will reset after you generate the QRcode.

https://epipay.epiccash.com?receive_address - use this form of URL with a query string and it will make your receive address persistent between code generations. This is good for merchants to have running on a spare phone or tablet at checkout so all they have to do between transactions is update the invoice and amount without re-entering the receive address.

https://epipay.epiccash.com?receive_address*inv/memo*amount*local_currency - this will populate all data fields from the URL delimited with '*' and all will be persistent. To reset, just use https://epipay.epiccash.com.

https://epipay.epiccash.com?receive_address***local_currency for persistent address and currency or ?***local_currency for just that.

Notes:
* The amount field should bring up the Numeric Keypad on mobiles. Default local currency is USD.
* Select EPIC to skip conversion of local currency to Epic.
* The QRcode second output field now contains ID plus currency and currency amount.

Example Output (rounded to 4 dec places from 8):

`esYMG6XY8YBn5jmFGW6JeN8xyyat2MWUK6r4sGmrgiAJ7voL2itW@epicbox.epicnet.us*ID: 54611 USD: 6.55*2.9573`

The payment info string is also present in a field under the QRcode with a 'Copy/Reset' button. This allows a merchant to copy/paste into an email message to the customer so they can copy/paste into the GUI Payment Processing field (planned for future release).

## Using GUI for Payment Processing via Merchant Payment Request

Merchant would provide not only a QRcode for EP but also the same data string as a 'Payment Request' on their Checkout screen that can either be copied from the Merchant's website (custom POS with online checkout) by the customer or emailed to the customer and pasted into a Pay screen on GUI that follows the same processing steps as EP with an additional first step to paste the Payment Request string into a 'Merchant Payment Data' field with a 'Process' button next to it. GUI could then stay on Pay screen to review processed data and verify amount then Send via a Pay Button or could jump to the standard Send screen with data autofilled (to be determined).

## Developer Notes

php modules required: mb-string gd curl

Modify epipay.php and use your own CMC API key:

```'X-CMC_PRO_API_KEY: {enter your CMC API key here without brackets}' //get your own API key from CMC```

Retrieve Transactions:

./epic-wallet -t ~/.epic/main -p {pwd} txs

Result contains slateID, Type=Received, Confirmed=True, and amount to match with Invoice Amount Due (mismatch is partial payment and merchant contacts customer for balance)

Use slateID to make API call:

curl -vvv  -X POST -H 'Content-Type: application/json' -d  '{"id":1,"jsonrpc":"2.0","method":"retrieve_txs","params":{"tx_slate_id":"<slate_id>", "refresh_from_node": true, "tx_id": null}}' http://epic:<owner_api_secret>@127.0.0.1:3420/v2/owner;

This will obviously require a running wallet to check wallet txs and keep track of last tx ID as 'last processed' and only process newer then set 'last processed' again. Then run API to pull POS ID info from 'Message 0' to match and settle pending Invoice for each new transaction by looking up slateID.

## Merchant Invoicing

As an alternative, we are working on using the Epic Wallet Invoice method where the Merchant scans the Customer's Receive Address at checkout and generates an Invoice transaction that the Customer wallet receives and Accepts then sends a confirmation back to the Merchant's Wallet to finalize. Like a reverse File Transaction method between wallets or a reverse Payment (as explained above) where the Customer scans the Merchant Info, initiates the transaction flow and finalizes. Allows the Merchanat wallet to initiate the transaction flow and finalize rather than the Customer's wallet.
