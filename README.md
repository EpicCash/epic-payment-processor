# Epic Payment Processor
Methodology for Using Epic Cash to Settle Merchant Invoicing

If you mention in the interview my Payment Processing Solution, we should clarify the difference between POS and PPS. Too many people want Epic to have a 'Point of Sale' system that don't understand what POS is. POS is an inventory control system with the ability to generate a sales invoice, customized for each merchant's specific situation. It does not function as a Payment Processor.

Settling the Invoice is the job of a separate Payment Processing System. This could be as simple as a cash drawer attached to a computer or an integrated cash register that settles the transaction with cash. For 99% of most transactions, they are settled using a bank's Merchant Terminal to process a CC and settle the invoice with Credit (haha, reverse logic there, it's personal debt). Small businesses can tell the banks to go screw and use something like Square (I do) with no monthly fees and fixed rate that is taken out of the transaction before it hits your account. Still uses card swipe or chip reader.

Payment Processing is separate from Point of Sale, but they can have hooks into each other (POS sends PPS the invoice and amt, PPS sends POS confirmation of payment to settle Invoice). My Epic PPS allows the merchant to manually generate the parts required for Epic wallet to settle the transaction with Epic and would also require a manual query of the wallet to verify the amount was received and then mark the Invoice in the POS as 'settled/paid'.

The required parts (Epic address, Invoice #, Amount) can be generated from a merchant's POS system with minor coding and present the info as a QRcode for the Epic Pay Wallet to scan and process (like my epipay.epic.tech but fully automated). More POS code would be needed to query the Epic wallet to confirm the transaction amount was received in order to settle the Invoice and mark as paid.

For Epic Pay, the 'Pay' button feature should be fairly quick and easy since it already has the component parts working:
Scan QRcode
Parse string into 3 data elements by delimeter (*)
EPIC address * Invoice Number * Amount
Calculate fiat value of amount and append to Invoice/Memo
Jump to Send page
Autofill the Send Address, On-Chain Memo, Amount

## Using epipay at epic.tech for Payment QRcodes

You use a browser to access the epipay QRcode generator. It is configured to accept the following URL formats:

https://epipay.epic.tech - allows manual entry of receive address, invoice/memo, and amount. These values will reset after you generate the QRcode.

https://epipay.epic.tech?receive_address - use this form of URL with a query string and it will make your receive address persistent between code generations. This is good for merchants to have running on a spare phone or tablet at checkout so all they have to do between transactions is update the invoice and amount without re-entering the receive address.

https://epipay.epic.tech?receive_address*inv/memo*amount - this will populate all data fields from the URL delimited with '*' and all will be persistent. To reset, just use https://epipay.epic.tech.

Note: The amount field should bring up the Numeric Keypad on mobiles.


