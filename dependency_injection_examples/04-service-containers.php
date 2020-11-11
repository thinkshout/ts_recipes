<?php

class ServiceContainer {
  protected $services = [];

  public function get($id) {
    return $this->services[$id];
  }

  public function register($id, $object) {
    $this->services[$id] = $object;
  }
}


$container = new ServiceContainer();
$container->register('mail_transport.phpmail', new PHPMailTransport());
$container->register('mail_transport.sendgrid', new SendGridTransport());
$container->register('mail_transport.test', new TestMailTransport());


if (UNIT_TESTING) {
  $container->register('mail_transport.phpmail', MailTransport::create('test'));
}


$transport = $container->get('mail_transport.phpmail');

$mailer = new Mailer($transport);
$mailer->sendMail();
