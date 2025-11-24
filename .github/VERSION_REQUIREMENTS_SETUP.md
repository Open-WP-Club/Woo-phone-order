# Version Requirements Auto-Update Setup

This document explains how to set up and use the automated version requirements update workflow.

## Overview

The `update-version-requirements.yml` workflow automatically updates version requirements across your WordPress plugin files by fetching configuration from a centralized repository. This allows you to manage version requirements for multiple plugins from a single source.

## Features

- ✅ **Centralized Configuration**: Manage versions from one main repository
- ✅ **Automatic Detection**: Detects WooCommerce plugins automatically
- ✅ **Quarterly Updates**: Runs every 4 months automatically
- ✅ **Manual Trigger**: Can be triggered manually anytime
- ✅ **Pull Request Creation**: Creates PRs with detailed change logs
- ✅ **Retry Logic**: Network-resilient with exponential backoff
- ✅ **Multi-file Updates**: Updates both main plugin file and readme.txt

## Setup Instructions

### Step 1: Create Your Configuration Repository

1. Create or choose a repository to store your version configuration
2. Create a file named `version-requirements.yml` in the root (or any path you prefer)
3. Use this template:

```yaml
wordpress:
  requires_at_least: "6.0"
  tested_up_to: "6.8"

php:
  requires: "7.4"

woocommerce:
  requires_at_least: "8.0"
  tested_up_to: "9.5"
```

### Step 2: Configure Repository Variables

1. Go to your plugin repository's **Settings** → **Secrets and variables** → **Actions** → **Variables**
2. Click **New repository variable**
3. Add:
   - **Name**: `VERSION_CONFIG_REPO`
   - **Value**: `your-username/your-config-repo` (e.g., `Open-WP-Club/plugin-configs`)

### Step 3: Verify Permissions

Ensure the `GITHUB_TOKEN` has the following permissions in your repository settings:
- **Contents**: Read and write
- **Pull requests**: Read and write

Go to **Settings** → **Actions** → **General** → **Workflow permissions** and select:
- ✅ Read and write permissions
- ✅ Allow GitHub Actions to create and approve pull requests

### Step 4: Test the Workflow

1. Go to **Actions** tab in your repository
2. Select **Update Version Requirements** workflow
3. Click **Run workflow**
4. Choose:
   - Leave `config_repo` empty (uses repository variable)
   - Leave `config_path` empty (uses default `version-requirements.yml`)
   - Or provide custom values to override

## How It Works

### Automatic Detection

The workflow automatically detects if your plugin is a WooCommerce plugin by searching for these keywords:
- "woocommerce"
- "woo commerce"
- "woo-commerce"

Search locations:
- `readme.txt` (case-insensitive)
- Main plugin PHP file (case-insensitive)

### Files Updated

#### Main Plugin File (`*.php`)
Updates or adds these fields in the plugin header:
```php
* Requires at least: 6.0
* Requires PHP: 7.4
* WC requires at least: 8.0      // Only for WooCommerce plugins
* WC tested up to: 9.5            // Only for WooCommerce plugins
```

#### readme.txt
Updates or adds these fields:
```
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
WC requires at least: 8.0         // Only for WooCommerce plugins
WC tested up to: 9.5              // Only for WooCommerce plugins
```

### Schedule

The workflow runs automatically:
- **Every 4 months** on the 1st at midnight UTC
- Specifically: January 1, May 1, September 1

You can also trigger it manually anytime.

## Manual Triggering

### Using the GitHub UI

1. Go to **Actions** tab
2. Select **Update Version Requirements**
3. Click **Run workflow**
4. Optionally provide:
   - `config_repo`: Override the default config repository
   - `config_path`: Override the default config file path

### Using GitHub CLI

```bash
gh workflow run update-version-requirements.yml
```

With custom configuration:
```bash
gh workflow run update-version-requirements.yml \
  -f config_repo="owner/repo" \
  -f config_path="path/to/config.yml"
```

## Customization

### Change the Schedule

Edit `.github/workflows/update-version-requirements.yml`:

```yaml
schedule:
  # Run every 2 months instead
  - cron: '0 0 1 */2 *'

  # Run on the 15th of every 6 months
  - cron: '0 0 15 */6 *'
```

### Change Configuration File Location

If your config file is not in the root or has a different name:

**Option 1**: Update repository variable `VERSION_CONFIG_PATH`
**Option 2**: Provide `config_path` input when triggering manually
**Option 3**: Edit the workflow default value

### Multiple Configuration Files

If you have different version requirements for different plugins:

1. Create multiple config files:
   - `version-requirements-legacy.yml`
   - `version-requirements-modern.yml`
2. Trigger manually with the appropriate `config_path`

Or set up different repository variables per plugin.

## Troubleshooting

### No changes detected

The workflow compares versions with your current files. If versions match, no PR is created.

### Authentication errors

Ensure:
- Repository variable `VERSION_CONFIG_REPO` is set correctly
- Config repository is accessible (public or `GITHUB_TOKEN` has access)
- Workflow permissions are configured correctly

### Push failures

The workflow includes retry logic (4 attempts with exponential backoff). If all retries fail:
- Check network status
- Verify branch protection rules allow Actions to push
- Check workflow permissions

### WooCommerce requirements not added

The workflow searches for WooCommerce keywords. If not detected:
- Verify your plugin mentions "WooCommerce" in readme.txt or main PHP file
- Check the Action logs to see if detection worked
- Manually add the keywords if needed

## Example Pull Request

When changes are detected, a PR is created with:

**Title**: `chore: update version requirements`

**Body**:
```markdown
## Version Requirements Update

This PR updates the plugin's version requirements based on the centralized configuration.

### Changes:
- **WordPress requires at least:** 6.0
- **WordPress tested up to:** 6.8
- **PHP requires:** 7.4
- **WooCommerce requires at least:** 8.0
- **WooCommerce tested up to:** 9.5

### Configuration Source:
`Open-WP-Club/plugin-configs/version-requirements.yml`

---
*This PR was automatically generated by the version requirements update workflow.*
```

## Best Practices

1. **Keep configuration updated**: Regularly update your central config repository
2. **Test before merging**: Review PRs before merging to ensure compatibility
3. **Document changes**: Update changelog when version requirements change
4. **Version compatibility**: Ensure new requirements are tested
5. **Coordinate updates**: Update multiple plugins together for consistency

## Support

For issues or questions about this workflow:
- Check the [GitHub Actions logs](../../actions)
- Review the [workflow file](workflows/update-version-requirements.yml)
- Check the [example config](version-requirements.example.yml)

## Advanced: Using with Private Repositories

If your configuration repository is private, you'll need to:

1. Create a Personal Access Token (PAT) with `repo` scope
2. Add it as a repository secret named `CONFIG_REPO_TOKEN`
3. Modify the workflow to use this token instead of `GITHUB_TOKEN`

Edit the "Download version configuration" step:
```yaml
- name: Download version configuration
  run: |
    curl -f -H "Authorization: token ${{ secrets.CONFIG_REPO_TOKEN }}" \
         ...
```
