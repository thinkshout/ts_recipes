<?php

namespace Drupal\dependency_injection_examples\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExampleSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = 'onRequest';

    return $events;
  }

  /**
   * Request handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   */
  public function onRequest(GetResponseEvent $event) {
    $currentUser = \Drupal::currentUser();

    if ($currentUser->isAuthenticated()) {
      // Do something
    }
  }

}
