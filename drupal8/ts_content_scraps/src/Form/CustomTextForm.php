<?php

namespace Drupal\ts_content_scraps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Custom Texts.
 */
class CustomTextForm extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'ts_content_scraps';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['ts_content_scraps.items'];
  }

  /**
   * Settings for "Custom Text" form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Add form header describing purpose and use of form.
    $form['header'] = [
      '#type' => 'markup',
      '#markup' => '<h3>Set custom text used on the site.</h3><p>Configure text that appears on the site outside of specific pieces of content or blocks. Use with care, the effect is immediate.</p>',
    ];
    $form['text_example'] = [
      '#type' => 'textarea',
      '#title' => t('Example text area called "text_example"'),
      '#description' => t('To use this value on the site, call "\Drupal::config(\'ts_content_scraps.items\')->get(\'text_example\')"'),
      '#default_value' => $this->config('ts_content_scraps.items')->get('text_example'),
      '#required' => TRUE,
    ];
    $form['section_example'] = [
      '#type' => 'details',
      '#title' => t('Error Messages'),
      '#description' => t("Example of how to put some fields into a section."),
      '#open' => TRUE,
    ];
    $form['section_example']['line_example'] = [
      '#type' => 'textfield',
      '#title' => t('Example error.'),
      '#default_value' => $this->config('ts_content_scraps.items')->get('line_example'),
      '#required' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $text_settings = \Drupal::service('config.factory')->getEditable('ts_content_scraps.items');
    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $field_key => $field_value) {
      $text_settings->set($field_key, $field_value);
    }
    $text_settings->save();
    parent::submitForm($form, $form_state);
  }

}
