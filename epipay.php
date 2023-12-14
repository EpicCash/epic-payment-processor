<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!---<html xmlns="http://www.w3.org/1999/xhtml">--->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Epicenter QRcode Generator">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
<title>Epicenter QRcode Generator</title>
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
  list($radd, $inv, $amt) = explode('*', $qstr);
} else {
  $radd = $inv = $amt = '';
}
?>

<body position: absolute; bgcolor=black>
<center>
<p class="bigtext"><font face="arial" size="32" color="#f2bb66"><strong>Epic Payment Processor</strong></font></p>

<div id="menu">

<form method="post">
<br>
<font color=white face="arial" size="4">Wallet Receive Address<br>
<textarea name="t1" cols="30" rows="3" required="true" spellcheck="false" maxlength="80">
<?php echo $radd;?></textarea>
<br><font size="2">80 char max</font><br><br>
<font color=white face="arial" size="4">Invoice or Memo<br>
<textarea name="t2" cols="20" rows="1" required="true" spellcheck="false" maxlength="20">
<?php echo $inv;?></textarea>
<br><font size="2">20 char max</font><br><br>
<font color=white face="arial" size="4">Amount<br>
<!-- <textarea name="t3" cols="15" rows="1" required="true" spellcheck="false" maxlength="13"> -->
<input
  name="t3"
  inputmode="decimal"
  type="decimal"
  style="font-size:18px; color:blue; background-color:lightgray;"
  maxlength="15"
  size="13"
  value=<?php echo $amt;?>
>
<!-- <?php echo $amt;?></textarea> -->
<br><font size="2">15 digit w/dec max</font><br><br>
<button class="button" name="gen"><font face="arial" size="4" color="green">Generate</font>
</button>
</form>
<br><br><br>
</body>
</html>

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

// echo "Address: " . $radd . " Inv/Memo: " . $inv ." Amt: " . $amt;

if(array_key_exists('gen',$_POST)){

  $a = $_POST['t1'] . "*" . $_POST['t2'] . "*" . $_POST['t3']; 
  // echo $a;

  // (B) CREATE QR CODE
  $qr = QrCode::create($a)
  // (B1) CORRECTION LEVEL
  ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
  // (B2) SIZE & MARGIN
  ->setSize(160)
  ->setMargin(2)
  // (B3) COLORS
  ->setForegroundColor(new Color(0, 0, 0))
  ->setBackgroundColor(new Color(214, 159, 78));

  // (B4) ATTACH LOGO
  $logo = Logo::create(__DIR__ . "/Epic-icon-black-bg.jpg")
  ->setResizeToWidth(30);

  // (B5) ATTACH LABEL
  // $label = Label::create($a)
  // ->setTextColor(new Color(0, 0, 0));

  // (C) OUTPUT QR CODE
  $writer = new PngWriter();
  // $result = $writer->write($qr, $logo, $label);
  $result = $writer->write($qr, $logo);
  //header("Content-Type: " . $result->getMimeType());
  //echo $result->getString();
  echo "<img src='{$result->getDataUri()}'>";
}
?>
