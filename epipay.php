<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!---<html xmlns="http://www.w3.org/1999/xhtml">--->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="EpiPay QRcode Generator">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
<title>EpiPay QRcode Generator</title>
</head>

<style>

.button {
  border-radius: 20px ;
  background-color: #f2bb66 ;
  padding: 6px 12px;
  font-size: 25px;
  font-weight: bold;
  cursor:pointer;
  transition-duration: 0.4s;
}
.button:hover {
  background-color: #e2a76f;
  color: white;
}

.resultbox {
  width: 650px;
  height: 280px;
  background-color: lightgray;
}

input[type="text"]
{
    font-size: 18px;
    background-color: lightgray;
}

textarea
{
    font-size: 18px;
    background-color: lightgray;
    color: blue;
    resize: none;
}
</style>

<?php
if (isset($_SERVER["QUERY_STRING"])) {
  $qstr = $_SERVER["QUERY_STRING"];
  list($radd, $inv, $amt, $lcurr) = explode('*', $qstr);
  if(empty($_SERVER["QUERY_STRING"])){
    $radd = $inv = $amt = '';
    $lcurr = "USD";
  }
} else {
  $radd = $inv = $amt = '';
  $lcurr = "USD";
}
?>

<body position: absolute; bgcolor=black>
<center>
<p class="bigtext"><font face="arial" size="32" color="#f2bb66"><strong>Epic Payment Processor</strong></font></p>

<div id="menu">

<form method="post">
<br>
<font color=white face="arial" size="4">Local Currency<br>
<select name="s1">
<option selected="selected"><?php echo $lcurr; ?></option>
<option value="EPIC">EPIC</option>
<option value="USD">USD</option><option value="AUD">AUD</option><option value="BRL">BRL</option>
<option value="CAD">CAD</option><option value="CHF">CHF</option><option value="CLP">CLP</option>
<option value="CNY">CNY</option><option value="CZK">CZK</option><option value="DKK">DKK</option>
<option value="EUR">EUR</option><option value="GBP">GBP</option><option value="HKD">HKD</option>
<option value="HUF">HUF</option><option value="IRD">IRD</option><option value="ILS">ILS</option>
<option value="INR">INR</option><option value="JPY">JPY</option><option value="KRW">KRW</option>
<option value="MXN">MXN</option><option value="MYR">MYR</option><option value="NOK">NOK</option>
<option value="NZD">NZD</option><option value="PHP">PHP</option><option value="PKR">PKR</option>
<option value="PLN">PLN</option><option value="RUB">RUB</option><option value="SEK">SEK</option>
<option value="SGD">SGD</option><option value="THB">THB</option><option value="TRY">TRY</option>
<option value="TWD">TWD</option><option value="ZAR">ZAR</option><option value="VND">VND</option>
<option value="MAD">MAD</option><option value="IRR">IRR</option><option value="ARS">ARS</option>
<option value="RON">RON</option><option value="UAH">UAH</option><option value="NGN">NGN</option>
<option value="AED">AED</option><option value="COP">COP</option><option value="EGP">EGP</option>
<option value="SAR">SAR</option><option value="BDT">BDT</option><option value="GHS">GHS</option>
<option value="BGN">BGN</option><option value="VES">VES</option>

</select><br><br>
<font color=white face="arial" size="4">Wallet Receive Address<br>
<textarea name="t1" cols="30" rows="3" required="true" spellcheck="false" maxlength="80">
<?php echo $radd;?></textarea>
<br><font size="2">80 char max</font><br><br>
<font color=white face="arial" size="4">Point of Sale ID<br>
<textarea name="t2" cols="40" rows="1" required="true" spellcheck="false" maxlength="20">
<?php echo $inv;?></textarea>
<br><font size="2">40 char max</font><br><br>
<font color=white face="arial" size="4">Amount<br>

<input
  name="t3"
  required="true"
  inputmode="decimal"
  type="decimal"
  style="font-family: 'Helvetica', Arial, Lucida Grande, sans-serif; font-size: 18px; color: green; background-color: lightgray;"
  maxlength="15"
  size="15"
  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');"
  value=<?php echo $amt;?>
>

<br><font size="2">15 digit w/dec max</font><br><br>

<button class="button" name="gen"><font face="arial" size="4" color="green">Generate</font>
</button>
<br><br>
<a href="https://github.com/EpicCash/epic-payment-processor">User Guide</a>
</form>
<br><br>
<?php

// (A) LOAD QR CODE LIBRARY
require "vendor/autoload.php";
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Label;
session_start();

if(array_key_exists('gen',$_POST)){

  $eprice = "";
  $memo = $_POST['t2'];

  // Remove any * from ID Field
  if (str_contains($memo, '*')) {
     $memo = str_replace('*', '', $memo);
  }

  if($_POST['s1'] != "EPIC"){  
     getprice();
     $a = $_POST['t1'] . "*ID: " . $memo . " " . $_POST['s1'] .": " . $_POST['t3'] . "*" . $eprice;
  } else {
     $a = $_POST['t1'] . "*ID: " . $memo . " " . $_POST['s1'] .": " . $_POST['t3'] . "*" . $_POST['t3'];
  }


  // (B) CREATE QR CODE
  $qr = QrCode::create($a)
  // (B1) CORRECTION LEVEL
  ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
  // (B2) SIZE & MARGIN
  ->setSize(200) //160
  ->setMargin(2)
  // (B3) COLORS
  ->setForegroundColor(new Color(0, 0, 0))
  ->setBackgroundColor(new Color(214, 159, 78));

  // (B4) ATTACH LOGO
  $logo = Logo::create(__DIR__ . "/Epic-icon-black-bg.jpg")
  ->setResizeToWidth(30);

  // (B5) ATTACH LABEL
  if($_POST['s1'] != "EPIC"){  
     $label = Label::create($_POST['t3']." ".$_POST['s1']." = ".$eprice." Epic")
     ->setTextColor(new Color(0, 0, 0));
  } else {
     $label = Label::create("Epic to Send: ".$_POST['t3'])
     ->setTextColor(new Color(0, 0, 0));
  }
    
  // (C) OUTPUT QR CODE
  $writer = new PngWriter();
  $result = $writer->write($qr, $logo, $label);
  // $result = $writer->write($qr, $logo);
  //header("Content-Type: " . $result->getMimeType());
  //echo $result->getString();
  $msg = $label->getText();
  echo "<img src='{$result->getDataUri()}'>";

// END QRCODE GENERATION

//  echo "<script type='text/javascript'>alert('".$msg."');</script>";
//echo "<font color='green'>" . $_POST['t3'] . " " . $_POST['s1'] . " = " . $eprice . " Epic " . date("Y/m/d h:i:sa") . "</font>" ;
date_default_timezone_set("America/New_York");
echo "<br><br><font color='green'>" . $msg . " @ " . date("Y/m/d h:i:sa") . "</font>";

echo <<< EOF
<br><br>
<textarea
id="epic"
style="font-family: 'Helvetica', Arial, Lucida Grande, sans-serif; font-size: 18px; color: blue; background-color: lightgray;"
cols=40
rows=4
maxlength=138
>
EOF;

  echo $a . "</textarea>";

  echo '<br><br><button name="clip"><font face="arial" size="4" color="green" onclick="copyclip()">Copy/Reset</button>';

  echo '<br><br> Conversion provided by CoinMarketCap';

}

function getprice() {
  global $eprice;

  //$url = 'https://pro-api.coinmarketcap.com/v2/tools/price-conversion';
  $url = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest';
  $parameters = [
    //'symbol' => $_POST['s1'],
    //'amount' => $_POST['t3'],
    //'convert' => 'EPIC'
    'symbol' => 'EPIC',
    'convert' => $_POST['s1']
  ];

  $headers = [
  'Accepts: application/json',
  'X-CMC_PRO_API_KEY: {enter your CMC API key here}' //get your own API key from CMC
  ];
  $qs = http_build_query($parameters); // query string encode the parameters
  $request = "{$url}?{$qs}"; // create the request URL

  $curl = curl_init(); // Get cURL resource
  // Set cURL options
  curl_setopt_array($curl, array(
  CURLOPT_URL => $request,            // set the request URL
  CURLOPT_HTTPHEADER => $headers,     // set the headers 
  CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
  ));

  $response = curl_exec($curl); // Send the request, save the response
  // print_r(json_decode($response)); // print json decoded response
  curl_close($curl); // Close request

  $ppart = substr(strchr($response,"price"),7);
//  $endpos = strpos($ppart,"last");
//  $eprice = substr($ppart,0,$endpos-2); // returns everything between 'price' and 'last' in case no decimal
  $decpos = strpos($ppart,"."); // assumes CMC always returns a decimal in Epic value
  $eprice = strval(round(floatval(substr($ppart,0,$decpos+9)),2)); // change 2 to 4,6,8 for epic 10,100,1000
  $eprice = round($_POST['t3']/$eprice, 2);  // change 2 to 4,6,8 for epic 10,100,1000
}
?>
<script text/javascript>
function copyclip() {
  // Get the text field
  var copyText = document.getElementById("epic");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

   // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);
  document.execCommand("copy");

  // Alert the copied text
  //setTimeout(function(){
  //alert("Copied to Clipboard: " + copyText.value);
  //},1000);

  //try {
  //     var retVal = document.execCommand("copy");
  //     //console.log('Copy to clipboard returns: ' + retVal);
  //     alert("Copied to Clipboard: " + copyText.value); 
  //     }
  //     catch (err) { console.log('Error while copying to clipboard: ' + err); }    
  //    };

  window.location.href = window.location.href;
}
</script>

</body>


