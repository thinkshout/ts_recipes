<?php

class Mailer {
  protected $transport;

  public function __construct() {
    $this->transport = new PHPMailTransport();
  }

  public function sendMail() {
    $this->transport->send();
  }
}


$mailer = new Mailer();
$mailer->sendMail();
