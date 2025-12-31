@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::common.settings'))
@section('page_header', trans('blueprint-manager::common.settings'))

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/blueprint-manager/css/blueprint-manager.css') }}">
@endpush



@section('full')
<div class="blueprint-manager-wrapper">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            {{-- Settings Tabs --}}
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="container-tab" data-toggle="tab" href="#containerConfig" role="tab">
                        <i class="fas fa-box"></i> Container Configuration
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="webhook-tab" data-toggle="tab" href="#webhookConfig" role="tab">
                        <i class="fab fa-discord"></i> {{ trans('blueprint-manager::common.webhooks') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabContent">
                {{-- Container Configuration Tab --}}
                <div class="tab-pane fade show active" id="containerConfig" role="tabpanel">
                    {{-- Container Configuration Section --}}
                    <div class="settings-section mt-3">
                        <h4>
                            <i class="fas fa-box"></i> {{ trans('blueprint-manager::common.container_configuration') }}
                        </h4>

                        <div class="info-banner">
                            <i class="fas fa-info-circle"></i>
                            <strong>About Container Configuration:</strong>
                            Configure which corporation asset containers contain blueprints and how they should be categorized. 
                            Use the "Detect Containers" feature to automatically find containers with blueprints.
                        </div>

                {{-- Corporation Selector & Actions --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="corporationSelect">{{ trans('blueprint-manager::common.select_corporation') }}</label>
                        <select id="corporationSelect" class="form-control">
                            <option value="">-- {{ trans('blueprint-manager::common.all_corporations') }} --</option>
                            @foreach($corporations as $corp)
                            <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary mr-2" id="addConfigBtn">
                            <i class="fas fa-plus"></i> {{ trans('blueprint-manager::common.add_configuration') }}
                        </button>
                        <button type="button" class="btn btn-info" id="detectContainersBtn" disabled>
                            <i class="fas fa-search"></i> {{ trans('blueprint-manager::common.detect_containers') }}
                        </button>
                    </div>
                </div>

                {{-- Hangar Division Filter Section --}}
                <div class="card mb-3" id="hangarFilterCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter"></i> Detection Filter Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Select which corporation hangar divisions to scan when detecting containers. 
                            This helps narrow down results and improves detection speed.
                        </p>
                        
                        <div class="row" id="hangarCheckboxContainer">
                            <!-- Hangar checkboxes will be dynamically loaded here -->
                            <div class="col-12 text-center">
                                <i class="fas fa-spinner fa-spin"></i> Loading hangar divisions...
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-secondary" id="selectAllHangars">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="deselectAllHangars">
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                            <button type="button" class="btn btn-sm btn-primary float-right" id="saveHangarSettings">
                                <i class="fas fa-save"></i> Save Filter Settings
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Configuration Table --}}
                <div class="table-responsive">
                    <table id="configurationsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('blueprint-manager::common.corporation') }}</th>
                                <th>{{ trans('blueprint-manager::common.container_name') }}</th>
                                <th>{{ trans('blueprint-manager::common.display_category') }}</th>
                                <th>{{ trans('blueprint-manager::common.match_type') }}</th>
                                <th>{{ trans('blueprint-manager::common.priority') }}</th>
                                <th>{{ trans('blueprint-manager::common.status') }}</th>
                                <th>{{ trans('blueprint-manager::common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($configurations as $config)
                            <tr data-config-id="{{ $config->id }}" data-corp-id="{{ $config->corporation_id }}">
                                <td>{{ $config->corporation->name ?? 'Unknown' }}</td>
                                <td><code>{{ $config->container_name }}</code></td>
                                <td><span class="badge badge-secondary">{{ $config->display_category }}</span></td>
                                <td><span class="badge match-type-badge badge-info">{{ ucfirst(str_replace('_', ' ', $config->match_type)) }}</span></td>
                                <td><span class="badge badge-priority badge-dark">{{ $config->priority }}</span></td>
                                <td>
                                    @if($config->enabled)
                                    <i class="fas fa-check-circle status-enabled"></i> {{ trans('blueprint-manager::common.enabled') }}
                                    @else
                                    <i class="fas fa-times-circle status-disabled"></i> {{ trans('blueprint-manager::common.disabled') }}
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-warning btn-edit" data-config-id="{{ $config->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-delete" data-config-id="{{ $config->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> {{-- End table-responsive --}}
            </div> {{-- End settings-section --}}
        </div> {{-- End Container Configuration Tab --}}

        {{-- Discord Webhooks Tab --}}
        <div class="tab-pane fade" id="webhookConfig" role="tabpanel">
            <div class="settings-section mt-3">
                <h4>
                    <i class="fab fa-discord"></i> {{ trans('blueprint-manager::common.webhooks') }}
                </h4>

                <div class="info-banner">
                    <i class="fas fa-info-circle"></i>
                    <strong>About Discord Webhooks:</strong>
                    {{ trans('blueprint-manager::common.webhook_description') }}
                </div>

                {{-- Add Webhook Button --}}
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" id="addWebhookBtn">
                        <i class="fas fa-plus"></i> {{ trans('blueprint-manager::common.add_webhook') }}
                    </button>
                </div>

                {{-- Webhooks Table --}}
                <div class="table-responsive">
                    <table id="webhooksTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('blueprint-manager::common.webhook_name') }}</th>
                                <th>{{ trans('blueprint-manager::common.corporation') }}</th>
                                <th>{{ trans('blueprint-manager::common.notify_on_created') }}</th>
                                <th>{{ trans('blueprint-manager::common.notify_on_approved') }}</th>
                                <th>{{ trans('blueprint-manager::common.notify_on_rejected') }}</th>
                                <th>{{ trans('blueprint-manager::common.notify_on_fulfilled') }}</th>
                                <th>{{ trans('blueprint-manager::common.status') }}</th>
                                <th>{{ trans('blueprint-manager::common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="webhooksTableBody">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> {{ trans('blueprint-manager::common.loading') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div> {{-- End Discord Webhooks Tab --}}

            </div> {{-- End Tab Content --}}
    </div>
</div>

{{-- Add Configuration Modal --}}
<div class="modal fade" id="addConfigModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.add_configuration') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addConfigForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_corporation_id">{{ trans('blueprint-manager::common.corporation') }} *</label>
                        <select id="add_corporation_id" name="corporation_id" class="form-control" required>
                            <option value="">-- {{ trans('blueprint-manager::common.select_corporation') }} --</option>
                            @foreach($corporations as $corp)
                            <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="add_container_name">{{ trans('blueprint-manager::common.container_name') }} *</label>
                        <input type="text" id="add_container_name" name="container_name" class="form-control" required>
                        <small class="form-text text-muted">The container name pattern to match</small>
                    </div>

                    <div class="form-group">
                        <label for="add_display_category">{{ trans('blueprint-manager::common.display_category') }} *</label>
                        <input type="text" id="add_display_category" name="display_category" class="form-control" required>
                        <small class="form-text text-muted">How to categorize these blueprints (e.g., BPO, BPC, Ship BPOs)</small>
                    </div>

                    <div class="form-group">
                        <label for="add_match_type">{{ trans('blueprint-manager::common.match_type') }} *</label>
                        <select id="add_match_type" name="match_type" class="form-control" required>
                            <option value="exact">{{ trans('blueprint-manager::common.exact') }}</option>
                            <option value="contains">{{ trans('blueprint-manager::common.contains') }}</option>
                            <option value="starts_with">{{ trans('blueprint-manager::common.starts_with') }}</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Exact:</strong> Container name must match exactly<br>
                            <strong>Contains:</strong> Container name contains this text<br>
                            <strong>Starts With:</strong> Container name starts with this text
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="add_priority">{{ trans('blueprint-manager::common.priority') }}</label>
                        <input type="number" id="add_priority" name="priority" class="form-control" value="0" min="0" max="100">
                        <small class="form-text text-muted">Higher priority wins when multiple patterns match (0-100)</small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="add_enabled" name="enabled" class="form-check-input" checked>
                        <label for="add_enabled" class="form-check-label">{{ trans('blueprint-manager::common.enabled') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('blueprint-manager::common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Configuration Modal --}}
<div class="modal fade" id="editConfigModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.edit_configuration') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editConfigForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_config_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('blueprint-manager::common.corporation') }}</label>
                        <input type="text" id="edit_corporation_name" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit_container_name">{{ trans('blueprint-manager::common.container_name') }} *</label>
                        <input type="text" id="edit_container_name" name="container_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_display_category">{{ trans('blueprint-manager::common.display_category') }} *</label>
                        <input type="text" id="edit_display_category" name="display_category" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_match_type">{{ trans('blueprint-manager::common.match_type') }} *</label>
                        <select id="edit_match_type" name="match_type" class="form-control" required>
                            <option value="exact">{{ trans('blueprint-manager::common.exact') }}</option>
                            <option value="contains">{{ trans('blueprint-manager::common.contains') }}</option>
                            <option value="starts_with">{{ trans('blueprint-manager::common.starts_with') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_priority">{{ trans('blueprint-manager::common.priority') }}</label>
                        <input type="number" id="edit_priority" name="priority" class="form-control" min="0" max="100">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="edit_enabled" name="enabled" class="form-check-input">
                        <label for="edit_enabled" class="form-check-label">{{ trans('blueprint-manager::common.enabled') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('blueprint-manager::common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteConfigModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.delete_configuration') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this container configuration?</p>
                <p><strong>Container:</strong> <span id="delete_container_name"></span></p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="delete_config_id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ trans('blueprint-manager::common.delete') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Detect Containers Modal --}}
<div class="modal fade" id="detectContainersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.detect_containers') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detectContainersResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Webhook Modal --}}
<div class="modal fade" id="webhookModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="webhookModalTitle">{{ trans('blueprint-manager::common.add_webhook') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="webhookForm">
                @csrf
                <input type="hidden" id="webhook_id" name="webhook_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="webhook_name">{{ trans('blueprint-manager::common.webhook_name') }} *</label>
                        <input type="text" id="webhook_name" name="name" class="form-control" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="webhook_url">{{ trans('blueprint-manager::common.webhook_url') }} *</label>
                        <input type="url" id="webhook_url" name="webhook_url" class="form-control" required 
                               placeholder="https://discord.com/api/webhooks/...">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter your Discord webhook URL
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="webhook_corporation_id">{{ trans('blueprint-manager::common.corporation') }}</label>
                        <select id="webhook_corporation_id" name="corporation_id" class="form-control">
                            <option value="">-- {{ trans('blueprint-manager::common.all_corporations_webhook') }} --</option>
                            @foreach($corporations as $corp)
                            <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Leave empty to notify for all corporations
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Notification Events & Role Pings</label>
                        <small class="form-text text-muted mb-2">
                            <i class="fas fa-info-circle"></i> Configure which events trigger notifications and optionally specify Discord role IDs to ping for each event
                        </small>
                        
                        {{-- Request Created --}}
                        <div class="border rounded p-3 mb-2">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="webhook_notify_created" name="notify_created" checked>
                                <label class="custom-control-label font-weight-bold" for="webhook_notify_created">
                                    {{ trans('blueprint-manager::common.notify_on_created') }}
                                </label>
                            </div>
                            <div class="form-group mb-0">
                                <label for="webhook_ping_role_created" class="small">Role ID to Ping (optional)</label>
                                <input type="text" 
                                       id="webhook_ping_role_created" 
                                       name="ping_role_created" 
                                       class="form-control form-control-sm" 
                                       placeholder="e.g., 123456789012345678"
                                       maxlength="50">
                                <small class="form-text text-muted">
                                    <i class="fas fa-at"></i> Discord role ID to mention when a new request is created
                                </small>
                            </div>
                        </div>

                        {{-- Request Approved --}}
                        <div class="border rounded p-3 mb-2">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="webhook_notify_approved" name="notify_approved" checked>
                                <label class="custom-control-label font-weight-bold" for="webhook_notify_approved">
                                    {{ trans('blueprint-manager::common.notify_on_approved') }}
                                </label>
                            </div>
                            <div class="form-group mb-0">
                                <label for="webhook_ping_role_approved" class="small">Role ID to Ping (optional)</label>
                                <input type="text" 
                                       id="webhook_ping_role_approved" 
                                       name="ping_role_approved" 
                                       class="form-control form-control-sm" 
                                       placeholder="e.g., 123456789012345678"
                                       maxlength="50">
                                <small class="form-text text-muted">
                                    <i class="fas fa-at"></i> Discord role ID to mention when a request is approved
                                </small>
                            </div>
                        </div>

                        {{-- Request Rejected --}}
                        <div class="border rounded p-3 mb-2">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="webhook_notify_rejected" name="notify_rejected" checked>
                                <label class="custom-control-label font-weight-bold" for="webhook_notify_rejected">
                                    {{ trans('blueprint-manager::common.notify_on_rejected') }}
                                </label>
                            </div>
                            <div class="form-group mb-0">
                                <label for="webhook_ping_role_rejected" class="small">Role ID to Ping (optional)</label>
                                <input type="text" 
                                       id="webhook_ping_role_rejected" 
                                       name="ping_role_rejected" 
                                       class="form-control form-control-sm" 
                                       placeholder="e.g., 123456789012345678"
                                       maxlength="50">
                                <small class="form-text text-muted">
                                    <i class="fas fa-at"></i> Discord role ID to mention when a request is rejected
                                </small>
                            </div>
                        </div>

                        {{-- Request Fulfilled --}}
                        <div class="border rounded p-3 mb-2">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="webhook_notify_fulfilled" name="notify_fulfilled" checked>
                                <label class="custom-control-label font-weight-bold" for="webhook_notify_fulfilled">
                                    {{ trans('blueprint-manager::common.notify_on_fulfilled') }}
                                </label>
                            </div>
                            <div class="form-group mb-0">
                                <label for="webhook_ping_role_fulfilled" class="small">Role ID to Ping (optional)</label>
                                <input type="text" 
                                       id="webhook_ping_role_fulfilled" 
                                       name="ping_role_fulfilled" 
                                       class="form-control form-control-sm" 
                                       placeholder="e.g., 123456789012345678"
                                       maxlength="50">
                                <small class="form-text text-muted">
                                    <i class="fas fa-at"></i> Discord role ID to mention when a request is fulfilled
                                </small>
                            </div>
                        </div>

                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> To find a Discord role ID, enable Developer Mode in Discord settings, 
                            then right-click a role and select "Copy ID". You can use the same role ID for all events or different ones for each step.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="webhook_enabled" name="enabled" checked>
                            <label class="custom-control-label" for="webhook_enabled">
                                {{ trans('blueprint-manager::common.enabled') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="button" class="btn btn-info" id="testWebhookBtn">
                        <i class="fas fa-vial"></i> {{ trans('blueprint-manager::common.test_webhook') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveWebhookBtn">
                        <i class="fas fa-save"></i> {{ trans('blueprint-manager::common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Webhook Modal --}}
<div class="modal fade" id="deleteWebhookModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.delete_webhook') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this webhook configuration?</p>
                <p><strong>Webhook:</strong> <span id="delete_webhook_name"></span></p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="delete_webhook_id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteWebhookBtn">{{ trans('blueprint-manager::common.delete') }}</button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('javascript')
<script>
$(document).ready(function() {
    let configurationsTable;
    let selectedCorporationId = null;
    let divisionNames = {}; // Track loaded division names
    let checkboxStateCache = {}; // Cache for unsaved checkbox states per corporation
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initialize DataTable
    configurationsTable = $('#configurationsTable').DataTable({
        order: [[4, 'desc'], [2, 'asc']], // Sort by priority desc, then category asc
        pageLength: 25,
        responsive: true
    });

    // Corporation selector change
    $('#corporationSelect').on('change', function() {
        // Cache current checkbox state before switching
        if (selectedCorporationId) {
            const checkedDivisions = [];
            $('.hangar-division-checkbox:checked').each(function() {
                checkedDivisions.push($(this).val());
            });
            
            if ($('.hangar-division-checkbox').length > 0) {
                checkboxStateCache[selectedCorporationId] = {
                    checkedDivisions: checkedDivisions,
                    divisionNames: divisionNames
                };
            }
        }
        
        selectedCorporationId = $(this).val();
        
        // Enable/disable detect button
        $('#detectContainersBtn').prop('disabled', !selectedCorporationId);
        
        // Load detection settings for hangar filter
        if (selectedCorporationId) {
            configurationsTable.column(0).search('^' + $('option:selected', this).text() + '$', true, false).draw();
            loadDetectionSettings(selectedCorporationId);
        } else {
            configurationsTable.column(0).search('').draw();
            $('#hangarFilterCard').hide();
        }
    });

    /**
     * Load detection settings when corporation is selected
     */
    function loadDetectionSettings(corporationId) {
        // Check if we have cached unsaved changes
        if (checkboxStateCache[corporationId]) {
            console.log('Using cached checkbox state for corporation ' + corporationId);
            $('#hangarFilterCard').show();
            divisionNames = checkboxStateCache[corporationId].divisionNames;
            buildHangarCheckboxes(
                checkboxStateCache[corporationId].checkedDivisions,
                divisionNames
            );
            return;
        }

        // Load from server if no cache
        $.ajax({
            url: '{{ route('blueprint-manager.settings.detection-settings', ['corporationId' => '__CORP_ID__']) }}'.replace('__CORP_ID__', corporationId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Show the hangar filter card
                    $('#hangarFilterCard').show();
                    
                    // Store division names
                    divisionNames = response.division_names || {};
                    
                    // Build hangar checkboxes with actual names
                    buildHangarCheckboxes(response.hangar_divisions || [], divisionNames);
                }
            },
            error: function() {
                console.error('Failed to load detection settings');
                // Default to all checked with generic names
                const defaultDivisions = ['CorpSAG1', 'CorpSAG2', 'CorpSAG3', 'CorpSAG4', 'CorpSAG5', 'CorpSAG6', 'CorpSAG7', 'AssetSafety'];
                const defaultNames = {
                    'CorpSAG1': 'Division 1',
                    'CorpSAG2': 'Division 2',
                    'CorpSAG3': 'Division 3',
                    'CorpSAG4': 'Division 4',
                    'CorpSAG5': 'Division 5',
                    'CorpSAG6': 'Division 6',
                    'CorpSAG7': 'Division 7',
                    'AssetSafety': 'Asset Safety'
                };
                buildHangarCheckboxes(defaultDivisions, defaultNames);
                $('#hangarFilterCard').show();
            }
        });
    }

    /**
     * Build hangar checkbox UI with actual names
     */
    function buildHangarCheckboxes(enabledDivisions, names) {
        const divisions = ['CorpSAG1', 'CorpSAG2', 'CorpSAG3', 'CorpSAG4', 'CorpSAG5', 'CorpSAG6', 'CorpSAG7', 'AssetSafety'];
        
        let html = '';
        
        // Split into two columns
        const halfPoint = Math.ceil(divisions.length / 2);
        
        // First column
        html += '<div class="col-md-6">';
        html += '<h6>Corporation Hangars</h6>';
        for (let i = 0; i < halfPoint; i++) {
            const division = divisions[i];
            const isChecked = enabledDivisions.includes(division);
            const divisionName = names[division] || division;
            
            html += '<div class="form-check" style="margin-bottom: 10px;">';
            html += '<input class="form-check-input hangar-division-checkbox" type="checkbox" ';
            html += 'value="' + division + '" id="hangar_' + division + '" ';
            if (isChecked) html += 'checked';
            html += '>';
            html += '<label class="form-check-label" for="hangar_' + division + '">';
            html += '<strong>' + divisionName + '</strong> <span class="text-muted">(' + division + ')</span>';
            html += '</label>';
            html += '</div>';
        }
        html += '</div>';
        
        // Second column
        html += '<div class="col-md-6">';
        html += '<h6>Additional Divisions</h6>';
        for (let i = halfPoint; i < divisions.length; i++) {
            const division = divisions[i];
            const isChecked = enabledDivisions.includes(division);
            const divisionName = names[division] || division;
            
            html += '<div class="form-check" style="margin-bottom: 10px;">';
            html += '<input class="form-check-input hangar-division-checkbox" type="checkbox" ';
            html += 'value="' + division + '" id="hangar_' + division + '" ';
            if (isChecked) html += 'checked';
            html += '>';
            html += '<label class="form-check-label" for="hangar_' + division + '">';
            html += '<strong>' + divisionName + '</strong> <span class="text-muted">(' + division + ')</span>';
            html += '</label>';
            html += '</div>';
        }
        html += '</div>';
        
        $('#hangarCheckboxContainer').html(html);
    }

    /**
     * Save detection settings
     */
    $('#saveHangarSettings').on('click', function() {
        const corporationId = selectedCorporationId;
        if (!corporationId) {
            showAlert('warning', 'Please select a corporation first');
            return;
        }

        // Get selected hangar divisions
        const selectedDivisions = [];
        $('.hangar-division-checkbox:checked').each(function() {
            selectedDivisions.push($(this).val());
        });

        if (selectedDivisions.length === 0) {
            showAlert('warning', 'Please select at least one hangar division to scan');
            return;
        }

        $.ajax({
            url: '{{ route('blueprint-manager.settings.save-detection-settings', ['corporationId' => '__CORP_ID__']) }}'.replace('__CORP_ID__', corporationId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                hangar_divisions: selectedDivisions
            },
            success: function(response) {
                if (response.success) {
                    // Clear cache after successful save
                    delete checkboxStateCache[corporationId];
                    showAlert('success', 'Detection filter settings saved successfully');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to save detection filter settings');
            }
        });
    });

    /**
     * Select all hangars
     */
    $('#selectAllHangars').on('click', function() {
        $('.hangar-division-checkbox').prop('checked', true);
    });

    /**
     * Deselect all hangars
     */
    $('#deselectAllHangars').on('click', function() {
        $('.hangar-division-checkbox').prop('checked', false);
    });

    // Add Configuration Button
    $('#addConfigBtn').on('click', function() {
        // Pre-select corporation if filtered
        if (selectedCorporationId) {
            $('#add_corporation_id').val(selectedCorporationId);
        }
        $('#addConfigModal').modal('show');
    });

    // Add Configuration Form Submit
    $('#addConfigForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            corporation_id: $('#add_corporation_id').val(),
            container_name: $('#add_container_name').val(),
            display_category: $('#add_display_category').val(),
            match_type: $('#add_match_type').val(),
            priority: $('#add_priority').val(),
            enabled: $('#add_enabled').is(':checked') ? 1 : 0,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route('blueprint-manager.settings.container.store') }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#addConfigModal').modal('hide');
                    $('#addConfigForm')[0].reset();
                    location.reload(); // Reload to show new config
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to add configuration';
                showAlert('danger', errorMsg);
            }
        });
    });

    // Edit Configuration Button
    $(document).on('click', '.btn-edit', function() {
        const configId = $(this).data('config-id');
        const row = $(this).closest('tr');
        
        // Populate form with existing data
        $('#edit_config_id').val(configId);
        $('#edit_corporation_name').val(row.find('td:eq(0)').text());
        $('#edit_container_name').val(row.find('code').text());
        $('#edit_display_category').val(row.find('td:eq(2) .badge').text());
        
        // Set match type
        const matchTypeText = row.find('.match-type-badge').text().toLowerCase().replace(' ', '_');
        $('#edit_match_type').val(matchTypeText);
        
        // Set priority
        $('#edit_priority').val(row.find('.badge-priority').text());
        
        // Set enabled status
        const isEnabled = row.find('.status-enabled').length > 0;
        $('#edit_enabled').prop('checked', isEnabled);
        
        $('#editConfigModal').modal('show');
    });

    // Edit Configuration Form Submit
    $('#editConfigForm').on('submit', function(e) {
        e.preventDefault();
        
        const configId = $('#edit_config_id').val();
        const formData = {
            container_name: $('#edit_container_name').val(),
            display_category: $('#edit_display_category').val(),
            match_type: $('#edit_match_type').val(),
            priority: $('#edit_priority').val(),
            enabled: $('#edit_enabled').is(':checked') ? 1 : 0,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        $.ajax({
            url: '{{ route('blueprint-manager.settings.container.update', '') }}/' + configId,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#editConfigModal').modal('hide');
                    location.reload(); // Reload to show updated config
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to update configuration';
                showAlert('danger', errorMsg);
            }
        });
    });

    // Delete Configuration Button
    $(document).on('click', '.btn-delete', function() {
        const configId = $(this).data('config-id');
        const row = $(this).closest('tr');
        const containerName = row.find('code').text();
        
        $('#delete_config_id').val(configId);
        $('#delete_container_name').text(containerName);
        $('#deleteConfigModal').modal('show');
    });

    // Confirm Delete
    $('#confirmDeleteBtn').on('click', function() {
        const configId = $('#delete_config_id').val();

        $.ajax({
            url: '{{ route('blueprint-manager.settings.container.delete', '') }}/' + configId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#deleteConfigModal').modal('hide');
                    location.reload(); // Reload to remove deleted config
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to delete configuration';
                showAlert('danger', errorMsg);
            }
        });
    });

    // Detect Containers Button
    $('#detectContainersBtn').on('click', function() {
        if (!selectedCorporationId) {
            showAlert('warning', 'Please select a corporation first');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Detecting...');

        $.ajax({
            url: '{{ route('blueprint-manager.settings.detect', '') }}/' + selectedCorporationId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayDetectedContainers(response);
                    $('#detectContainersModal').modal('show');
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to detect containers';
                showAlert('danger', errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-search"></i> {{ trans('blueprint-manager::common.detect_containers') }}');
            }
        });
    });

    // When detect containers modal is shown, show filter info
    $('#detectContainersModal').on('show.bs.modal', function() {
        const checkedCount = $('.hangar-division-checkbox:checked').length;
        const totalCount = $('.hangar-division-checkbox').length;
        
        if (checkedCount > 0 && checkedCount < totalCount) {
            // Build list of enabled division names
            const enabledNames = [];
            $('.hangar-division-checkbox:checked').each(function() {
                const divisionKey = $(this).val();
                const divisionName = divisionNames[divisionKey] || divisionKey;
                enabledNames.push(divisionName);
            });
            
            const filterNote = `<div class="alert alert-info mt-2">
                <i class="fas fa-info-circle"></i> 
                <strong>Filter Active:</strong> Scanning ${checkedCount} of ${totalCount} hangar divisions:<br>
                <small>${enabledNames.join(', ')}</small>
            </div>`;
            
            // Wait a moment for the modal to open, then prepend the note
            setTimeout(function() {
                if ($('#detectContainersResult .alert-info').length === 0) {
                    $('#detectContainersResult').prepend(filterNote);
                }
            }, 100);
        }
    });

    // Display detected containers with station information
    function displayDetectedContainers(data) {
        let html = '<div class="alert alert-info">';
        html += '<strong><i class="fas fa-info-circle"></i> Detection Results:</strong><br>';
        html += 'Total containers with blueprints: ' + data.total_found + '<br>';
        html += 'Already configured: ' + data.already_configured + '<br>';
        html += 'New containers found: ' + data.new_containers;
        html += '</div>';

        if (data.containers.length > 0) {
            html += '<h6>New Containers:</h6>';
            
            // Group containers by station
            const containersByStation = {};
            data.containers.forEach(function(container) {
                const stationName = container.station_name || 'Unknown Station';
                if (!containersByStation[stationName]) {
                    containersByStation[stationName] = [];
                }
                containersByStation[stationName].push(container);
            });

            // Display grouped by station
            Object.keys(containersByStation).sort().forEach(function(stationName) {
                html += '<div class="mb-3">';
                html += '<h6 class="text-info"><i class="fas fa-building"></i> ' + stationName + '</h6>';
                html += '<ul class="list-group">';
                
                containersByStation[stationName].forEach(function(container) {
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    html += '<code>' + container.container_name + '</code>';
                    html += '<button class="btn btn-sm btn-primary quick-add-config" data-container="' + container.container_name + '">';
                    html += '<i class="fas fa-plus"></i> Add Config';
                    html += '</button>';
                    html += '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
            });
        } else {
            html += '<p class="text-muted">No new containers found. All containers with blueprints are already configured.</p>';
        }

        $('#detectContainersResult').html(html);
    }

    // Quick add config from detect modal
    $(document).on('click', '.quick-add-config', function() {
        const containerName = $(this).data('container');
        $('#detectContainersModal').modal('hide');
        
        // Pre-fill add modal
        $('#add_corporation_id').val(selectedCorporationId);
        $('#add_container_name').val(containerName);
        $('#addConfigModal').modal('show');
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + '"></i> ' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('.container-fluid').prepend(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // ============================================================================
    // WEBHOOK MANAGEMENT
    // ============================================================================

    let webhooks = [];

    // Load webhooks
    function loadWebhooks() {
        console.log('Loading webhooks...');
        $.ajax({
            url: '{{ route("blueprint-manager.settings.webhooks") }}',
            method: 'GET',
            success: function(response) {
                console.log('Webhook response:', response);
                if (response.success) {
                    webhooks = response.configs;
                    console.log('Loaded ' + webhooks.length + ' webhooks');
                    renderWebhooksTable();
                } else {
                    console.error('Response success was false');
                }
            },
            error: function(xhr) {
                console.error('Failed to load webhooks:', xhr);
                showAlert('danger', 'Failed to load webhooks', 'webhookAlert');
            }
        });
    }

    // Render webhooks table
    function renderWebhooksTable() {
        console.log('Rendering webhooks table with', webhooks.length, 'webhooks');
        const tbody = $('#webhooksTableBody');
        tbody.empty();

        if (webhooks.length === 0) {
            tbody.append('<tr><td colspan="8" class="text-center">{{ trans("blueprint-manager::common.no_webhooks") }}</td></tr>');
            return;
        }

        webhooks.forEach(function(webhook) {
            console.log('Rendering webhook:', webhook);
            const corpName = webhook.corporation ? webhook.corporation.name : '{{ trans("blueprint-manager::common.all_corporations_webhook") }}';
            const statusBadge = webhook.enabled 
                ? '<span class="badge badge-success">{{ trans("blueprint-manager::common.enabled") }}</span>'
                : '<span class="badge badge-secondary">{{ trans("blueprint-manager::common.disabled") }}</span>';
            
            const checkIcon = '<i class="fas fa-check text-success"></i>';
            const timesIcon = '<i class="fas fa-times text-muted"></i>';

            const row = `
                <tr>
                    <td>${escapeHtml(webhook.name)}</td>
                    <td>${escapeHtml(corpName)}</td>
                    <td class="text-center">${webhook.notify_created ? checkIcon : timesIcon}</td>
                    <td class="text-center">${webhook.notify_approved ? checkIcon : timesIcon}</td>
                    <td class="text-center">${webhook.notify_rejected ? checkIcon : timesIcon}</td>
                    <td class="text-center">${webhook.notify_fulfilled ? checkIcon : timesIcon}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info edit-webhook-btn" data-id="${webhook.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-webhook-btn" data-id="${webhook.id}" data-name="${escapeHtml(webhook.name)}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        console.log('Webhooks table rendered');
    }

    // Add webhook button
    $('#addWebhookBtn').click(function() {
        $('#webhookModalTitle').text('{{ trans("blueprint-manager::common.add_webhook") }}');
        $('#webhookForm')[0].reset();
        $('#webhook_id').val('');
        $('#webhook_notify_created').prop('checked', true);
        $('#webhook_notify_approved').prop('checked', true);
        $('#webhook_notify_rejected').prop('checked', true);
        $('#webhook_notify_fulfilled').prop('checked', true);
        $('#webhook_enabled').prop('checked', true);
        
        // Clear all role ping fields
        $('#webhook_ping_role_created').val('');
        $('#webhook_ping_role_approved').val('');
        $('#webhook_ping_role_rejected').val('');
        $('#webhook_ping_role_fulfilled').val('');
        
        $('#webhookModal').modal('show');
    });

    // Edit webhook button
    $(document).on('click', '.edit-webhook-btn', function() {
        const webhookId = $(this).data('id');
        const webhook = webhooks.find(w => w.id === webhookId);
        
        if (webhook) {
            $('#webhookModalTitle').text('{{ trans("blueprint-manager::common.edit_webhook") }}');
            $('#webhook_id').val(webhook.id);
            $('#webhook_name').val(webhook.name);
            $('#webhook_url').val(webhook.webhook_url);
            $('#webhook_corporation_id').val(webhook.corporation_id || '');
            $('#webhook_notify_created').prop('checked', webhook.notify_created);
            $('#webhook_notify_approved').prop('checked', webhook.notify_approved);
            $('#webhook_notify_rejected').prop('checked', webhook.notify_rejected);
            $('#webhook_notify_fulfilled').prop('checked', webhook.notify_fulfilled);
            $('#webhook_enabled').prop('checked', webhook.enabled);
            
            // Set role ping fields
            $('#webhook_ping_role_created').val(webhook.ping_role_created || '');
            $('#webhook_ping_role_approved').val(webhook.ping_role_approved || '');
            $('#webhook_ping_role_rejected').val(webhook.ping_role_rejected || '');
            $('#webhook_ping_role_fulfilled').val(webhook.ping_role_fulfilled || '');
            
            $('#webhookModal').modal('show');
        }
    });


    // Delete webhook button
    $(document).on('click', '.delete-webhook-btn', function() {
        const webhookId = $(this).data('id');
        const webhookName = $(this).data('name');
        
        $('#delete_webhook_id').val(webhookId);
        $('#delete_webhook_name').text(webhookName);
        $('#deleteWebhookModal').modal('show');
    });

    // Confirm delete webhook
    $('#confirmDeleteWebhookBtn').click(function() {
        const webhookId = $('#delete_webhook_id').val();
        
        $.ajax({
            url: '{{ route("blueprint-manager.settings.webhooks.delete", ":id") }}'.replace(':id', webhookId),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteWebhookModal').modal('hide');
                    showAlert('success', response.message, 'webhookAlert');
                    loadWebhooks();
                } else {
                    showAlert('danger', response.message, 'webhookAlert');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to delete webhook: ' + (xhr.responseJSON?.message || 'Unknown error'), 'webhookAlert');
            }
        });
    });

    // Test webhook button
    $('#testWebhookBtn').click(function() {
        const webhookUrl = $('#webhook_url').val();
        
        if (!webhookUrl) {
            showAlert('warning', 'Please enter a webhook URL first', 'webhookAlert');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');

        $.ajax({
            url: '{{ route("blueprint-manager.settings.webhooks.test") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                webhook_url: webhookUrl
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message, 'webhookAlert');
                } else {
                    showAlert('danger', response.message, 'webhookAlert');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Test failed: ' + (xhr.responseJSON?.message || 'Unknown error'), 'webhookAlert');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-vial"></i> {{ trans("blueprint-manager::common.test_webhook") }}');
            }
        });
    });

    // Save webhook form
    $('#webhookForm').submit(function(e) {
        e.preventDefault();
        
        const webhookId = $('#webhook_id').val();
        const isEdit = webhookId !== '';
        
        const formData = {
            name: $('#webhook_name').val(),
            webhook_url: $('#webhook_url').val(),
            corporation_id: $('#webhook_corporation_id').val() || null,
            notify_created: $('#webhook_notify_created').is(':checked'),
            notify_approved: $('#webhook_notify_approved').is(':checked'),
            notify_rejected: $('#webhook_notify_rejected').is(':checked'),
            notify_fulfilled: $('#webhook_notify_fulfilled').is(':checked'),
            ping_role_created: $('#webhook_ping_role_created').val() || null,
            ping_role_approved: $('#webhook_ping_role_approved').val() || null,
            ping_role_rejected: $('#webhook_ping_role_rejected').val() || null,
            ping_role_fulfilled: $('#webhook_ping_role_fulfilled').val() || null,
            enabled: $('#webhook_enabled').is(':checked')
        };
    
        const url = isEdit 
            ? '{{ route("blueprint-manager.settings.webhooks.update", ":id") }}'.replace(':id', webhookId)
            : '{{ route("blueprint-manager.settings.webhooks.store") }}';
        
        const method = isEdit ? 'PUT' : 'POST';
    
        $.ajax({
            url: url,
            method: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#webhookModal').modal('hide');
                    showAlert('success', response.message, 'webhookAlert');
                    loadWebhooks();
                } else {
                    showAlert('danger', response.message, 'webhookAlert');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let errorMsg = 'Validation errors:<br>';
                    Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                        errorMsg += '- ' + errors.join('<br>- ') + '<br>';
                    });
                    showAlert('danger', errorMsg, 'webhookAlert');
                } else {
                    showAlert('danger', 'Failed to save webhook: ' + (xhr.responseJSON?.message || 'Unknown error'), 'webhookAlert');
                }
            }
        });
    });

    // Load webhooks when webhook tab is shown
    $('a[href="#webhookConfig"]').on('shown.bs.tab', function() {
        loadWebhooks();
    });

    // Also load webhooks when webhook tab is clicked
    $('a[href="#webhookConfig"]').on('click', function() {
        loadWebhooks();
    });

    // Load webhooks initially if webhook tab is active on page load
    if ($('#webhookConfig').hasClass('active') || $('#webhookConfig').hasClass('show')) {
        loadWebhooks();
    }

    // Helper function for showing alerts in webhook tab
    function showAlert(type, message, containerId) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $(`#${containerId}`).remove();
        
        // Add new alert
        const container = $('<div>').attr('id', containerId);
        container.html(alertHtml);
        $('#webhookConfig .settings-section').prepend(container);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $(`#${containerId} .alert`).alert('close');
        }, 5000);
    }
});

</script>
@endpush
