<?php

class Mailer {
  protected $transport;

  public function __construct() {

  }

  public function setTransport($transport) {
    $this->transport = $transport;
  }

  public function sendMail() {
    $this->transport->send();
  }
}


$transport = new PHPMailTransport();
//$transport = new SendGridTransport();
//$transport = new TestMailTransport();

$mailer = new Mailer();
$mailer->setTransport($transport);
$mailer->sendMail();
