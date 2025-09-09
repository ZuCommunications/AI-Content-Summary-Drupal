<?php

/**
 * @file
 * Contains the controller for AJAX summary generation.
 */

namespace Drupal\ai_content_summary\Controller;

use Drupal\ai_content_summary\Service\AiSummaryService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for AI Content Summary operations.
 */
class AiContentSummaryController extends ControllerBase {
  /**
   * The AI summary service.
   *
   * @var \Drupal\ai_content_summary\Service\AiSummaryService
   */
  protected $aiSummaryService;

  /**
   * Constructs a new AiContentSummaryController.
   *
   * @param \Drupal\ai_content_summary\Service\AiSummaryService $ai_summary_service
   *   The AI summary service.
   */
  public function __construct(AiSummaryService $ai_summary_service) {
    $this->aiSummaryService = $ai_summary_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('ai_content_summary.service')
      );
  }

  /**
   * Generate summary via AJAX.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with summary.
   */
  public function generateSummary(Request $request) {
    $text = $request->request->get('text');
    $max_length = $request->request->get('max_length', 150);
    $min_length = $request->request->get('min_length', 50);

    if (empty($text)) {
      return new JsonResponse(['error' => 'No text provided'], 400);
    }

    try {
      $clean_text = $this->aiSummaryService->cleanText($text);
      $summary = $this->aiSummaryService->generateSummary($clean_text, $max_length, $min_length);

      return new JsonResponse([
        'summary' => $summary,
        'success' => TRUE,
      ]);
    }
    catch (\Exception $e) {
      return new JsonResponse([
        'error' => $e->getMessage(),
        'success' => FALSE,
      ], 500);
    }
  }

}
