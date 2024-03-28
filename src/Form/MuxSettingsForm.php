<?php

namespace Drupal\mux\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Mux settings for this site.
 */
final class MuxSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'mux_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['mux.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('mux.settings');

    $form['message'] = [
      '#markup' => '<p>' . $this->t('An Access Token which includes the ID and the secret key is required to interface with Mux API. Get your API Access Token by creating one in your <a href=":link" target="_blank">Mux Dashboard</a>.', [':link' => 'https://dashboard.mux.com/settings/access-tokens']) . '</p>'
    ];

    $form['username'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Token ID'),
      '#default_value' => $config->get('username'),
    ];

    $form['password'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Token secret'),
      '#default_value' => $config->get('password'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('mux.settings')
      ->set('username', $form_state->getValue('username'))
      ->set('password', $form_state->getValue('password'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
