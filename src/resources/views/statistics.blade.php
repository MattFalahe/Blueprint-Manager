@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::menu.statistics'))
@section('page_header', trans('blueprint-manager::menu.statistics'))

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/blueprint-manager/css/blueprint-manager.css') }}">
@endpush



@section('full')
<div class="blueprint-manager-wrapper">
<div class="container-fluid">
    
    {{-- Error Message --}}
    @if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Error:</strong> {{ session('error') ?? $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    {{-- No Corporations Warning --}}
    @if($corporations->isEmpty())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <strong>No Corporations Found:</strong> No corporations are available or you don't have access to any corporations.
        Please contact your administrator.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Filter Controls -->
    <div class="filter-controls">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="corporationFilter" class="form-label">Filter by Corporation</label>
                <select id="corporationFilter" class="form-select">
                    <option value="">All Corporations</option>
                    @foreach($corporations as $corp)
                        <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="timeFilter" class="form-label">Time Period</label>
                <select id="timeFilter" class="form-select">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="0">All Time</option>
                </select>
            </div>
            <div class="col-md-4">
                <button id="refreshStats" class="btn btn-info w-100">
                    <i class="fas fa-sync-alt"></i> Refresh Statistics
                </button>
            </div>
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <h3>Total Requests</h3>
                <div class="stat-value" id="totalRequests">-</div>
                <div class="stat-label">All time requests</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>Unique Requesters</h3>
                <div class="stat-value" id="uniqueRequesters">-</div>
                <div class="stat-label">Different characters</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>Last 7 Days</h3>
                <div class="stat-value" id="last7Days">-</div>
                <div class="stat-label">Recent activity</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>Last 30 Days</h3>
                <div class="stat-value" id="last30Days">-</div>
                <div class="stat-label">Monthly activity</div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row">
        <div class="col-md-3">
            <div class="mini-stat pending">
                <div class="mini-stat-value" id="pendingCount">-</div>
                <div class="mini-stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat approved">
                <div class="mini-stat-value" id="approvedCount">-</div>
                <div class="mini-stat-label">Approved</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat fulfilled">
                <div class="mini-stat-value" id="fulfilledCount">-</div>
                <div class="mini-stat-label">Fulfilled</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-stat rejected">
                <div class="mini-stat-value" id="rejectedCount">-</div>
                <div class="mini-stat-label">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Character Statistics Section -->
    <h2 class="section-header">
        <i class="fas fa-users"></i> Character Request Statistics
    </h2>
    <div class="stats-table">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Character</th>
                    <th class="text-center">Total Requests</th>
                    <th class="text-center">Total Quantity</th>
                    <th class="text-center">Fulfilled</th>
                    <th class="text-center">Rejected</th>
                    <th class="text-center">Rejection Rate</th>
                    <th class="text-center">Req/Day</th>
                    <th>Last Request</th>
                    <th>Indicators</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="characterStatsBody">
                <tr>
                    <td colspan="10" class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mt-3">Loading character statistics...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Blueprint Popularity Section -->
    <h2 class="section-header">
        <i class="fas fa-chart-bar"></i> Most Requested Blueprints
    </h2>
    <div class="stats-table">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Blueprint</th>
                    <th class="text-center">Request Count</th>
                    <th class="text-center">Total Quantity</th>
                    <th class="text-center">Unique Requesters</th>
                    <th class="text-center">Fulfilled</th>
                    <th class="text-center">Rejected</th>
                    <th>Last Requested</th>
                </tr>
            </thead>
            <tbody id="blueprintStatsBody">
                <tr>
                    <td colspan="7" class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mt-3">Loading blueprint statistics...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- Character Detail Modal -->
<div class="modal fade" id="characterDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="characterDetailModalTitle">Character Request History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="stats-table">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Blueprint</th>
                                <th>Corporation</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Runs</th>
                                <th class="text-center">Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="characterDetailBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('javascript')
<script>
$(document).ready(function() {
    
    // Load all statistics
    function loadStatistics() {
        loadOverallStats();
        loadCharacterStats();
        loadBlueprintStats();
    }
    
    // Load overall statistics
    function loadOverallStats() {
        console.log('Blueprint Manager: Loading overall statistics...');
        $.ajax({
            url: '{{ route("blueprint-manager.statistics.overall") }}',
            method: 'GET',
            success: function(data) {
                console.log('Blueprint Manager: Overall stats loaded successfully', data);
                $('#totalRequests').text(data.total_requests.toLocaleString());
                $('#uniqueRequesters').text(data.unique_requesters.toLocaleString());
                $('#last7Days').text(data.last_7_days.toLocaleString());
                $('#last30Days').text(data.last_30_days.toLocaleString());
                
                $('#pendingCount').text(data.pending.toLocaleString());
                $('#approvedCount').text(data.approved.toLocaleString());
                $('#fulfilledCount').text(data.fulfilled.toLocaleString());
                $('#rejectedCount').text(data.rejected.toLocaleString());
            },
            error: function(xhr, status, error) {
                console.error('Blueprint Manager: Failed to load overall statistics');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let errorMessage = 'Failed to load overall statistics';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ': ' + xhr.responseJSON.message;
                }
                alert(errorMessage + '\n\nCheck browser console (F12) for details.');
            }
        });
    }
    
    // Load character statistics
    function loadCharacterStats() {
        console.log('Blueprint Manager: Loading character statistics...');
        $.ajax({
            url: '{{ route("blueprint-manager.statistics.characters") }}',
            method: 'GET',
            success: function(data) {
                console.log('Blueprint Manager: Character stats loaded successfully', data);
                const tbody = $('#characterStatsBody');
                tbody.empty();
                
                if (data.length === 0) {
                    tbody.append('<tr><td colspan="10" class="text-center text-muted py-4">No character statistics available</td></tr>');
                    return;
                }
                
                data.forEach(function(char) {
                    const indicators = char.abuse_indicators.map(function(indicator) {
                        return `<span class="abuse-indicator"><i class="fas fa-exclamation-triangle"></i> ${indicator}</span>`;
                    }).join('');
                    
                    const indicatorCell = indicators || '<span class="text-muted">None</span>';
                    
                    const row = `
                        <tr>
                            <td>
                                <a href="#" class="character-link" data-character-id="${char.character_id}" data-character-name="${char.character_name}">
                                    ${char.character_name}
                                </a>
                            </td>
                            <td class="text-center">${char.total_requests}</td>
                            <td class="text-center">${char.total_quantity.toLocaleString()}</td>
                            <td class="text-center text-success">${char.fulfilled_count}</td>
                            <td class="text-center text-danger">${char.rejected_count}</td>
                            <td class="text-center">
                                <span class="${char.rejection_rate > 30 ? 'text-danger' : char.rejection_rate > 15 ? 'text-warning' : 'text-success'}">
                                    ${char.rejection_rate}%
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="${char.requests_per_day > 3 ? 'text-warning' : ''}">
                                    ${char.requests_per_day}
                                </span>
                            </td>
                            <td>${new Date(char.last_request).toLocaleDateString()}</td>
                            <td>${indicatorCell}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info view-character-details" data-character-id="${char.character_id}" data-character-name="${char.character_name}">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Blueprint Manager: Failed to load character statistics');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let errorMessage = 'Failed to load character statistics';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ': ' + xhr.responseJSON.message;
                    console.error('Error details:', xhr.responseJSON);
                }
                
                alert(errorMessage + '\n\nCheck browser console (F12) for details.');
                
                // Show error in table
                const tbody = $('#characterStatsBody');
                tbody.empty();
                tbody.append(`
                    <tr>
                        <td colspan="10" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                            <br><small>Check browser console and Laravel logs for details</small>
                        </td>
                    </tr>
                `);
            }
        });
    }
    
    // Load blueprint statistics
    function loadBlueprintStats() {
        console.log('Blueprint Manager: Loading blueprint statistics...');
        $.ajax({
            url: '{{ route("blueprint-manager.statistics.blueprints") }}',
            method: 'GET',
            success: function(data) {
                console.log('Blueprint Manager: Blueprint stats loaded successfully', data);
                const tbody = $('#blueprintStatsBody');
                tbody.empty();
                
                if (data.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted py-4">No blueprint statistics available</td></tr>');
                    return;
                }
                
                data.forEach(function(bp) {
                    const row = `
                        <tr>
                            <td>${bp.blueprint_name}</td>
                            <td class="text-center">${bp.request_count}</td>
                            <td class="text-center">${bp.total_quantity.toLocaleString()}</td>
                            <td class="text-center">${bp.unique_requesters}</td>
                            <td class="text-center text-success">${bp.fulfilled_count}</td>
                            <td class="text-center text-danger">${bp.rejected_count}</td>
                            <td>${new Date(bp.last_requested).toLocaleDateString()}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Blueprint Manager: Failed to load blueprint statistics');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let errorMessage = 'Failed to load blueprint statistics';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ': ' + xhr.responseJSON.message;
                }
                alert(errorMessage + '\n\nCheck browser console (F12) for details.');
            }
        });
    }
    
    // View character details
    $(document).on('click', '.view-character-details, .character-link', function(e) {
        e.preventDefault();
        const characterId = $(this).data('character-id');
        const characterName = $(this).data('character-name');
        
        $('#characterDetailModalTitle').text(`Request History - ${characterName}`);
        $('#characterDetailBody').html('<tr><td colspan="7" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><p class="mt-3">Loading...</p></td></tr>');
        $('#characterDetailModal').modal('show');
        
        $.ajax({
            url: '{{ route("blueprint-manager.statistics.character-details", ":id") }}'.replace(':id', characterId),
            method: 'GET',
            success: function(data) {
                const tbody = $('#characterDetailBody');
                tbody.empty();
                
                if (data.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted py-4">No requests found</td></tr>');
                    return;
                }
                
                data.forEach(function(req) {
                    const statusBadge = `<span class="status-badge status-${req.status}">${req.status.toUpperCase()}</span>`;
                    const row = `
                        <tr>
                            <td>${new Date(req.created_at).toLocaleDateString()}</td>
                            <td>${req.blueprint_type ? req.blueprint_type.typeName : 'Unknown'}</td>
                            <td>${req.corporation ? req.corporation.name : 'Unknown'}</td>
                            <td class="text-center">${req.quantity}</td>
                            <td class="text-center">${req.runs || '-'}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td>${req.notes || '-'}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function() {
                $('#characterDetailBody').html('<tr><td colspan="7" class="text-center text-danger py-4">Failed to load details</td></tr>');
            }
        });
    });
    
    // Refresh button
    $('#refreshStats').on('click', function() {
        loadStatistics();
    });
    
    // Initial load
    loadStatistics();
    
});
</script>
@endpush
