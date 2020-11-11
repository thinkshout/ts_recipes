<?php

namespace Drupal\dependency_injection_examples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExampleController extends ControllerBase {

  /**
   * The token service.
   *
   * @var \Drupal\token\Token
   */
  protected $token;

  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->token = $container->get('token');
    return $instance;
  }

  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->token->replace('Hello [user:name]', [
        'user' => $this->currentUser(),
      ]),
    ];
  }

}
