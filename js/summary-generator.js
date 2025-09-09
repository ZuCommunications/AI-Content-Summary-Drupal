/**
 * Handles frontend summary generation for the AI Content Summary module.
 */
(function ($, Drupal, drupalSettings) {
  'use strict';

  /**
   * Attaches behavior for generating summaries through AJAX.
   */

  Drupal.behaviors.aiContentSummary = {
    attach: function (context, settings) {
      $('.ai-summary-generate-button', context)
        .once('ai-summary-button')
        .each(function () {
          const $button = $(this);
          const fieldName = $button.data('field-name');

          $button.on('click', function (e) {
            e.preventDefault();

            let content = '';
            let summaryField = '';

            // Find the appropriate content field
            if (fieldName === 'body') {
              content = $('textarea[name="body[0][value]"]').val();
              summaryField = 'textarea[name="body[0][summary]"]';
            } else if (fieldName === 'field_summary') {
              // Look for body content first
              content =
                $('textarea[name="body[0][value]"]').val() ||
                $('textarea[name="field_body[0][value]"]').val() ||
                $('textarea[name="field_content[0][value]"]').val();
              summaryField = 'textarea[name="field_summary[0][value]"]';
            }

            if (!content.trim()) {
              alert(
                Drupal.t('Please add some content before generating a summary.')
              );
              return;
            }

            // Show loading state
            $button.prop('disabled', true).val(Drupal.t('Generating...'));

            // Get configuration
            const config = settings.aiContentSummary || {};
            const maxLength = config.maxLength || 150;
            const minLength = config.minLength || 50;

            // Make AJAX request
            $.ajax({
              url: '/ai-content-summary/generate',
              method: 'POST',
              data: {
                text: content,
                max_length: maxLength,
                min_length: minLength
              },
              dataType: 'json',
              success: function (response) {
                if (response.success) {
                  $(summaryField).val(response.summary);
                  Drupal.behaviors.aiContentSummary.showMessage(
                    Drupal.t('Summary generated successfully!'),
                    'status'
                  );
                } else {
                  alert(
                    Drupal.t('Error: @error', { '@error': response.error })
                  );
                }
              },
              error: function (xhr, status, error) {
                alert(
                  Drupal.t('Failed to generate summary. Please try again.')
                );
              },
              complete: function () {
                $button
                  .prop('disabled', false)
                  .val(Drupal.t('Generate AI Summary'));
              }
            });
          });
        });
    },

    /**
     * Display a temporary message on the page.
     *
     * @param {string} message
     *   The message to show.
     * @param {string} type
     *   The Drupal message type.
     */
    showMessage: function (message, type) {
      const $message = $(
        '<div class="messages messages--' + type + '">' + message + '</div>'
      );
      $('.region-content').prepend($message);

      setTimeout(() => {
        $message.fadeOut(() => {
          $message.remove();
        });
      }, 3000);
    }
  };
})(jQuery, Drupal, drupalSettings);
