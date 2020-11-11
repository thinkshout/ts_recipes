<?php

namespace Drupal\dependency_injection_examples\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Example Block with DI.
 *
 * @Block(
 *   id = "dependency_injection_examples_block_with_di",
 *   admin_label = @Translation("Example block with DI"),
 *   category = @Translation("Examples"),
 * )
 */
class ExampleBlockWithDI extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $elements = [];
    $plain_content = $this->renderer->renderPlain($elements);
    //...
  }

}
