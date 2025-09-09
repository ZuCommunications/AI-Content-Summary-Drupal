# AI Content Summary

Generate content summaries in Drupal using providers from the AI module.

## Features
- Button on node forms to request an AI summary.
- Works with any AI provider configured in the AI module.
- Optional automatic generation when the summary field is empty.
- Configurable prompt and summary length.
- Supports body summaries or custom summary fields.

## Requirements
- Drupal 9, 10, or 11.
- [AI module](https://www.drupal.org/project/ai) with a chat-capable provider.

## Installation
1. Copy the module to `web/modules/custom/`.
2. Enable with `drush en ai_content_summary`.
3. Configure at `/admin/config/content/ai-content-summary`.

## Usage
- On enabled content types, click **Generate AI Summary** to fill the summary field.
- Enable automatic generation to create a summary on save when the field is empty.

## Permissions
- **Administer AI Content Summary**
- **Generate AI summaries**

## Configuration
Choose the prompt, min/max length, auto-generation, and allowed content types.

## License
Licensed under the MIT License. See the `LICENSE` file for details.

