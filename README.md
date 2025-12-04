# Blueprint Manager for SeAT

[![Latest Version](https://img.shields.io/packagist/v/mattfalahe/blueprint-manager.svg?style=flat-square)](https://packagist.org/packages/mattfalahe/blueprint-manager)
[![License](https://img.shields.io/badge/license-GPL--2.0-blue.svg?style=flat-square)](LICENSE)
[![SeAT](https://img.shields.io/badge/SeAT-5.x-blue.svg?style=flat-square)](https://github.com/eveseat/seat)

A comprehensive blueprint library and request management system for EVE Online corporations using SeAT. Organize your corporation's blueprints, manage member requests, track statistics, and receive Discord/Slack notifications for seamless blueprint distribution.

## Features

### 📚 Blueprint Library
- **Pattern-Based Organization**: Configure container name patterns with wildcards to automatically categorize blueprints
- **Custom Categories**: Group blueprints by ship class, module type, or any custom system
- **Automatic Detection**: Scan corporation containers to quickly discover and configure blueprint locations
- **Multi-Corporation Support**: Manage blueprints across multiple corporations
- **Real-Time Data**: Automatically syncs with SeAT's ESI updates

### 📝 Request System
- **Simple Request Creation**: Members request blueprints with specified quantity and runs
- **Complete Workflow**: Pending → Approved → Rejected → Fulfilled states
- **Request Management**: Approve, reject, and fulfill requests with notes
- **Self-Service**: Users can delete their own pending/rejected requests
- **Request History**: Complete audit trail with filtering and search

### 🔔 Discord & Slack Notifications
- **Webhook Integration**: Rich embed notifications for request events
- **Event Types**: New requests, approvals, rejections, and fulfillments
- **Per-Event Configuration**: Enable/disable notifications individually
- **Corporation Filtering**: Target notifications to specific corporations
- **Clean Formatting**: Professional, minimal emoji design
- **Test Function**: Verify webhook configuration

### 📊 Statistics & Analytics
- **Overall Metrics**: Total requests, approval rates, fulfillment rates, processing times
- **Time Series Analysis**: Visualize trends over 7, 30, and 90 days
- **Character Statistics**: Track most active requesters
- **Blueprint Popularity**: Identify most-requested blueprints
- **Corporation Comparison**: Compare activity across multiple corporations
- **Interactive Charts**: Visual graphs for trend analysis

### ⚙️ Configuration & Settings
- **Container Configuration**: Define patterns to organize blueprint library
- **Container Detection**: Automatically scan and suggest configurations
- **Webhook Management**: Configure multiple Discord/Slack webhooks
- **Detection Settings**: Customize which hangars to scan per corporation

### 🔐 Permission System
- **View Library**: Browse available blueprints
- **Create Requests**: Submit blueprint requests
- **Manage Requests**: Approve/reject/fulfill requests and view statistics
- **Settings**: Configure container patterns and webhooks

## Installation

```bash
composer require mattfalahe/blueprint-manager
php artisan migrate
```

That's it! The plugin will automatically:
- Create database tables
- Register permissions
- Set up navigation menu items
- Begin tracking blueprints on next SeAT sync

## Usage

### Getting Started

1. **Configure Container Patterns** (Settings page)
   - Add patterns matching your container names (e.g., `*Capital*`, `*Frigate*`)
   - Use "Detect Containers" to automatically scan your corporation
   - Assign display categories for organization

2. **Set Up Notifications** (Optional)
   - Create Discord/Slack webhooks
   - Configure which events to notify
   - Test webhook to verify connectivity

3. **Configure Permissions**
   - Assign Blueprint Manager permissions to corporation roles in SeAT
   - At minimum, members need "View Library" and "Create Requests"
   - Managers need "Manage Requests" for processing

### Blueprint Library

Access the library from the SeAT sidebar. The main page shows:
- All corporation blueprints organized by configured categories
- Quantity available, ME/TE ranges, and runs for each blueprint type
- Category filtering for easy browsing
- Detail views showing individual blueprint copies

### Request System

**For Members:**
1. Navigate to Requests page
2. Click "New Request"
3. Select corporation and blueprint
4. Specify quantity and runs
5. Add notes explaining your need
6. Submit request

**For Managers:**
1. Review pending requests on Requests page
2. Click "Approve" to accept or "Reject" to decline
3. Add notes about delivery or rejection reason
4. Click "Fulfill" after providing the blueprint in-game

### Statistics

Access comprehensive analytics:
- Overall request metrics
- Time series graphs showing trends
- Character activity rankings
- Most popular blueprints
- Corporation comparisons

## Data Synchronization

Blueprint Manager reads directly from SeAT's asset and blueprint tables, which sync automatically with EVE's ESI API. This means:

✅ **New blueprints** added in-game appear after SeAT's next sync  
✅ **Research changes** (ME/TE) update automatically  
✅ **Location changes** reflect when assets sync  
✅ **No manual sync needed** - completely automatic

## Help & Documentation

Complete in-app help system available at `Blueprint Manager → Help`:
- 10 major documentation sections
- 12 FAQ entries
- Troubleshooting guides
- Configuration examples
- Permission setup guides

## Screenshots

*Coming soon - check the Wiki*

## Requirements

- SeAT 5.x
- PHP 8.1+
- Laravel 10+
- Active ESI token with asset and blueprint scopes

## Permissions

Configure in SeAT's role management:

- `blueprint-manager.view` - View blueprint library
- `blueprint-manager.request` - Create and view own requests
- `blueprint-manager.manage_requests` - Process requests and view statistics
- `blueprint-manager.settings` - Configure system settings

## Support

- **Documentation**: In-app help system
- **Issues**: [GitHub Issue Tracker](https://github.com/MattFalahe/blueprint-manager/issues)
- **Source**: [GitHub Repository](https://github.com/MattFalahe/blueprint-manager)
- **Changelog**: [CHANGELOG.MD](CHANGELOG.MD)

## Contributing

Found a bug or have a feature request? Please create an issue on GitHub!

## License

This plugin is licensed under the [GPL-2.0 License](LICENSE).

## Credits

Developed by **Matt Falahe** for the EVE Online community.

Built for SeAT (Simple EVE API Tool) by [eveseat.github.io](https://eveseat.github.io/)

## Other Plugins by Matt Falahe

- **[Structure Manager](https://github.com/MattFalahe/Structure-Manager)** - Comprehensive fuel tracking for Upwell structures and POSes
- **[Corp Wallet Manager](https://github.com/MattFalahe/Corp-Wallet-Manager)** - Corporation wallet journal analysis
- **[SeAT-Discord-Pings](https://github.com/MattFalahe/SeAT-Discord-Pings)** - Discord ping and broadcast management for SeAT 

---

**Happy blueprint managing! o7**

