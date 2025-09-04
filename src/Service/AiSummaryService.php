<?php

namespace Drupal\ai_content_summary\Service;

use Drupal\ai\AiProviderPluginManager;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service for generating AI-powered summaries.
 */
class AiSummaryService {
  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The AI provider manager.
   *
   * @var \Drupal\ai\AiProviderPluginManager
   */
  protected $aiProvider;

  /**
   * Constructs a new AiSummaryService.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\ai\AiProviderPluginManager $ai_provider
   *   The AI provider manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AiProviderPluginManager $ai_provider) {
    $this->configFactory = $config_factory;
    $this->aiProvider = $ai_provider;
  }

  /**
   * Generate a summary for the given text.
   *
   * @param string $text
   *   The text to summarize.
   * @param int $max_length
   *   Maximum length of summary.
   * @param int $min_length
   *   Minimum length of summary.
   *
   * @return string
   *   The generated summary
   *
   * @throws \Exception
   *   If the API request fails.
   */
  public function generateSummary(string $text, int $max_length = 150, int $min_length = 50): string {
    try {
      // Find the default selected LLM.
      $settings = $this->aiProvider->getDefaultProviderForOperationType('chat');

      if (empty($settings)) {
        throw new \Exception('No AI provider configured for chat operations. Please configure an AI provider first.');
      }

      $provider = $this->aiProvider->createInstance($settings['provider_id']);

      // Get the prompt from configuration.
      $config = $this->configFactory->get('ai_content_summary.settings');
      $user_prompt = $config->get('prompt') ?? 'Create a detailed summary of the following text using the same language as the following text.';

      // Build the complete prompt with length restrictions and content.
      $prompt = $user_prompt . " The summary should be between {$min_length} and {$max_length} characters long:\n\n{$text}";

      $messages = new ChatInput([
        new ChatMessage('system', 'You are a helpful assistant that creates concise summaries of text content.'),
        new ChatMessage('user', $prompt),
      ]);

      $response = $provider->chat($messages, $settings['model_id'])->getNormalized();
      $summary = $response->getText();

      // Ensure summary is within length limits.
      if (strlen($summary) > $max_length) {
        $summary = substr($summary, 0, $max_length - 3) . '...';
      }

      return trim($summary);
    }
    catch (\Exception $e) {
      throw new \Exception('Failed to generate summary: ' . $e->getMessage());
    }
  }

  /**
   * Clean text before sending to AI.
   *
   * @param string $text
   *   Raw text to clean.
   *
   * @return string
   *   Cleaned text
   */
  public function cleanText(string $text): string {
    // Remove HTML tags.
    $text = strip_tags($text);

    // Remove extra whitespace.
    $text = preg_replace('/\s+/', ' ', $text);

    // Trim.
    $text = trim($text);

    // Limit to reasonable length to avoid API limits.
    if (strlen($text) > 4000) {
      $text = substr($text, 0, 4000) . '...';
    }

    return $text;
  }

}
