# AI Content Summary

Generate content summaries in Drupal using providers from the AI module.

## Features
- Button on node forms to request an AI summary.
- Works with any AI provider configured in the AI module.
- Configurable prompt and summary length.
- Supports body summaries or custom summary fields.

## Requirements
- Drupal 9, 10, or 11.
- [AI module](https://www.drupal.org/project/ai) with a chat-capable provider.

## Installation
1. Add this repository to the `repositories` section of your project's `composer.json`:

   ```json
   {
     "type": "vcs",
     "url": "https://github.com/tanmay-pathak/AI-Content-Summary-Drupal.git"
   }
   ```

2. Require the module with Composer:

   ```bash
   lando composer require 'zu/ai-content-summary'
   ```

3. Enable the module:

   ```bash
   lando drush en ai-content-summary
   ```

4. Configure at `/admin/config/content/ai-content-summary`.

## Usage
Once configured, the module adds a **Generate AI Summary** button to the edit forms of
allowed content types. Clicking the button sends the saved content to your default AI
provider and writes the returned summary into the summary field. The request uses the
configured prompt and length settings. Because the service reads the saved node data,
you should save the node before generating a new summary if you have unsaved changes.

## Permissions
- **Administer AI Content Summary**
- **Generate AI summaries**

## Configuration
Configuration lives at `/admin/config/content/ai-content-summary`.

Here you can:

- Enter the prompt that will be sent to the AI provider.
- Choose minimum and maximum summary lengths.
- Specify a view mode used to extract the text that will be summarized. Creating a
  dedicated view mode (e.g. `ai_summary_source`) lets you control exactly which fields
  are passed to the AI service.
- Select which content types should expose the summary generation button.

The module relies on the AI core module for provider configuration, so ensure that a
chat‑capable provider is enabled and set as the default for chat operations.

## License
Licensed under the MIT License. See the `LICENSE` file for details.

