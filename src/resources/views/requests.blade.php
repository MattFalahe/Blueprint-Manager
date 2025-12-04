@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::common.requests'))
@section('page_header', trans('blueprint-manager::common.requests'))

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/blueprint-manager/css/blueprint-manager.css') }}">
@endpush



@section('full')
<div class="blueprint-manager-wrapper">

<!-- Dedicated Alert Container -->
<div id="alertContainer" style="position: fixed; top: 60px; right: 20px; z-index: 9999; width: 400px; max-width: 90vw;"></div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            {{-- Info Banner --}}
            <div class="info-banner">
                <i class="fas fa-info-circle"></i>
                <strong>About Blueprint Requests:</strong>
                Request blueprints from your corporation's library. Managers can approve, reject, or fulfill requests.
            </div>

            @if($corporations->isEmpty())
            <div class="warning-banner">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No Corporations Available:</strong>
                No corporations with blueprints were found. Contact your administrator.
            </div>
            @else

            {{-- Action Buttons --}}
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="newRequestBtn">
                    <i class="fas fa-plus"></i> {{ trans('blueprint-manager::common.request_blueprint') }}
                </button>
            </div>

            {{-- Tabs --}}
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#myRequests" role="tab">
                        <i class="fas fa-user"></i> {{ trans('blueprint-manager::common.my_requests') }}
                    </a>
                </li>
                @if($canManageRequests)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#manageRequests" role="tab">
                        <i class="fas fa-tasks"></i> {{ trans('blueprint-manager::common.pending_requests') }}
                    </a>
                </li>
                @endif
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- My Requests Tab --}}
                <div class="tab-pane fade show active" id="myRequests" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            {{-- Status Filter --}}
                            <div class="filter-section mb-3">
                                <label for="myRequestsStatusFilter">{{ trans('blueprint-manager::common.filter') }}:</label>
                                <select id="myRequestsStatusFilter" class="form-control" style="width: 200px; display: inline-block;">
                                    <option value="">{{ trans('blueprint-manager::common.all_status') }}</option>
                                    <option value="pending">{{ trans('blueprint-manager::common.pending') }}</option>
                                    <option value="approved">{{ trans('blueprint-manager::common.approved') }}</option>
                                    <option value="fulfilled">{{ trans('blueprint-manager::common.fulfilled') }}</option>
                                    <option value="rejected">{{ trans('blueprint-manager::common.rejected') }}</option>
                                </select>
                            </div>

                            <div class="table-responsive">
                                <table id="myRequestsTable" class="table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('blueprint-manager::common.corporation') }}</th>
                                            <th>{{ trans('blueprint-manager::common.blueprint') }}</th>
                                            <th>{{ trans('blueprint-manager::common.quantity') }}</th>
                                            <th>{{ trans('blueprint-manager::common.runs') }}</th>
                                            <th>{{ trans('blueprint-manager::common.status') }}</th>
                                            <th>{{ trans('blueprint-manager::common.requested_at') }}</th>
                                            <th>{{ trans('blueprint-manager::common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Manage Requests Tab --}}
                @if($canManageRequests)
                <div class="tab-pane fade" id="manageRequests" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            {{-- Status Filter --}}
                            <div class="filter-section mb-3">
                                <label for="manageRequestsStatusFilter">{{ trans('blueprint-manager::common.filter') }}:</label>
                                <select id="manageRequestsStatusFilter" class="form-control" style="width: 200px; display: inline-block;">
                                    <option value="">{{ trans('blueprint-manager::common.all_status') }}</option>
                                    <option value="pending" selected>{{ trans('blueprint-manager::common.pending') }}</option>
                                    <option value="approved">{{ trans('blueprint-manager::common.approved') }}</option>
                                    <option value="fulfilled">{{ trans('blueprint-manager::common.fulfilled') }}</option>
                                    <option value="rejected">{{ trans('blueprint-manager::common.rejected') }}</option>
                                </select>
                            </div>

                            <div class="table-responsive">
                                <table id="manageRequestsTable" class="table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('blueprint-manager::common.requested_by') }}</th>
                                            <th>{{ trans('blueprint-manager::common.corporation') }}</th>
                                            <th>{{ trans('blueprint-manager::common.blueprint') }}</th>
                                            <th>{{ trans('blueprint-manager::common.quantity') }}</th>
                                            <th>{{ trans('blueprint-manager::common.runs') }}</th>
                                            <th>{{ trans('blueprint-manager::common.status') }}</th>
                                            <th>{{ trans('blueprint-manager::common.requested_at') }}</th>
                                            <th>{{ trans('blueprint-manager::common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @endif

        </div>
    </div>
</div>

{{-- New Request Modal --}}
<div class="modal fade" id="newRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.request_blueprint') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="newRequestForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="request_corporation_id">{{ trans('blueprint-manager::common.corporation') }} *</label>
                        <select id="request_corporation_id" name="corporation_id" class="form-control" required>
                            <option value="">-- {{ trans('blueprint-manager::common.select_corporation') }} --</option>
                            @foreach($corporations as $corp)
                            <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="request_blueprint_type_id">{{ trans('blueprint-manager::common.blueprint') }} *</label>
                        <select id="request_blueprint_type_id" name="blueprint_type_id" class="form-control" required disabled>
                            <option value="">-- Select corporation first --</option>
                        </select>
                        <small class="form-text text-muted">Select a corporation to load available blueprints</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request_quantity">{{ trans('blueprint-manager::common.quantity') }} *</label>
                                <input type="number" id="request_quantity" name="quantity" class="form-control" value="1" min="1" max="1000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request_runs">{{ trans('blueprint-manager::common.runs') }}</label>
                                <input type="number" id="request_runs" name="runs" class="form-control" min="1" placeholder="For BPCs">
                                <small class="form-text text-muted">Leave empty for BPOs</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="request_notes">{{ trans('blueprint-manager::common.notes') }}</label>
                        <textarea id="request_notes" name="notes" class="form-control" rows="3" maxlength="1000" placeholder="Optional notes for your request"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('blueprint-manager::common.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Request Details Modal --}}
<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Request Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="requestDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.approve') }} Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveForm">
                @csrf
                <input type="hidden" id="approve_request_id">
                <div class="modal-body">
                    <p>Are you sure you want to approve this request?</p>
                    <p><strong>Blueprint:</strong> <span id="approve_blueprint_name"></span></p>
                    <p><strong>Quantity:</strong> <span id="approve_quantity"></span></p>
                    
                    <div class="form-group">
                        <label for="approve_notes">{{ trans('blueprint-manager::common.notes') }} (Optional)</label>
                        <textarea id="approve_notes" name="notes" class="form-control" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('blueprint-manager::common.approve') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.reject') }} Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm">
                @csrf
                <input type="hidden" id="reject_request_id">
                <div class="modal-body">
                    <p>Are you sure you want to reject this request?</p>
                    <p><strong>Blueprint:</strong> <span id="reject_blueprint_name"></span></p>
                    
                    <div class="form-group">
                        <label for="reject_notes">{{ trans('blueprint-manager::common.notes') }} *</label>
                        <textarea id="reject_notes" name="notes" class="form-control" rows="3" maxlength="1000" required placeholder="Please explain why this request is being rejected"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ trans('blueprint-manager::common.reject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Fulfill Modal --}}
<div class="modal fade" id="fulfillModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('blueprint-manager::common.fulfill') }} Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="fulfillForm">
                @csrf
                <input type="hidden" id="fulfill_request_id">
                <div class="modal-body">
                    <p>Mark this request as fulfilled?</p>
                    <p><strong>Blueprint:</strong> <span id="fulfill_blueprint_name"></span></p>
                    <p><strong>Quantity:</strong> <span id="fulfill_quantity"></span></p>
                    
                    <div class="form-group">
                        <label for="fulfill_notes">{{ trans('blueprint-manager::common.notes') }} (Optional)</label>
                        <textarea id="fulfill_notes" name="notes" class="form-control" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('blueprint-manager::common.fulfill') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_request_id">
                <div class="modal-body">
                    <div class="warning-banner">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                    <p>Are you sure you want to delete this request?</p>
                    <p><strong>Blueprint:</strong> <span id="delete_blueprint_name"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('blueprint-manager::common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">Delete Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
@endsection

@push('javascript')
<script src="{{ asset('vendor/blueprint-manager/js/select2.min.js') }}"></script>
<script>
$(document).ready(function() {
    let myRequestsTable;
    let manageRequestsTable;
    
    // Initialize My Requests DataTable
    myRequestsTable = $('#myRequestsTable').DataTable({
        data: [],
        columns: [
            { data: 'corporation_name' },
            { data: 'blueprint_name' },
            { data: 'quantity' },
            { data: 'runs' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'actions' }
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: 3, // Runs
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                targets: 4, // Status
                render: function(data, type, row) {
                    return getStatusBadge(data);
                }
            },
            {
                targets: 6, // Actions
                render: function(data, type, row) {
                    let html = '<div class="btn-group btn-group-sm">';
                    html += '<button class="btn btn-info btn-view-details" data-request-id="' + row.id + '"><i class="fas fa-eye"></i></button>';
                    
                    // Allow deleting own pending or rejected requests
                    if (row.is_own && (row.status === 'pending' || row.status === 'rejected')) {
                        html += '<button class="btn btn-danger btn-delete" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '"><i class="fas fa-trash"></i></button>';
                    }
                    
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    // Initialize Manage Requests DataTable (if user can manage)
    @if($canManageRequests)
    manageRequestsTable = $('#manageRequestsTable').DataTable({
        data: [],
        columns: [
            { data: 'character_name' },
            { data: 'corporation_name' },
            { data: 'blueprint_name' },
            { data: 'quantity' },
            { data: 'runs' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'actions' }
        ],
        order: [[6, 'desc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: 4, // Runs
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                targets: 5, // Status
                render: function(data, type, row) {
                    return getStatusBadge(data);
                }
            },
            {
                targets: 7, // Actions
                render: function(data, type, row) {
                    let html = '<div class="btn-group btn-group-sm">';
                    html += '<button class="btn btn-info btn-view-details" data-request-id="' + row.id + '"><i class="fas fa-eye"></i></button>';
                    
                    if (row.status === 'pending') {
                        html += '<button class="btn btn-success btn-approve" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '" data-quantity="' + row.quantity + '"><i class="fas fa-check"></i></button>';
                        html += '<button class="btn btn-danger btn-reject" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '"><i class="fas fa-times"></i></button>';
                        html += '<button class="btn btn-danger btn-delete" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '"><i class="fas fa-trash"></i></button>';
                    } else if (row.status === 'approved') {
                        html += '<button class="btn btn-success btn-fulfill" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '" data-quantity="' + row.quantity + '"><i class="fas fa-check-double"></i></button>';
                    } else if (row.status === 'rejected') {
                        html += '<button class="btn btn-danger btn-delete" data-request-id="' + row.id + '" data-blueprint-name="' + row.blueprint_name + '"><i class="fas fa-trash"></i></button>';
                    }
                    
                    html += '</div>';
                    return html;
                }
            }
        ]
    });
    
    // Load manage requests
    loadManageRequests('pending');
    @endif

    // Load my requests
    loadMyRequests('');

    // Status filter change - My Requests
    $('#myRequestsStatusFilter').on('change', function() {
        loadMyRequests($(this).val());
    });

    // Status filter change - Manage Requests
    $('#manageRequestsStatusFilter').on('change', function() {
        loadManageRequests($(this).val());
    });

    // Tab change - reload data
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target === '#myRequests') {
            loadMyRequests($('#myRequestsStatusFilter').val());
        } else if (target === '#manageRequests') {
            loadManageRequests($('#manageRequestsStatusFilter').val());
        }
    });

    // Load my requests
    function loadMyRequests(status) {
        $.ajax({
            url: '{{ route('blueprint-manager.requests.data') }}',
            method: 'GET',
            data: { view_type: 'my', status: status },
            success: function(response) {
                if (response.success) {
                    myRequestsTable.clear();
                    if (response.data.length > 0) {
                        myRequestsTable.rows.add(response.data).draw();
                    } else {
                        myRequestsTable.draw();
                    }
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to load requests');
            }
        });
    }

    // Load manage requests
    function loadManageRequests(status) {
        $.ajax({
            url: '{{ route('blueprint-manager.requests.data') }}',
            method: 'GET',
            data: { view_type: 'manage', status: status },
            success: function(response) {
                if (response.success) {
                    manageRequestsTable.clear();
                    if (response.data.length > 0) {
                        manageRequestsTable.rows.add(response.data).draw();
                    } else {
                        manageRequestsTable.draw();
                    }
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to load requests');
            }
        });
    }

    // Get status badge HTML
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="status-badge status-pending">Pending</span>',
            'approved': '<span class="status-badge status-approved">Approved</span>',
            'fulfilled': '<span class="status-badge status-fulfilled">Fulfilled</span>',
            'rejected': '<span class="status-badge status-rejected">Rejected</span>'
        };
        return badges[status] || status;
    }

    // New Request button
    $('#newRequestBtn').on('click', function() {
        $('#newRequestForm')[0].reset();
        $('#request_blueprint_type_id').prop('disabled', true).html('<option value="">-- Select corporation first --</option>');
        $('#newRequestModal').modal('show');
    });

    // Corporation change - load blueprints
    $('#request_corporation_id').on('change', function() {
        const corpId = $(this).val();
        const blueprintSelect = $('#request_blueprint_type_id');
        
        if (corpId) {
            blueprintSelect.prop('disabled', true).html('<option value="">Loading...</option>');
            
            $.ajax({
                url: '{{ route('blueprint-manager.requests.blueprints', '') }}/' + corpId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '<option value="">-- Select blueprint --</option>';
                        response.blueprints.forEach(function(bp) {
                            html += '<option value="' + bp.id + '">' + bp.text + '</option>';
                        });
                        blueprintSelect.html(html).prop('disabled', false);
                        
                        // Initialize Select2
                        blueprintSelect.select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#newRequestModal'),
                            placeholder: 'Search blueprints...',
                            width: '100%'
                        });
                    }
                },
                error: function() {
                    blueprintSelect.html('<option value="">Failed to load</option>');
                }
            });
        } else {
            blueprintSelect.prop('disabled', true).html('<option value="">-- Select corporation first --</option>');
        }
    });

    // Submit new request
    $('#newRequestForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            corporation_id: $('#request_corporation_id').val(),
            blueprint_type_id: $('#request_blueprint_type_id').val(),
            quantity: $('#request_quantity').val(),
            runs: $('#request_runs').val(),
            notes: $('#request_notes').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route('blueprint-manager.requests.create') }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#newRequestModal').modal('hide');
                    loadMyRequests($('#myRequestsStatusFilter').val());
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to submit request';
                showAlert('danger', errorMsg);
            }
        });
    });

    // View details button
    $(document).on('click', '.btn-view-details', function() {
        const requestId = $(this).data('request-id');
        showRequestDetails(requestId);
    });

    // Show request details
    function showRequestDetails(requestId) {
        // Find request in table data
        let requestData = null;
        
        // Check my requests table
        const myData = myRequestsTable.rows().data();
        for (let i = 0; i < myData.length; i++) {
            if (myData[i].id === requestId) {
                requestData = myData[i];
                break;
            }
        }
        
        // Check manage requests table if not found
        @if($canManageRequests)
        if (!requestData) {
            const manageData = manageRequestsTable.rows().data();
            for (let i = 0; i < manageData.length; i++) {
                if (manageData[i].id === requestId) {
                    requestData = manageData[i];
                    break;
                }
            }
        }
        @endif
        
        if (requestData) {
            let html = '<div class="request-details">';
            html += '<div class="detail-row"><div class="detail-label">Corporation:</div><div class="detail-value">' + requestData.corporation_name + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Blueprint:</div><div class="detail-value">' + requestData.blueprint_name + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Requested By:</div><div class="detail-value">' + requestData.character_name + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Quantity:</div><div class="detail-value">' + requestData.quantity + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Runs:</div><div class="detail-value">' + (requestData.runs || '-') + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Status:</div><div class="detail-value">' + getStatusBadge(requestData.status) + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Requested At:</div><div class="detail-value">' + requestData.created_at + '</div></div>';
            
            if (requestData.notes) {
                html += '<div class="detail-row"><div class="detail-label">Notes:</div><div class="detail-value">' + requestData.notes + '</div></div>';
            }
            
            if (requestData.approved_by) {
                html += '<div class="detail-row"><div class="detail-label">Approved By:</div><div class="detail-value">' + requestData.approved_by + '</div></div>';
                html += '<div class="detail-row"><div class="detail-label">Approved At:</div><div class="detail-value">' + requestData.approved_at + '</div></div>';
            }
            
            if (requestData.fulfilled_by) {
                html += '<div class="detail-row"><div class="detail-label">Fulfilled By:</div><div class="detail-value">' + requestData.fulfilled_by + '</div></div>';
                html += '<div class="detail-row"><div class="detail-label">Fulfilled At:</div><div class="detail-value">' + requestData.fulfilled_at + '</div></div>';
            }
            
            if (requestData.response_notes) {
                html += '<div class="detail-row"><div class="detail-label">Response Notes:</div><div class="detail-value">' + requestData.response_notes + '</div></div>';
            }
            
            html += '</div>';
            
            $('#requestDetailsContent').html(html);
            $('#requestDetailsModal').modal('show');
        }
    }

    // Approve button
    $(document).on('click', '.btn-approve', function() {
        const requestId = $(this).data('request-id');
        const blueprintName = $(this).data('blueprint-name');
        const quantity = $(this).data('quantity');
        
        $('#approve_request_id').val(requestId);
        $('#approve_blueprint_name').text(blueprintName);
        $('#approve_quantity').text(quantity);
        $('#approve_notes').val('');
        $('#approveModal').modal('show');
    });

    // Approve form submit
    $('#approveForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#approve_request_id').val();
        const notes = $('#approve_notes').val();
        
        // Debug logging
        console.log('Approving request ID:', requestId);
        
        $.ajax({
            url: '{{ route('blueprint-manager.requests.approve', ':id') }}'.replace(':id', requestId),
            method: 'POST',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Approve response:', response);
                if (response.success) {
                    showAlert('success', response.message);
                    $('#approveModal').modal('hide');
                    loadManageRequests($('#manageRequestsStatusFilter').val());
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                console.error('Approve error:', xhr);
                console.error('Response:', xhr.responseJSON);
                const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Failed to approve request';
                showAlert('danger', 'Failed to approve: ' + errorMsg);
            }
        });
    });

    // Reject button
    $(document).on('click', '.btn-reject', function() {
        const requestId = $(this).data('request-id');
        const blueprintName = $(this).data('blueprint-name');
        
        $('#reject_request_id').val(requestId);
        $('#reject_blueprint_name').text(blueprintName);
        $('#reject_notes').val('');
        $('#rejectModal').modal('show');
    });

    // Reject form submit
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#reject_request_id').val();
        const notes = $('#reject_notes').val();
        
        if (!notes || notes.trim() === '') {
            showAlert('warning', 'Please provide a reason for rejection');
            return;
        }
        
        // Debug logging
        console.log('Rejecting request ID:', requestId);
        
        $.ajax({
            url: '{{ route('blueprint-manager.requests.reject', ':id') }}'.replace(':id', requestId),
            method: 'POST',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Reject response:', response);
                if (response.success) {
                    showAlert('success', response.message);
                    $('#rejectModal').modal('hide');
                    loadManageRequests($('#manageRequestsStatusFilter').val());
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                console.error('Reject error:', xhr);
                console.error('Response:', xhr.responseJSON);
                const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Failed to reject request';
                showAlert('danger', 'Failed to reject: ' + errorMsg);
            }
        });
    });

    // Fulfill button
    $(document).on('click', '.btn-fulfill', function() {
        const requestId = $(this).data('request-id');
        const blueprintName = $(this).data('blueprint-name');
        const quantity = $(this).data('quantity');
        
        $('#fulfill_request_id').val(requestId);
        $('#fulfill_blueprint_name').text(blueprintName);
        $('#fulfill_quantity').text(quantity);
        $('#fulfill_notes').val('');
        $('#fulfillModal').modal('show');
    });

    // Fulfill form submit
    $('#fulfillForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#fulfill_request_id').val();
        const notes = $('#fulfill_notes').val();
        
        // Debug logging
        console.log('Fulfilling request ID:', requestId);
        
        $.ajax({
            url: '{{ route('blueprint-manager.requests.fulfill', ':id') }}'.replace(':id', requestId),
            method: 'POST',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Fulfill response:', response);
                if (response.success) {
                    showAlert('success', response.message);
                    $('#fulfillModal').modal('hide');
                    loadManageRequests($('#manageRequestsStatusFilter').val());
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                console.error('Fulfill error:', xhr);
                console.error('Response:', xhr.responseJSON);
                const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Failed to fulfill request';
                showAlert('danger', 'Failed to fulfill: ' + errorMsg);
            }
        });
    });

    // Delete button click handler
    $(document).on('click', '.btn-delete', function() {
        const requestId = $(this).data('request-id');
        const blueprintName = $(this).data('blueprint-name');
        
        $('#delete_request_id').val(requestId);
        $('#delete_blueprint_name').text(blueprintName);
        $('#deleteModal').modal('show');
    });

    // Delete form submit handler
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        
        const requestId = $('#delete_request_id').val();
        
        $.ajax({
            url: '{{ route('blueprint-manager.requests.delete', '') }}/' + requestId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#deleteModal').modal('hide');
                    
                    // Reload the appropriate table
                    loadMyRequests($('#myRequestsStatusFilter').val());
                    @if($canManageRequests)
                    loadManageRequests($('#manageRequestsStatusFilter').val());
                    @endif
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to delete request';
                showAlert('danger', errorMsg);
            }
        });
    });

    // Helper function to show alerts (FIXED - proper wrapping and display)
    function showAlert(type, message) {
        // Remove any existing alerts
        $('#alertContainer .alert').remove();
        
        const iconMap = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        
        const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas fa-' + (iconMap[type] || 'info-circle') + '"></i>' +
            '<span>' + message + '</span>' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');
        
        $('#alertContainer').append(alert);
        
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
