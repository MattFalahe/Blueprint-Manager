@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::common.blueprint_library'))
@section('page_header', trans('blueprint-manager::common.blueprint_library'))

@push('head')
<style>
    /* Dark theme compatible styles */
    .library-filters {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }
    
    .filter-item {
        flex: 1;
        min-width: 200px;
    }
    
    /* Blueprint type badges */
    .badge-bpo {
        background-color: rgba(23, 162, 184, 0.2);
        color: #5dade2;
        border: 1px solid rgba(23, 162, 184, 0.3);
    }
    
    .badge-bpc {
        background-color: rgba(255, 193, 7, 0.2);
        color: #ffd43b;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    /* Statistics styling */
    .stats-group {
        display: flex;
        gap: 0.5rem;
        font-size: 0.875rem;
        flex-wrap: wrap;
    }
    
    .stat-badge {
        padding: 0.25rem 0.5rem;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.25rem;
        white-space: nowrap;
    }
    
    .stat-badge i {
        margin-right: 0.25rem;
        opacity: 0.7;
    }
    
    /* Research indicators */
    .research-indicator {
        display: inline-block;
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
        margin-left: 0.25rem;
        background-color: rgba(40, 167, 69, 0.2);
        color: #51cf66;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    
    .research-indicator i {
        margin-right: 0.25rem;
    }
    
    /* Category badge */
    .category-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        background: rgba(108, 117, 125, 0.2);
        border: 1px solid rgba(108, 117, 125, 0.3);
        color: #adb5bd;
    }
    
    /* Info banner */
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
    
    /* Details modal styling */
    .location-group {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .location-header {
        font-weight: 600;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .blueprint-item {
        padding: 0.5rem;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 0.25rem;
        margin-bottom: 0.5rem;
    }
    
    .blueprint-item:last-child {
        margin-bottom: 0;
    }
    
    /* Progress bar for research */
    .research-progress {
        height: 20px;
        margin-top: 0.25rem;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    .research-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #28a745 0%, #51cf66 100%);
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* Quantity badge */
    .quantity-badge {
        background: rgba(108, 117, 125, 0.2);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    /* Clickable row */
    .clickable-row {
        cursor: pointer;
    }
    
    .clickable-row:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }
</style>
@endpush

@section('full')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            {{-- Info Banner --}}
            <div class="info-banner">
                <i class="fas fa-info-circle"></i>
                <strong>About Blueprint Library:</strong>
                View your corporation's blueprints organized by the categories you configured in Settings. 
                Click on any blueprint to see detailed information including locations and research status.
            </div>

            @if($corporations->isEmpty())
            <div class="warning-banner">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No Blueprints Found:</strong>
                No corporations with blueprints were found, or container configurations haven't been set up yet.
                Go to <a href="{{ route('blueprint-manager.settings') }}">Settings</a> to configure blueprint containers.
            </div>
            @else

            {{-- Filters --}}
            <div class="library-filters">
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="corporationSelect">{{ trans('blueprint-manager::common.corporation') }}</label>
                        <select id="corporationSelect" class="form-control">
                            <option value="">-- {{ trans('blueprint-manager::common.select_corporation') }} --</option>
                            @foreach($corporations as $corp)
                            <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="categorySelect">{{ trans('blueprint-manager::common.category') }}</label>
                        <select id="categorySelect" class="form-control" disabled>
                            <option value="">-- {{ trans('blueprint-manager::common.all_categories') }} --</option>
                        </select>
                    </div>
                    <div class="filter-item" style="flex: 0;">
                        <button type="button" class="btn btn-primary" id="loadBlueprintsBtn" disabled>
                            <i class="fas fa-sync"></i> {{ trans('blueprint-manager::common.refresh') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Blueprints Table --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="blueprintsTable" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ trans('blueprint-manager::common.blueprint') }}</th>
                                    <th>{{ trans('blueprint-manager::common.category') }}</th>
                                    <th>{{ trans('blueprint-manager::common.type') }}</th>
                                    <th>{{ trans('blueprint-manager::common.quantity') }}</th>
                                    <th>{{ trans('blueprint-manager::common.me') }}</th>
                                    <th>{{ trans('blueprint-manager::common.te') }}</th>
                                    <th>{{ trans('blueprint-manager::common.runs') }}</th>
                                    <th>{{ trans('blueprint-manager::common.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> {{ trans('blueprint-manager::common.select_corporation') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @endif

        </div>
    </div>
</div>

{{-- Blueprint Details Modal --}}
<div class="modal fade" id="blueprintDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-list"></i>
                    <span id="modalBlueprintName">Blueprint Details</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="blueprintDetailsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>{{ trans('blueprint-manager::common.loading') }}</p>
                    </div>
                </div>
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
    let blueprintsTable;
    let selectedCorporationId = null;
    let selectedCategory = null;
    
    // Initialize DataTable (empty initially)
    blueprintsTable = $('#blueprintsTable').DataTable({
        data: [],
        columns: [
            { data: 'type_name' },
            { data: 'category' },
            { data: 'is_bpo' },
            { data: 'quantity' },
            { data: 'me_stats' },
            { data: 'te_stats' },
            { data: 'runs' },
            { data: 'research' }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: 2, // Type column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data ? '<span class="badge badge-bpo">BPO</span>' : '<span class="badge badge-bpc">BPC</span>';
                    }
                    return data ? 'BPO' : 'BPC';
                }
            },
            {
                targets: 1, // Category column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<span class="category-badge">' + data + '</span>';
                    }
                    return data;
                }
            },
            {
                targets: 3, // Quantity column
                render: function(data, type, row) {
                    if (type === 'display') {
                        let html = '<span class="quantity-badge">' + data + '</span>';
                        if (row.locations > 1) {
                            html += ' <small class="text-muted">(' + row.locations + ' locations)</small>';
                        }
                        return html;
                    }
                    return data;
                }
            },
            {
                targets: 4, // ME stats column
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (row.me_min === row.me_max) {
                            return '<span class="stat-badge"><i class="fas fa-cog"></i> ' + row.me_min + '</span>';
                        } else {
                            return '<span class="stat-badge"><i class="fas fa-cog"></i> ' + row.me_min + '-' + row.me_max + ' (avg: ' + row.me_avg + ')</span>';
                        }
                    }
                    return row.me_avg;
                }
            },
            {
                targets: 5, // TE stats column
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (row.te_min === row.te_max) {
                            return '<span class="stat-badge"><i class="fas fa-clock"></i> ' + row.te_min + '</span>';
                        } else {
                            return '<span class="stat-badge"><i class="fas fa-clock"></i> ' + row.te_min + '-' + row.te_max + ' (avg: ' + row.te_avg + ')</span>';
                        }
                    }
                    return row.te_avg;
                }
            },
            {
                targets: 6, // Runs column
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (row.is_bpo) {
                            return '<span class="text-muted">-</span>';
                        } else if (data && data.length > 0) {
                            return '<span class="stat-badge">' + data.join(', ') + '</span>';
                        } else {
                            return '<span class="text-muted">N/A</span>';
                        }
                    }
                    return data;
                }
            },
            {
                targets: 7, // Research column
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (row.has_research) {
                            return '<span class="research-indicator"><i class="fas fa-flask"></i> In Research</span>';
                        }
                        return '<span class="text-muted">-</span>';
                    }
                    return row.has_research ? 'In Research' : '';
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('clickable-row');
            $(row).attr('data-type-id', data.type_id);
        }
    });

    // Corporation selector change
    $('#corporationSelect').on('change', function() {
        selectedCorporationId = $(this).val();
        
        if (selectedCorporationId) {
            $('#categorySelect').prop('disabled', false);
            $('#loadBlueprintsBtn').prop('disabled', false);
            loadCategories();
            loadBlueprints();
        } else {
            $('#categorySelect').prop('disabled', true).html('<option value="">-- {{ trans('blueprint-manager::common.all_categories') }} --</option>');
            $('#loadBlueprintsBtn').prop('disabled', true);
            blueprintsTable.clear().draw();
            updateTableMessage('{{ trans('blueprint-manager::common.select_corporation') }}');
        }
    });

    // Category selector change
    $('#categorySelect').on('change', function() {
        selectedCategory = $(this).val();
        if (selectedCorporationId) {
            loadBlueprints();
        }
    });

    // Refresh button
    $('#loadBlueprintsBtn').on('click', function() {
        if (selectedCorporationId) {
            loadBlueprints();
        }
    });

    // Load categories for selected corporation
    function loadCategories() {
        $.ajax({
            url: '{{ route('blueprint-manager.library.categories', '') }}/' + selectedCorporationId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let html = '<option value="">-- {{ trans('blueprint-manager::common.all_categories') }} --</option>';
                    $.each(response.categories, function(category, count) {
                        html += '<option value="' + category + '">' + category + ' (' + count + ')</option>';
                    });
                    $('#categorySelect').html(html);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load categories');
            }
        });
    }

    // Load blueprints
    function loadBlueprints() {
        const btn = $('#loadBlueprintsBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ trans('blueprint-manager::common.loading') }}');
        
        updateTableMessage('{{ trans('blueprint-manager::common.loading') }}');

        $.ajax({
            url: '{{ route('blueprint-manager.library.data', '') }}/' + selectedCorporationId,
            method: 'GET',
            data: { category: selectedCategory || '' },
            success: function(response) {
                if (response.success) {
                    blueprintsTable.clear();
                    if (response.data.length > 0) {
                        blueprintsTable.rows.add(response.data).draw();
                    } else {
                        updateTableMessage('{{ trans('blueprint-manager::common.no_blueprints') }}');
                    }
                } else {
                    showAlert('danger', response.message);
                    updateTableMessage('Error loading blueprints');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to load blueprints';
                showAlert('danger', errorMsg);
                updateTableMessage('Error loading blueprints');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync"></i> {{ trans('blueprint-manager::common.refresh') }}');
            }
        });
    }

    // Update table message
    function updateTableMessage(message) {
        blueprintsTable.clear().draw();
        $('#blueprintsTable tbody').html(
            '<tr><td colspan="8" class="text-center text-muted"><i class="fas fa-info-circle"></i> ' + message + '</td></tr>'
        );
    }

    // Row click - show details
    $('#blueprintsTable tbody').on('click', 'tr.clickable-row', function() {
        const typeId = $(this).data('type-id');
        const typeName = $(this).find('td:first').text();
        
        if (typeId && selectedCorporationId) {
            showBlueprintDetails(typeId, typeName);
        }
    });

    // Show blueprint details modal
    function showBlueprintDetails(typeId, typeName) {
        $('#modalBlueprintName').text(typeName);
        $('#blueprintDetailsContent').html(
            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>{{ trans('blueprint-manager::common.loading') }}</p></div>'
        );
        $('#blueprintDetailsModal').modal('show');

        $.ajax({
            url: '{{ route('blueprint-manager.library.details', ['', '']) }}/' + selectedCorporationId + '/' + typeId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    renderBlueprintDetails(response);
                } else {
                    $('#blueprintDetailsContent').html(
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>'
                    );
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to load details';
                $('#blueprintDetailsContent').html(
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</div>'
                );
            }
        });
    }

    // Render blueprint details
    function renderBlueprintDetails(data) {
        let html = '';

        // Research jobs section
        if (data.research_jobs && data.research_jobs.length > 0) {
            html += '<div class="location-group">';
            html += '<div class="location-header"><i class="fas fa-flask"></i> Active Research Jobs</div>';
            data.research_jobs.forEach(function(job) {
                html += '<div class="blueprint-item">';
                html += '<strong>' + job.activity + ':</strong> ' + job.description + '<br>';
                html += '<small>Time Remaining: ' + job.time_remaining + '</small>';
                html += '<div class="research-progress">';
                html += '<div class="research-progress-bar" style="width: ' + job.progress + '%">' + job.progress + '%</div>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        }

        // Locations section
        html += '<h6>Locations (' + data.locations.length + ')</h6>';
        data.locations.forEach(function(location) {
            html += '<div class="location-group">';
            html += '<div class="location-header">';
            html += '<i class="fas fa-box"></i> ' + location.container_name;
            html += ' <span class="badge badge-secondary ml-2">' + location.quantity + ' blueprints</span>';
            html += '</div>';
            
            location.blueprints.forEach(function(bp) {
                html += '<div class="blueprint-item">';
                html += '<div class="stats-group">';
                html += '<span class="stat-badge"><i class="fas fa-cog"></i> ME: ' + bp.material_efficiency + '</span>';
                html += '<span class="stat-badge"><i class="fas fa-clock"></i> TE: ' + bp.time_efficiency + '</span>';
                if (bp.runs !== -1) {
                    html += '<span class="stat-badge"><i class="fas fa-redo"></i> Runs: ' + bp.runs + '</span>';
                }
                html += '<span class="stat-badge"><i class="fas fa-layer-group"></i> Qty: ' + bp.quantity + '</span>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        });

        $('#blueprintDetailsContent').html(html);
    }

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
