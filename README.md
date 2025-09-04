# AI Content Summary Module

A Drupal module that automatically generates summaries for content nodes using the Drupal AI core module integration. This module provides seamless AI-powered summary generation with support for multiple AI providers through a unified interface.

## Features

- **Drupal AI Integration**: Uses the modern Drupal AI core module for provider management
- **Universal AI Provider Support**: Works with any AI provider supported by the Drupal AI module (OpenAI, Google Gemini, Anthropic, etc.)
- **Smart Form Integration**: Adds "Generate AI Summary" buttons directly to node forms
- **Automatic Generation**: Optional auto-regeneration of summaries on content updates
- **Flexible Configuration**: Configurable summary length and content type settings
- **Multi-language Support**: Generates summaries in the same language as the source content
- **Permission-based Access**: Granular permissions for summary generation

## Requirements

- Drupal 9, 10, or 11
- [AI module](https://www.drupal.org/project/ai) - Provides the core AI functionality
- At least one configured AI provider in the AI module

## Installation

1. Ensure the AI module is installed and configured with at least one provider
2. Copy the `ai_content_summary` directory to your `web/modules/custom/` directory
3. Enable the module: `drush en ai_content_summary`
4. Configure the module at: `/admin/config/content/ai-content-summary`

## Configuration

### Prerequisites

**First, configure an AI provider:**

1. Navigate to **Configuration > AI** (`/admin/config/ai`)
2. Configure your preferred AI provider (OpenAI, Google Gemini, Anthropic, etc.)
3. Set it as the default provider for "Chat" operations

### Module Settings

Navigate to **Configuration > Content > AI Content Summary** (`/admin/config/content/ai-content-summary`) to:

- Configure summary length (minimum/maximum characters)
- Enable/disable auto-regeneration on content updates
- Select which content types can use AI summaries

## Usage

### Manual Generation

1. Create or edit a node of an enabled content type
2. Look for the "Generate AI Summary" button in the body or summary field area
3. Enter your content in the body field
4. Click "Generate AI Summary" to create a summary based on your content
5. The summary will be automatically inserted into the appropriate field

### Automatic Generation

When enabled in settings, summaries will be automatically generated:

- When creating new content (if summary field is empty)
- When updating existing content (if "Regenerate on update" is enabled)
- When content changes significantly (>20% length change)

### Supported Fields

The module works with:

- Standard body fields (with summary)
- Custom `field_summary` fields
- Any text field configured for the content type

## Permissions

- **Administer AI Content Summary**: Configure module settings
- **Generate AI summaries**: Use the summary generation feature on content forms

## Technical Details

### Architecture

- **Service-based**: Uses `AiSummaryService` for core functionality
- **AJAX Integration**: Real-time summary generation without page reload
- **Hook Integration**: Automatic generation via `hook_entity_presave()`
- **Dependency Injection**: Proper Drupal service container usage

### AI Integration

- Uses Drupal AI module's standardized chat interface
- Automatically detects configured AI providers
- Supports any chat-capable AI provider (GPT, Claude, Gemini, etc.)
- Configurable prompt engineering for optimal results

## Troubleshooting

### Common Issues

**"No AI provider configured for chat operations"**

- Configure an AI provider in the AI module first
- Ensure the provider is set as default for "Chat" operations

**"Failed to generate summary"**

- Check AI module configuration and provider credentials
- Verify the AI provider service is accessible
- Check Drupal logs at `/admin/reports/dblog` for detailed errors

**"No content found to summarize"**

- Ensure content is entered in the body field
- Verify the content type has supported fields (body, field_summary)

**Permission Issues**

- Ensure users have "Generate AI summaries" permission
- Check content type is enabled in module settings

### Performance Considerations

- Content is automatically truncated to 4000 characters to avoid API limits
- Significant content changes trigger auto-regeneration (configurable)
- AJAX requests prevent page reloads during generation

## Logging and Debugging

The module provides comprehensive logging:

- Check `/admin/reports/dblog` for detailed error messages
- All API failures and exceptions are logged with context
- Auto-generation attempts are logged for tracking

## Development Notes

This module was originally based on a community module but has been heavily customized with:

### Key Customizations

- **Drupal AI Integration**: Replaced direct API calls with Drupal AI module
- **Enhanced Field Support**: Added support for custom summary fields
- **Improved Error Handling**: Better exception handling and user feedback
- **AJAX Improvements**: More responsive user interface
- **Smart Auto-generation**: Content change detection for efficient updates
- **Multi-language Support**: Maintains source content language in summaries

### File Structure

```
ai_content_summary/
├── src/
│   ├── Service/AiSummaryService.php    # Core AI integration service
│   ├── Controller/                     # AJAX endpoint controller
│   └── Form/                          # Configuration form
├── js/summary-generator.js            # Frontend JavaScript
├── config/schema/                     # Configuration schema
└── ai_content_summary.module          # Hook implementations
```

### Dependencies

- `drupal:core` (^9 || ^10 || ^11)
- `ai:ai` - Drupal AI module for provider management

## Contributing

When making modifications:

- Follow Drupal coding standards
- Update configuration schema as needed
- Test with multiple AI providers
- Update this documentation for any new features
