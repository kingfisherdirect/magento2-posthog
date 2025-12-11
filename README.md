# KingfisherDirect_Posthog

PostHog Analytics integration module for Magento 2.

## Features

- Global PostHog analytics integration
- Admin panel configuration
- CSP (Content Security Policy) whitelist included
- Store-level configuration support
- Easy to configure for multiple sites (KFD, BS, DWT, GB)

## Configuration

Navigate to: **Stores > Configuration > General > PostHog**

### Settings:

1. **Enable PostHog**: Yes/No
2. **PostHog Project API Key**: Your PostHog project API key (e.g., `phc_...`)
3. **API Host**: PostHog API host URL (default: `https://eu.i.posthog.com`)
4. **Person Profiles**: Choose when to create person profiles:
   - Always
   - Identified Only (default)
   - Never

## Installation

```bash
# Enable module
bin/magento module:enable KingfisherDirect_Posthog

# Run setup upgrade
bin/magento setup:upgrade

# Flush cache
bin/magento cache:flush
```

## CLI Configuration

You can configure PostHog via CLI:

```bash
# Enable PostHog
bin/magento config:set posthog/general/enabled 1

# Set API Key
bin/magento config:set posthog/general/project_api_key "phc_YOUR_KEY_HERE"

# Set API Host
bin/magento config:set posthog/general/api_host "https://eu.i.posthog.com"

# Set Person Profiles
bin/magento config:set posthog/general/person_profiles "identified_only"

# Flush cache
bin/magento cache:flush
```

## Multi-site Setup

For different sites (KFD, BS, DWT, GB), you can configure different PostHog projects:

```bash
# For a specific website
bin/magento config:set --scope=websites --scope-code=kfd posthog/general/project_api_key "phc_KFD_KEY"
bin/magento config:set --scope=websites --scope-code=bs posthog/general/project_api_key "phc_BS_KEY"
bin/magento config:set --scope=websites --scope-code=dwt posthog/general/project_api_key "phc_DWT_KEY"
bin/magento config:set --scope=websites --scope-code=gb posthog/general/project_api_key "phc_GB_KEY"
```

## How It Works

The module adds PostHog tracking script to all frontend pages via the `default.xml` layout.

The script is injected into the `after.body.start` container for optimal performance.

## File Structure

```
KingfisherDirect/Posthog/
├── Block/
│   └── Script.php              # Block class for rendering
├── Helper/
│   └── Data.php                # Helper for configuration
├── Model/
│   └── Config/
│       └── Source/
│           └── PersonProfiles.php  # Source model for dropdown
├── etc/
│   ├── acl.xml                 # ACL configuration
│   ├── adminhtml/
│   │   └── system.xml          # Admin system configuration
│   ├── config.xml              # Default configuration values
│   ├── csp_whitelist.xml       # CSP whitelist
│   └── module.xml              # Module declaration
├── view/
│   └── frontend/
│       ├── layout/
│       │   └── default.xml     # Global layout
│       └── templates/
│           └── script.phtml    # PostHog script template
└── registration.php            # Module registration
```

## License

Proprietary - KingfisherDirect
