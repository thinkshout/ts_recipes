<?php

namespace Drupal\dependency_injection_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

class ExampleController extends ControllerBase {

  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => \Drupal::service('token')->replace('Hello [user:name]', [
        'user' => \Drupal::currentUser(),
      ]),
    ];
  }

}
