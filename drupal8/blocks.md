# Blocks

* [Block API in Drupal 8](https://www.drupal.org/developing/api/8/block_api)

## Dynamic Blocks

In Drupal 7, it was possible to create blocks dynamically in `hook_block_info()`,
as any PHP code could be excuted in the hook. A for loop was a common way to create blocks for user-generated content.

In Drupal 8, blocks are defined as classes and cannot be as easily generated.

To do this in Drupal 8, you'll need to use a [Plugin Derivative](https://www.drupal.org/node/1653226).

## Plugin Derivative Example

This example is taken from the MailChimp Signup module. In the example, a new
block is dynamically created for every instance of the MailchimpSignup entity
which has a type property indicating it can be displayed as a block.

`src/Plugin/Block/MailchimpSignupSubscribeBlock.php`

  ```php
  namespace Drupal\mailchimp_signup\Plugin\Block;

  use Drupal\Core\Block\BlockBase;

  /**
   * Provides a 'Subscribe' block.
   *
   * @Block(
   *   id = "mailchimp_signup_subscribe_block",
   *   admin_label = @Translation("Subscribe Block"),
   *   category = @Translation("MailChimp Signup"),
   *   module = "mailchimp_signup",
   *   deriver = "Drupal\mailchimp_signup\Plugin\Derivative\MailchimpSignupSubscribeBlock"
   * )
   */
  class MailchimpSignupSubscribeBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
      $block_id = $this->getDerivativeId();

      return array(
        '#markup' => 'Block content here.',
      );
    }

  }
  ```

`src/Plugin/Derivative/MailchimpSignupSubscribeBlock.php`

  ```php
  namespace Drupal\mailchimp_signup\Plugin\Derivative;

  use Drupal\Component\Plugin\Derivative\DeriverBase;

  /**
   * Provides block plugin definitions for MailChimp Signup blocks.
   *
   * @see \Drupal\mailchimp_signup\Plugin\Block\MailchimpSignupSubscribeBlock
   */
  class MailchimpSignupSubscribeBlock extends DeriverBase {

    /**
     * {@inheritdoc}
     */
    public function getDerivativeDefinitions($base_plugin_definition) {
      $signups = mailchimp_signup_load_multiple();

      /* @var $signup \Drupal\mailchimp_signup\Entity\MailchimpSignup */
      foreach ($signups as $signup) {
        if (intval($signup->mode) == MAILCHIMP_SIGNUP_BLOCK || intval($signup->mode) == MAILCHIMP_SIGNUP_BOTH) {

          $this->derivatives[$signup->id] = $base_plugin_definition;
          $this->derivatives[$signup->id]['admin_label'] = t('Mailchimp Subscription Form: @name', array('@name' => $signup->label()));
          $this->derivatives[$signup->id]['cache'] = DRUPAL_NO_CACHE;

        }
      }

      return $this->derivatives;
    }

  }

  ```

Beware of outdated documentation in the wild. [Note this change to the definitions of plugin derivatives](https://www.drupal.org/node/2257811).
