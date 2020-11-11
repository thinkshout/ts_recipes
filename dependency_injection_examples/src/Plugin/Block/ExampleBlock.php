<?php

namespace Drupal\dependency_injection_examples\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Example Block.
 *
 * @Block(
 *   id = "dependency_injection_examples_block",
 *   admin_label = @Translation("Example block"),
 *   category = @Translation("Examples"),
 * )
 */
class ExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $elements = [];
    $renderer = \Drupal::service('renderer');
    $plain_content = $renderer->renderPlain($elements);
    //...
  }

}
