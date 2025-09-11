<?php

/**
 * @file
 * Settings form for the AI Content Summary module.
 */

namespace Drupal\ai_content_summary\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure AI Content Summary settings.
 */
class AiContentSummarySettingsForm extends ConfigFormBase {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new AiContentSummarySettingsForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('entity_type.manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_content_summary_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ai_content_summary.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ai_content_summary.settings');

    $form['ai_info'] = [
      '#markup' => '<div class="messages messages--warning">' . $this->t('This module uses the AI core module for provider configuration. Please configure your AI providers at <a href="@url">AI Configuration</a>.', ['@url' => '/admin/config/ai']) . '</div>',
    ];

    $form['summary'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Summary Settings'),
    ];

    $default_prompt = 'Create a detailed summary of the following text using the same language as the following text.';

    $form['summary']['prompt'] = [
      '#type' => 'textarea',
      '#title' => $this->t('AI Summary Prompt'),
      '#default_value' => $config->get('prompt') ?? $default_prompt,
      '#description' => $this->t('The main instruction to send to the AI for generating summaries.'),
      '#rows' => 4,
      '#required' => TRUE,
    ];

    $form['summary']['max_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Summary Length'),
      '#default_value' => $config->get('max_length') ?? 150,
      '#description' => $this->t('Maximum number of characters for generated summaries.'),
      '#min' => 50,
      '#max' => 10000,
    ];

    $form['summary']['min_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Summary Length'),
      '#default_value' => $config->get('min_length') ?? 50,
      '#description' => $this->t('Minimum number of characters for generated summaries.'),
      '#min' => 10,
      '#max' => 2000,
    ];

    $form['summary']['view_mode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View Mode for Content Extraction'),
      '#default_value' => $config->get('view_mode') ?? 'ai_summary_source',
      '#description' => $this->t('The view mode to use when extracting content for summarization. Create a custom view mode (e.g., "ai_summary_source") and configure it to include only the fields you want summarized. You can also use existing view modes like "teaser" or "full".'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['summary']['auto_generate_summary'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Automatically generate summary when empty'),
      '#default_value' => $config->get('auto_generate_summary') ?? FALSE,
      '#description' => $this->t('Automatically generate summaries for new content or when the summary field is empty.'),
    ];

    $form['content_types'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content Types'),
    ];

    // Get all content types.
    $content_types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();

    $options = [];
    foreach ($content_types as $machine_name => $content_type) {
      $options[$machine_name] = $content_type->label();
    }

    $form['content_types']['enabled_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Content Types'),
      '#options' => $options,
      '#default_value' => $config->get('enabled_types') ?? [],
      '#description' => $this->t('Select which content types should have AI summary generation available.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ai_content_summary.settings')
      ->set('prompt', $form_state->getValue('prompt'))
      ->set('max_length', $form_state->getValue('max_length'))
      ->set('min_length', $form_state->getValue('min_length'))
      ->set('view_mode', $form_state->getValue('view_mode'))
      ->set('auto_generate_summary', $form_state->getValue('auto_generate_summary'))
      ->set('enabled_types', array_filter($form_state->getValue('enabled_types')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
