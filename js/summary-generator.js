(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.aiContentSummary = {
    attach: function (context, settings) {
      $('.ai-summary-generate-button', context)
        .once('ai-summary-button')
        .each(function () {
          var $button = $(this);
          var fieldName = $button.data('field-name');

          $button.on('click', function (e) {
            e.preventDefault();

            var content = '';
            var summaryField = '';

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
            var config = settings.aiContentSummary || {};
            var maxLength = config.maxLength || 150;
            var minLength = config.minLength || 50;

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

    showMessage: function (message, type) {
      var $message = $(
        '<div class="messages messages--' + type + '">' + message + '</div>'
      );
      $('.region-content').prepend($message);

      setTimeout(function () {
        $message.fadeOut(function () {
          $message.remove();
        });
      }, 3000);
    }
  };
})(jQuery, Drupal, drupalSettings);
