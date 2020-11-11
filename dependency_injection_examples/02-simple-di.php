<?php


class PHPMailTransport implements MailTransportInterface {

}

class SendGridTransport implements MailTransportInterface {

}

class TestMailTransport implements MailTransportInterface {

}


class Mailer {
  protected $transport;

  public function __construct(MailTransportInterface $transport) {
    $this->transport = $transport;
  }

  public function sendMail() {
    $this->transport->send();
  }
}


$transport = new PHPMailTransport();

$mailer = new Mailer($transport);
$mailer->sendMail();
