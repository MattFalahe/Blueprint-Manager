# Changelog

Below is a summary of all major updates, improvements, and fixes made to **Blueprint Manager**.
Each version entry lists key changes for easier reference and upgrade planning.

---

## Version 1.0.2 — *December 2025*

### Fixed
- Fixed blueprint requests being submitted with incorrect character
  - Previously, requests were always submitted by the user's most recently added character
  - Users viewing a specific character in SeAT would have requests submitted by a different character
  - This occurred because SeAT v5.x doesn't maintain "selected character" state in session for form submissions

### Added
- **Character Selector**: Added dropdown to request form allowing users to explicitly choose which character submits the request
- Character validation to ensure selected character belongs to the current user
- Translation key for "Requesting Character" label

### Improved
- Request ownership detection now checks all of user's characters instead of just one
- Users can now delete requests made by any of their characters, not just their primary character
- "My Requests" tab now displays requests from all of user's characters

---

## Version 1.0.1 — *December 2025*

### Fixed
- Fixed blueprints disappearing when put into research (ME/TE research, copying jobs)
- Fixed incorrect relationship name in ResearchProgressService (`blueprintType` → `blueprint`)
- Fixed date type errors by adding Carbon::parse() for date conversions
- Fixed collection merge errors in BlueprintService after map() transformations

### Added
- Research blueprints now appear in library with "In Research" status
- Only blueprints from configured containers are tracked during research
- Blueprints remain visible until delivered in-game, not just until timer ends

### Technical Details
- BlueprintService now queries active industry jobs to include research blueprints
- Uses `blueprint_location_id` to filter only jobs from configured containers
- Converts Eloquent Collections to base Collections before merging to avoid key errors

---

## 🎉 Version 1.0.0 — *December 2025* — Initial Release

### Overview
First public release of Blueprint Manager - a comprehensive blueprint library and request management system for EVE Online corporations using SeAT.

### 📚 Blueprint Library
- **Pattern-Based Organization**: Configure container name patterns with wildcards to automatically categorize blueprints
- **Category System**: Group blueprints by ship class, module type, or any custom categorization
- **Automatic Detection**: Scan corporation containers to quickly discover and configure blueprint locations
- **Multi-Corporation Support**: Manage blueprints across multiple corporations in the same SeAT installation
- **Real-Time Data**: Reads directly from SeAT's synced ESI data for always-current blueprint information
- **Aggregated Views**: See quantity, ME/TE ranges, and available runs for each blueprint type
- **Detail Pages**: Drill down to view individual blueprint copies with full specifications
- **BPO/BPC Support**: Handles both blueprint originals and copies seamlessly

### 📝 Request System
- **Simple Request Creation**: Members can request blueprints with specified quantity and runs
- **Request Workflow**: Complete lifecycle from submission through approval/rejection to fulfillment
- **Request States**: 
  - Pending (yellow) - Awaiting manager review
  - Approved (green) - Accepted, awaiting delivery
  - Rejected (red) - Declined with reason
  - Fulfilled (purple) - Completed and delivered
- **Request Notes**: Add context when requesting or responding to requests
- **Self-Service Management**: Users can delete their own pending/rejected requests
- **Request History**: Complete audit trail of all requests with filtering by state and character
- **Manager Dashboard**: Centralized view for processing all corporation requests

### 🔔 Discord & Slack Notifications
- **Webhook Integration**: Rich embed notifications for Discord and Slack
- **Event Types**:
  - New Request (Green) - When members submit requests
  - Request Approved (Green) - When managers accept requests  
  - Request Rejected (Red) - When requests are declined
  - Request Fulfilled (Blue) - When blueprints are delivered
- **Clean Formatting**: Minimal emoji design for professional appearance
- **Per-Event Configuration**: Enable/disable notifications for each event type individually
- **Corporation Filtering**: Send notifications only for specific corporations
- **Test Functionality**: Verify webhook configuration before going live
- **Multiple Webhooks**: Support for multiple webhooks with independent configurations

### 📊 Statistics & Analytics
- **Overall Metrics**: 
  - Total request count
  - Approval rate percentage
  - Fulfillment rate percentage  
  - Average processing time
  - Pending request count
- **Time Series Analysis**: Visualize request trends over 7, 30, and 90 days
- **Character Statistics**: Track most active requesters with request counts and outcomes
- **Blueprint Popularity**: Identify most-requested blueprints with fulfillment rates
- **Corporation Comparison**: Compare request activity across multiple corporations
- **Interactive Charts**: Visual graphs for trend analysis
- **Character Detail Views**: Click through to see individual member request history

### ⚙️ Configuration & Settings
- **Container Configuration**:
  - Add/edit/delete container patterns
  - Wildcard matching with `*` operator
  - Display category customization
  - Enable/disable individual configurations
  - Corporation-specific filters
- **Container Detection**:
  - Automatic scanning of corporation containers
  - Hangar division filtering  
  - One-click configuration creation
  - Station grouping
- **Webhook Management**:
  - Add/edit/delete webhook configurations
  - Event type selection
  - Corporation filters
  - Test button for verification
  - Support for Discord and Slack
- **Detection Settings**: Configure which hangar divisions to scan per corporation

### 🔐 Permission System
Four distinct permission levels:
- **View Library**: Browse available blueprints in the library
- **Create Requests**: Submit blueprint requests and view own requests
- **Manage Requests**: Approve, reject, fulfill all requests and access statistics
- **Settings**: Configure container patterns, webhooks, and system settings

### 🎨 User Interface
- **Dark Theme Optimized**: Designed to match SeAT 5.x dark theme aesthetics
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Clean Layout**: Modern card-based interface with intuitive navigation
- **Color-Coded Status**: Visual badges for quick status identification
- **Search & Filtering**: Find blueprints and requests quickly
- **Sortable Tables**: Click column headers to sort data
- **Detail Modals**: In-page popups for request management
- **Category Navigation**: Filter library by configured categories

### 📖 Documentation
- **Complete Help System**: In-app documentation with 10 major sections
- **12 FAQ Entries**: Common questions and answers
- **Troubleshooting Guides**: Step-by-step solutions for common issues
- **Searchable Documentation**: Quick search functionality
- **Feature Descriptions**: Detailed explanation of all capabilities
- **Configuration Examples**: Real-world setup scenarios
- **Permission Guide**: Role-based access configuration

### 🔧 Technical Features
- **ESI Integration**: Reads from SeAT's synchronized asset and blueprint data
- **Auto-Sync**: Updates automatically when SeAT syncs with EVE ESI
- **Database Migrations**: Clean installation with automatic schema setup
- **Laravel 10 Compatible**: Built on modern Laravel framework
- **SeAT 5.x Compatible**: Designed for SeAT 5.0 and above
- **RESTful API**: AJAX-powered interface for smooth user experience
- **Efficient Queries**: Optimized database access patterns
- **Relationship Loading**: Eager loading for performance

### 📦 Installation
Simple two-command installation:
```bash
composer require mattfalahe/blueprint-manager
php artisan migrate
```

Automatic setup includes:
- Database table creation
- Permission registration
- Navigation menu items
- Default configuration

### 🚀 First Release Features
This initial release provides a complete, production-ready system for:
- Organizing corporation blueprint libraries
- Managing member blueprint requests
- Tracking request statistics and trends
- Notifying teams via Discord/Slack
- Configuring access permissions

---

## Future Roadmap

Potential features for future releases:
- Export functionality for statistics data
- Advanced search with multiple filters
- Blueprint reservation system
- Request priority levels
- Email notification support
- API endpoints for external integration
- Custom fields for requests
- Approval workflows with multiple reviewers

---

## Contributing

Found a bug or have a feature request? Please create an issue on [GitHub](https://github.com/MattFalahe/blueprint-manager/issues).

## Support

- **Documentation**: In-app help system with comprehensive guides
- **Issues**: [GitHub Issue Tracker](https://github.com/MattFalahe/blueprint-manager/issues)
- **Source Code**: [GitHub Repository](https://github.com/MattFalahe/blueprint-manager)

---

**Thank you for using Blueprint Manager!**

*Developed by Matt Falahe for the EVE Online community*
