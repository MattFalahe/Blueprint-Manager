@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::common.settings'))
@section('page_header', trans('blueprint-manager::common.settings'))

@push('head')
<style>
    /* Dark theme compatible styles */
    .settings-section {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .settings-section h4 {
        color: #17a2b8;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .info-banner {
        background: rgba(23, 162, 184, 0.1);
        border-left: 4px solid #17a2b8;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-radius: 0.25rem;
    }
    
    .warning-banner {
        background: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-radius: 0.25rem;
    }
    
    .badge-priority {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .match-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .status-enabled {
        color: #28a745;
    }
    
    .status-disabled {
        color: #dc3545;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('full')

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

            {{-- Container Configuration Section --}}
            <div class="settings-section">
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
                </div>
            </div>

        </div>
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

@endsection

@push('javascript')
<script>
$(document).ready(function() {
    let configurationsTable;
    let selectedCorporationId = null;
    
    // Initialize DataTable
    configurationsTable = $('#configurationsTable').DataTable({
        order: [[4, 'desc'], [2, 'asc']], // Sort by priority desc, then category asc
        pageLength: 25,
        responsive: true
    });

    // Corporation selector change
    $('#corporationSelect').on('change', function() {
        selectedCorporationId = $(this).val();
        
        // Enable/disable detect button
        $('#detectContainersBtn').prop('disabled', !selectedCorporationId);
        
        // Filter table
        if (selectedCorporationId) {
            configurationsTable.column(0).search('^' + $('option:selected', this).text() + '$', true, false).draw();
        } else {
            configurationsTable.column(0).search('').draw();
        }
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

    // Display detected containers
    function displayDetectedContainers(data) {
        let html = '<div class="alert alert-info">';
        html += '<strong><i class="fas fa-info-circle"></i> Detection Results:</strong><br>';
        html += 'Total containers with blueprints: ' + data.total_found + '<br>';
        html += 'Already configured: ' + data.already_configured + '<br>';
        html += 'New containers found: ' + data.new_containers;
        html += '</div>';

        if (data.containers.length > 0) {
            html += '<h6>New Containers:</h6>';
            html += '<ul class="list-group">';
            data.containers.forEach(function(container) {
                html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                html += '<code>' + container + '</code>';
                html += '<button class="btn btn-sm btn-primary quick-add-config" data-container="' + container + '">';
                html += '<i class="fas fa-plus"></i> Add Config';
                html += '</button>';
                html += '</li>';
            });
            html += '</ul>';
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
});
</script>
@endpush
