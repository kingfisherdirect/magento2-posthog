PostHog Analytics integration module for Magento 2.

Features

    Global PostHog analytics integration
    Admin panel configuration
    CSP (Content Security Policy) whitelist included
    Store-level configuration support
    Easy to configure for multiple sites

Configuration

Navigate to: Stores > Configuration > General > PostHog
Settings:

    Enable PostHog: Yes/No
    PostHog Project API Key: Your PostHog project API key (e.g., phc_...)
    API Host: PostHog API host URL (default: https://eu.i.posthog.com)
    Person Profiles: Choose when to create person profiles:
        Always
        Identified Only (default)
        Never

Installation

# Enable module
bin/magento module:enable KingfisherDirect_Posthog

# Run setup upgrade
bin/magento setup:upgrade

# Flush cache
bin/magento cache:flush

CLI Configuration

You can configure PostHog via CLI:

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

Multi-site Setup

To configure different PostHog projects per website:

# For a specific website
bin/magento config:set --scope=websites --scope-code=example1 posthog/general/project_api_key "phc_KEY_1"
bin/magento config:set --scope=websites --scope-code=example2 posthog/general/project_api_key "phc_KEY_2"

How It Works

The module adds PostHog tracking script to all frontend pages via the default.xml layout.

The script is injected into the after.body.start container for optimal performance.

File Structure

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

License
Proprietary
