@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::help.help_documentation'))
@section('page_header', trans('blueprint-manager::help.help_documentation'))

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/blueprint-manager/css/blueprint-manager.css') }}?v=3">
<style>
    /* "What's new" — green-accented, list form. Scoped + !important so a
       custom SeAT theme can't wash out the green or the text contrast. */
    .blueprint-manager-wrapper .whats-new-card { border-left: 4px solid #28a745 !important; }
    .blueprint-manager-wrapper .whats-new-card h3 { color: #28a745 !important; }
    .blueprint-manager-wrapper .whats-new-list { list-style: none; padding-left: 0; margin: 14px 0 0 0; }
    .blueprint-manager-wrapper .whats-new-list li {
        position: relative; padding: 9px 0 9px 30px; line-height: 1.5;
        color: #d1d5db !important; border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .blueprint-manager-wrapper .whats-new-list li:last-child { border-bottom: none; }
    .blueprint-manager-wrapper .whats-new-list li > i { position: absolute; left: 0; top: 11px; color: #28a745 !important; }
    .blueprint-manager-wrapper .whats-new-list li strong { color: #e2e8f0 !important; }
    .blueprint-manager-wrapper .whats-new-list li code { color: #fbbf24 !important; }
</style>
@endpush

@section('full')
<div class="blueprint-manager-wrapper">
<div class="help-wrapper">
    <div class="help-sidebar">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-compass"></i>
                    Navigation
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="search-box">
                    <input type="text" id="helpSearch" placeholder="{{ trans('blueprint-manager::help.search_placeholder') }}">
                    <i class="fas fa-search"></i>
                </div>
                <ul class="nav nav-pills flex-column help-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-section="overview"><i class="fas fa-home"></i> {{ trans('blueprint-manager::help.overview') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="getting-started"><i class="fas fa-rocket"></i> {{ trans('blueprint-manager::help.getting_started') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="features"><i class="fas fa-star"></i> {{ trans('blueprint-manager::help.features') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="library"><i class="fas fa-book"></i> {{ trans('blueprint-manager::help.library') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="requests"><i class="fas fa-paper-plane"></i> {{ trans('blueprint-manager::help.requests') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="statistics"><i class="fas fa-chart-line"></i> {{ trans('blueprint-manager::help.statistics') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="settings"><i class="fas fa-cog"></i> {{ trans('blueprint-manager::help.settings') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="permissions"><i class="fas fa-shield-alt"></i> {{ trans('blueprint-manager::help.permissions') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="faq"><i class="fas fa-question-circle"></i> {{ trans('blueprint-manager::help.faq') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="troubleshooting"><i class="fas fa-wrench"></i> {{ trans('blueprint-manager::help.troubleshooting') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="help-content">
        {{-- Overview --}}
        <div id="overview" class="help-section active">
            <div class="plugin-info">
                <h3 style="color: #17a2b8; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> {{ trans('blueprint-manager::help.plugin_info_title') }}</h3>
                <div class="info-row"><strong>{{ trans('blueprint-manager::help.version') }}:</strong> <img src="https://img.shields.io/packagist/v/mattfalahe/blueprint-manager?label=release&color=667eea" alt="Latest release" style="vertical-align: middle;"> <img src="https://img.shields.io/badge/SeAT-5.x-blue" alt="SeAT" style="vertical-align: middle;"></div>
                <div class="info-row"><strong>{{ trans('blueprint-manager::help.license') }}:</strong> GPL-2.0</div>
                
                <div class="author">
                    <i class="fas fa-user"></i> <strong>{{ trans('blueprint-manager::help.author') }}:</strong> Matt Falahe
                    <br>
                    <i class="fas fa-envelope"></i> <a href="mailto:mattfalahe@gmail.com" style="color: #17a2b8;">mattfalahe@gmail.com</a>
                </div>

                <div class="plugin-links">
                    <a href="https://github.com/MattFalahe/blueprint-manager" target="_blank" class="plugin-link"><i class="fab fa-github"></i><span>{{ trans('blueprint-manager::help.github_repo') }}</span></a>
                    <a href="https://github.com/MattFalahe/blueprint-manager/blob/main/CHANGELOG.MD" target="_blank" class="plugin-link"><i class="fas fa-list"></i><span>{{ trans('blueprint-manager::help.changelog') }}</span></a>
                    <a href="https://github.com/MattFalahe/blueprint-manager/issues" target="_blank" class="plugin-link"><i class="fas fa-bug"></i><span>{{ trans('blueprint-manager::help.report_issues') }}</span></a>
                    <a href="https://github.com/MattFalahe/blueprint-manager/blob/main/README.md" target="_blank" class="plugin-link"><i class="fas fa-book"></i><span>{{ trans('blueprint-manager::help.readme') }}</span></a>
                </div>

                <div class="success-box" style="margin-top: 15px;">
                    <i class="fas fa-heart"></i>
                    <strong>{{ trans('blueprint-manager::help.support_project') }}:</strong>
                    {!! trans('blueprint-manager::help.support_list') !!}
                </div>
            </div>

            {{-- Version Status — installed vs latest on Packagist. VersionChecker
                 handles caching, fallbacks, dev-branch awareness; the badge
                 colour/label keys off the resolved status. --}}
            @php
                $vs = $versionStatus ?? ['current' => '?', 'current_source' => 'config', 'is_dev_branch' => false, 'latest' => null, 'status' => 'unknown', 'message' => '', 'release_url' => null];
                $statusBadgeClass = [
                    'current'    => 'badge-success',
                    'outdated'   => 'badge-warning',
                    'ahead'      => 'badge-info',
                    'dev_branch' => 'badge-info',
                    'unknown'    => 'badge-secondary',
                ][$vs['status']] ?? 'badge-secondary';
                $statusLabel = [
                    'current'    => '✓ Up to date',
                    'outdated'   => '⚠ Update available',
                    'ahead'      => '🚀 Pre-release',
                    'dev_branch' => '🌱 Development branch',
                    'unknown'    => 'Unable to check',
                ][$vs['status']] ?? 'Unknown';
                $installedDisplay = $vs['is_dev_branch'] ? $vs['current'] : ('v' . $vs['current']);
                $sourceHint = $vs['current_source'] === 'composer'
                    ? "resolved via Composer's installed.json"
                    : 'resolved via blueprint-manager.config.php (fallback, Composer metadata unavailable)';
            @endphp
            <div class="help-card">
                <h3><i class="fas fa-tag"></i> Version status</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; margin: 0.5rem 0;">
                    <div>
                        <strong>Installed:</strong>
                        <span class="badge badge-secondary" style="font-size: 0.9rem;" title="{{ $sourceHint }}">{{ $installedDisplay }}</span>
                    </div>
                    <div>
                        <strong>Latest release:</strong>
                        @if($vs['latest'])
                            <span class="badge badge-secondary" style="font-size: 0.9rem;">v{{ $vs['latest'] }}</span>
                        @else
                            <span class="badge badge-secondary" style="font-size: 0.9rem;">unknown</span>
                        @endif
                    </div>
                    <div>
                        <span class="badge {{ $statusBadgeClass }}" style="font-size: 0.9rem;">{{ $statusLabel }}</span>
                    </div>
                    @if($vs['release_url'])
                        <div>
                            <a href="{{ $vs['release_url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-bp-primary">
                                <i class="fas fa-external-link-alt"></i> Release notes
                            </a>
                        </div>
                    @endif
                </div>
                <small class="text-muted">{{ $vs['message'] }}</small>
                @if($vs['status'] === 'outdated')
                    <div class="info-box" style="margin-top: 0.75rem;">
                        <i class="fas fa-arrow-circle-up"></i>
                        <strong>Upgrade:</strong>
                        <pre style="margin-top: 0.4rem; margin-bottom: 0;"><code>docker compose -f docker-compose.yml -f docker-compose.mariadb.yml -f docker-compose.traefik.yml down
docker compose -f docker-compose.yml -f docker-compose.mariadb.yml -f docker-compose.traefik.yml up -d</code></pre>
                        <small class="text-muted" style="display: block; margin-top: 0.4rem;">SeAT pulls the new composer package and runs migrations automatically on restart.</small>
                    </div>
                @endif
                <small class="text-muted" style="display: block; margin-top: 0.4rem; font-size: 0.75rem;">
                    <i class="fas fa-info-circle"></i>
                    Checked against Packagist, cached for 6 hours. Informational only; the plugin never blocks on it.
                </small>
            </div>

            <div class="help-card">
                <h3><i class="fas fa-rocket"></i> {{ trans('blueprint-manager::help.welcome_title') }}</h3>
                <p class="lead">{{ trans('blueprint-manager::help.welcome_desc') }}</p>
            </div>

            {{-- What's New in v2.0.0 — major release highlight (Overview card #4). --}}
            <div class="help-card whats-new-card">
                <h3><i class="fas fa-gift"></i> What's new in v2.0.0: The Ecosystem Era</h3>
                <p>Blueprint Manager joins the wider plugin suite. It still runs perfectly on its own, but when <strong>Manager Core</strong> is installed it now plugs into the shared ecosystem so other plugins can build on its data.</p>
                <ul class="whats-new-list">
                    <li><i class="fas fa-check-circle"></i> <strong>Manager Core integration.</strong> Publishes the request lifecycle (created / approved / rejected / fulfilled) to the EventBus and exposes per-member and per-corp request stats through the PluginBridge. Optional and guarded, so nothing changes when Manager Core is absent.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>HR Manager engagement.</strong> With HR Manager installed, a member's blueprint requests (volume, fulfilled vs rejected, favourite types) surface on their HR profile and a corp-wide engagement card, turning the request ledger into a retention signal.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Design-system refresh.</strong> The whole plugin now wears the shared suite chrome (cards, badges, tables, buttons) so it looks at home next to the other managers.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Main-character attribution.</strong> Requests and management actions attribute to your SeAT main character, so "requested / approved / fulfilled by" always shows the recognised identity rather than a recently-linked alt.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Security &amp; correctness hardening.</strong> Closed an authorization-scope gap for accounts with no linked characters, gated the detection-settings routes, escaped user-supplied text in the request/library/statistics views, and corrected the research activity-ID labels.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Diagnostic dashboard.</strong> A new admin-only diagnostics page (<code>/blueprint-manager/diagnostic</code>) with health checks, system validation, data-integrity checks, a webhook tester and a per-request trace.</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Version status + docs.</strong> The Version Status card now tells you when an update is available, and the help and README follow the same standards as the rest of the suite.</li>
                </ul>
                <div class="success-box" style="margin-top: 15px;">
                    <i class="fas fa-balance-scale"></i>
                    <strong>Standalone first:</strong>
                    Every ecosystem feature is opt-in and degrades cleanly. Without Manager Core, Blueprint Manager behaves exactly as it did in v1.0.x. The integration is a bonus layer, never a dependency.
                </div>
            </div>

            <div class="help-card">
                <h3><i class="fas fa-info-circle"></i> {{ trans('blueprint-manager::help.what_is_title') }}</h3>
                <p>{{ trans('blueprint-manager::help.what_is_desc') }}</p>
                
                <div class="info-box">
                    <i class="fas fa-lightbulb"></i>
                    <strong>{{ trans('blueprint-manager::help.key_benefit') }}:</strong>
                    {{ trans('blueprint-manager::help.key_benefit_desc') }}
                </div>
            </div>
            
            <div class="help-card">
                <h3><i class="fas fa-star"></i> {{ trans('blueprint-manager::help.core_features') }}</h3>
                <div class="feature-grid">
                    <div class="feature-item"><i class="fas fa-book"></i><h5>{{ trans('blueprint-manager::help.feature_library_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_library_desc') }}</p></div>
                    <div class="feature-item"><i class="fas fa-paper-plane"></i><h5>{{ trans('blueprint-manager::help.feature_requests_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_requests_desc') }}</p></div>
                    <div class="feature-item"><i class="fas fa-bell"></i><h5>{{ trans('blueprint-manager::help.feature_notifications_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_notifications_desc') }}</p></div>
                    <div class="feature-item"><i class="fas fa-chart-line"></i><h5>{{ trans('blueprint-manager::help.feature_statistics_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_statistics_desc') }}</p></div>
                    <div class="feature-item"><i class="fas fa-sync-alt"></i><h5>{{ trans('blueprint-manager::help.feature_autosync_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_autosync_desc') }}</p></div>
                    <div class="feature-item"><i class="fas fa-shield-alt"></i><h5>{{ trans('blueprint-manager::help.feature_permissions_title') }}</h5><p>{{ trans('blueprint-manager::help.feature_permissions_desc') }}</p></div>
                </div>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-book-open"></i> {{ trans('blueprint-manager::help.quick_start_title') }}</h3>
                <ol>
                    <li>{{ trans('blueprint-manager::help.quick_start_step1') }}</li>
                    <li>{{ trans('blueprint-manager::help.quick_start_step2') }}</li>
                    <li>{{ trans('blueprint-manager::help.quick_start_step3') }}</li>
                    <li>{{ trans('blueprint-manager::help.quick_start_step4') }}</li>
                    <li>{{ trans('blueprint-manager::help.quick_start_step5') }}</li>
                </ol>
                <div class="info-box"><i class="fas fa-info-circle"></i><strong>{{ trans('blueprint-manager::help.quick_start_note') }}:</strong> {{ trans('blueprint-manager::help.quick_start_note_desc') }}</div>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-external-link-alt"></i> {{ trans('blueprint-manager::help.quick_links') }}</h3>
                <div class="quick-links">
                    <a href="{{ route('blueprint-manager.library') }}" class="quick-link"><i class="fas fa-book"></i> {{ trans('blueprint-manager::help.view_library') }}</a>
                    <a href="{{ route('blueprint-manager.requests') }}" class="quick-link"><i class="fas fa-paper-plane"></i> {{ trans('blueprint-manager::help.view_requests') }}</a>
                    <a href="{{ route('blueprint-manager.statistics') }}" class="quick-link"><i class="fas fa-chart-line"></i> {{ trans('blueprint-manager::help.view_statistics') }}</a>
                </div>
            </div>
        </div>

        {{-- Getting Started --}}
        <div id="getting-started" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-rocket"></i> {{ trans('blueprint-manager::help.getting_started_intro') }}</h3>
                <p>{{ trans('blueprint-manager::help.getting_started_intro_desc') }}</p>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-cog"></i> {{ trans('blueprint-manager::help.initial_config') }}</h3>
                <h4>{{ trans('blueprint-manager::help.config_step1') }}</h4>
                <p>{!! trans('blueprint-manager::help.config_step1_desc') !!}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.config_step1_pattern') !!}</li>
                    <li>{!! trans('blueprint-manager::help.config_step1_category') !!}</li>
                    <li>{!! trans('blueprint-manager::help.config_step1_filter') !!}</li>
                </ul>
                <div class="info-box"><i class="fas fa-lightbulb"></i><strong>{{ trans('blueprint-manager::help.config_tip') }}:</strong> {{ trans('blueprint-manager::help.config_tip_desc') }}</div>
                <h4>{{ trans('blueprint-manager::help.config_step2') }}</h4>
                <p>{{ trans('blueprint-manager::help.config_step2_desc') }}</p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.config_step2_event1') }}</li>
                    <li>{{ trans('blueprint-manager::help.config_step2_event2') }}</li>
                    <li>{{ trans('blueprint-manager::help.config_step2_event3') }}</li>
                    <li>{{ trans('blueprint-manager::help.config_step2_event4') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.config_step3') }}</h4>
                <p>{{ trans('blueprint-manager::help.config_step3_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.config_step3_perm1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.config_step3_perm2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.config_step3_perm3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.config_step3_perm4') !!}</li>
                </ul>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-graduation-cap"></i> {{ trans('blueprint-manager::help.first_steps') }}</h3>
                <ol>
                    <li>{!! trans('blueprint-manager::help.first_steps_step1') !!}<p>{{ trans('blueprint-manager::help.first_steps_step1_desc') }}</p></li>
                    <li>{!! trans('blueprint-manager::help.first_steps_step2') !!}<p>{{ trans('blueprint-manager::help.first_steps_step2_desc') }}</p></li>
                    <li>{!! trans('blueprint-manager::help.first_steps_step3') !!}<p>{{ trans('blueprint-manager::help.first_steps_step3_desc') }}</p></li>
                    <li>{!! trans('blueprint-manager::help.first_steps_step4') !!}<p>{{ trans('blueprint-manager::help.first_steps_step4_desc') }}</p></li>
                </ol>
            </div>
        </div>

        {{-- Features --}}
        <div id="features" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-list-check"></i> {{ trans('blueprint-manager::help.features_complete') }}</h3>
                <h4>{{ trans('blueprint-manager::help.blueprint_organization') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.feat_org_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_org_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_org_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_org_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_org_5') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.request_management') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.feat_req_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_req_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_req_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_req_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_req_5') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.notifications_features') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.feat_notif_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_notif_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_notif_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_notif_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_notif_5') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.statistics_analytics') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.feat_stats_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_stats_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_stats_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_stats_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_stats_5') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.search_filtering') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.feat_search_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_search_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_search_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.feat_search_4') !!}</li>
                </ul>
            </div>
        </div>

        {{-- Blueprint Library --}}
        <div id="library" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-book"></i> {{ trans('blueprint-manager::help.library_guide') }}</h3>
                <h4>{{ trans('blueprint-manager::help.library_overview') }}</h4>
                <p>{{ trans('blueprint-manager::help.library_overview_desc') }}</p>
                <h4>{{ trans('blueprint-manager::help.viewing_blueprints') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.viewing_bp_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.viewing_bp_2') !!}
                        <ul>
                            <li>{{ trans('blueprint-manager::help.viewing_bp_2_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.viewing_bp_2_2') }}</li>
                            <li>{{ trans('blueprint-manager::help.viewing_bp_2_3') }}</li>
                            <li>{{ trans('blueprint-manager::help.viewing_bp_2_4') }}</li>
                            <li>{{ trans('blueprint-manager::help.viewing_bp_2_5') }}</li>
                        </ul>
                    </li>
                    <li>{!! trans('blueprint-manager::help.viewing_bp_3') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.blueprint_details') }}</h4>
                <p>{{ trans('blueprint-manager::help.blueprint_details_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.bp_detail_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.bp_detail_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.bp_detail_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.bp_detail_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.bp_detail_5') !!}</li>
                </ul>
                <div class="info-box"><i class="fas fa-sync-alt"></i><strong>{{ trans('blueprint-manager::help.auto_update') }}:</strong> {{ trans('blueprint-manager::help.auto_update_desc') }}</div>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-folder-tree"></i> {{ trans('blueprint-manager::help.organizing_library') }}</h3>
                <h4>{{ trans('blueprint-manager::help.container_naming') }}</h4>
                <p>{{ trans('blueprint-manager::help.container_naming_desc') }}</p>
                <div class="success-box">
                    <strong>{{ trans('blueprint-manager::help.good_examples') }}:</strong><br>
                    {{ trans('blueprint-manager::help.good_example_1') }}<br>
                    {{ trans('blueprint-manager::help.good_example_2') }}<br>
                    {{ trans('blueprint-manager::help.good_example_3') }}<br>
                    {{ trans('blueprint-manager::help.good_example_4') }}<br>
                    {{ trans('blueprint-manager::help.good_example_5') }}
                </div>
                <h4>{{ trans('blueprint-manager::help.pattern_matching') }}</h4>
                <p>{{ trans('blueprint-manager::help.pattern_matching_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.pattern_ex_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.pattern_ex_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.pattern_ex_3') !!}</li>
                </ul>
                <div class="warning-box"><i class="fas fa-exclamation-triangle"></i><strong>{{ trans('blueprint-manager::help.pattern_priority') }}:</strong> {{ trans('blueprint-manager::help.pattern_priority_desc') }}</div>
            </div>
        </div>

        {{-- Request System --}}
        <div id="requests" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-paper-plane"></i> {{ trans('blueprint-manager::help.request_system_guide') }}</h3>
                <h4>{{ trans('blueprint-manager::help.creating_requests') }}</h4>
                <p>{{ trans('blueprint-manager::help.creating_requests_desc') }}</p>
                <ol>
                    <li>{{ trans('blueprint-manager::help.create_req_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_5') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_6') }}</li>
                    <li>{{ trans('blueprint-manager::help.create_req_7') }}</li>
                </ol>
                <div class="info-box"><i class="fas fa-lightbulb"></i><strong>{{ trans('blueprint-manager::help.create_req_tip') }}:</strong> {{ trans('blueprint-manager::help.create_req_tip_desc') }}</div>
                <h4>{{ trans('blueprint-manager::help.request_states') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.req_state_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.req_state_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.req_state_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.req_state_4') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.managing_requests') }}</h4>
                <ul>
                    <li>{!! trans('blueprint-manager::help.manage_req_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.manage_req_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.manage_req_3') !!}</li>
                </ul>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-user-shield"></i> {{ trans('blueprint-manager::help.manager_workflow') }}</h3>
                <h4>{{ trans('blueprint-manager::help.processing_requests') }}</h4>
                <p>{{ trans('blueprint-manager::help.processing_desc') }}</p>
                <h5>{{ trans('blueprint-manager::help.approve_requests') }}</h5>
                <ul>
                    <li>{{ trans('blueprint-manager::help.approve_desc_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.approve_desc_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.approve_desc_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.approve_desc_4') }}</li>
                </ul>
                <h5>{{ trans('blueprint-manager::help.reject_requests') }}</h5>
                <ul>
                    <li>{{ trans('blueprint-manager::help.reject_desc_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.reject_desc_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.reject_desc_3') }}</li>
                </ul>
                <h5>{{ trans('blueprint-manager::help.fulfill_requests') }}</h5>
                <ul>
                    <li>{{ trans('blueprint-manager::help.fulfill_desc_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.fulfill_desc_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.fulfill_desc_3') }}</li>
                </ul>
                <div class="success-box"><i class="fas fa-check-circle"></i><strong>{{ trans('blueprint-manager::help.best_practice') }}:</strong> {{ trans('blueprint-manager::help.best_practice_desc') }}</div>
            </div>
            <div class="help-card">
                <h3><i class="fas fa-bell"></i> {{ trans('blueprint-manager::help.request_notifications') }}</h3>
                <p>{{ trans('blueprint-manager::help.notif_desc') }}</p>
                <h4>{{ trans('blueprint-manager::help.notif_new') }}</h4>
                <ul>
                    <li>{{ trans('blueprint-manager::help.notif_new_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.notif_new_2') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.notif_approved') }}</h4>
                <ul>
                    <li>{{ trans('blueprint-manager::help.notif_approved_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.notif_approved_2') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.notif_rejected') }}</h4>
                <ul>
                    <li>{{ trans('blueprint-manager::help.notif_rejected_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.notif_rejected_2') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.notif_fulfilled') }}</h4>
                <ul>
                    <li>{{ trans('blueprint-manager::help.notif_fulfilled_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.notif_fulfilled_2') }}</li>
                </ul>
            </div>
        </div>

        {{-- Statistics --}}
        <div id="statistics" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-chart-line"></i> {{ trans('blueprint-manager::help.statistics_analytics_title') }}</h3>
                <h4>{{ trans('blueprint-manager::help.overall_statistics') }}</h4>
                <p>{{ trans('blueprint-manager::help.overall_stats_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.stat_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.stat_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.stat_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.stat_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.stat_5') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.time_series_graphs') }}</h4>
                <p>{{ trans('blueprint-manager::help.time_series_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.time_7days') !!}</li>
                    <li>{!! trans('blueprint-manager::help.time_30days') !!}</li>
                    <li>{!! trans('blueprint-manager::help.time_90days') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.character_statistics') }}</h4>
                <p>{{ trans('blueprint-manager::help.char_stats_desc') }}</p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.char_stat_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.char_stat_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.char_stat_3') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.blueprint_popularity') }}</h4>
                <p>{{ trans('blueprint-manager::help.bp_popularity_desc') }}</p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.bp_pop_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.bp_pop_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.bp_pop_3') }}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.corporation_comparison') }}</h4>
                <p>{{ trans('blueprint-manager::help.corp_comp_desc') }}</p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.corp_comp_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.corp_comp_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.corp_comp_3') }}</li>
                </ul>
                <div class="info-box"><i class="fas fa-chart-bar"></i><strong>{{ trans('blueprint-manager::help.use_cases') }}:</strong> {{ trans('blueprint-manager::help.use_cases_desc') }}</div>
            </div>
        </div>

        {{-- Settings --}}
        <div id="settings" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-cog"></i> {{ trans('blueprint-manager::help.settings_configuration') }}</h3>
                <h4>{{ trans('blueprint-manager::help.container_configurations') }}</h4>
                <p>{{ trans('blueprint-manager::help.container_config_desc') }}</p>
                <h5>{{ trans('blueprint-manager::help.adding_pattern') }}</h5>
                <ol>
                    <li>{{ trans('blueprint-manager::help.add_pattern_1') }}</li>
                    <li>{!! trans('blueprint-manager::help.add_pattern_2') !!}</li>
                    <li>{{ trans('blueprint-manager::help.add_pattern_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.add_pattern_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.add_pattern_5') }}</li>
                    <li>{{ trans('blueprint-manager::help.add_pattern_6') }}</li>
                </ol>
                <h5>{{ trans('blueprint-manager::help.container_detection') }}</h5>
                <p>{{ trans('blueprint-manager::help.detection_desc') }}</p>
                <ol>
                    <li>{{ trans('blueprint-manager::help.detect_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_5') }}</li>
                </ol>
                <div class="success-box"><i class="fas fa-magic"></i><strong>{{ trans('blueprint-manager::help.time_saver') }}:</strong> {{ trans('blueprint-manager::help.time_saver_desc') }}</div>
                <h4>{{ trans('blueprint-manager::help.webhook_configuration') }}</h4>
                <p>{{ trans('blueprint-manager::help.webhook_desc') }}</p>
                <h5>{{ trans('blueprint-manager::help.creating_webhook') }}</h5>
                <ol>
                    <li>{{ trans('blueprint-manager::help.webhook_1') }}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_2') !!}</li>
                    <li>{{ trans('blueprint-manager::help.webhook_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.webhook_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.webhook_5') }}
                        <ul>
                            <li>{{ trans('blueprint-manager::help.webhook_5_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.webhook_5_2') }}</li>
                            <li>{{ trans('blueprint-manager::help.webhook_5_3') }}</li>
                            <li>{{ trans('blueprint-manager::help.webhook_5_4') }}</li>
                        </ul>
                    </li>
                    <li>{{ trans('blueprint-manager::help.webhook_6') }}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_7') !!}</li>
                </ol>
                <div class="warning-box"><i class="fas fa-exclamation-triangle"></i><strong>{{ trans('blueprint-manager::help.security_warning') }}:</strong> {{ trans('blueprint-manager::help.security_desc') }}</div>
                <h4>{{ trans('blueprint-manager::help.detection_settings') }}</h4>
                <p>{{ trans('blueprint-manager::help.detection_settings_desc') }}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.detect_setting_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.detect_setting_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.detect_setting_3') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.sharing_heading') }}</h4>
                <p>{{ trans('blueprint-manager::help.sharing_desc') }}</p>
                <p>{!! trans('blueprint-manager::help.sharing_modes_intro') !!}</p>
                <ul>
                    <li>{!! trans('blueprint-manager::help.sharing_mode_corp') !!}</li>
                    <li>{!! trans('blueprint-manager::help.sharing_mode_corporations') !!}</li>
                    <li>{!! trans('blueprint-manager::help.sharing_mode_alliance') !!}</li>
                    <li>{!! trans('blueprint-manager::help.sharing_mode_all') !!}</li>
                </ul>
                <p>{{ trans('blueprint-manager::help.sharing_save') }}</p>
                <div class="warning-box"><i class="fas fa-exclamation-triangle"></i><strong>{{ trans('blueprint-manager::help.sharing_boundary') }}:</strong> {{ trans('blueprint-manager::help.sharing_boundary_desc') }}</div>
            </div>
        </div>

        {{-- Permissions --}}
        <div id="permissions" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-shield-alt"></i> {{ trans('blueprint-manager::help.permission_system') }}</h3>
                <h4>{{ trans('blueprint-manager::help.available_permissions') }}</h4>
                <h5>{{ trans('blueprint-manager::help.perm_view') }}</h5>
                <ul>
                    <li>{!! trans('blueprint-manager::help.perm_view_access') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_view_purpose') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_view_recommended') !!}</li>
                </ul>
                <h5>{{ trans('blueprint-manager::help.perm_request') }}</h5>
                <ul>
                    <li>{!! trans('blueprint-manager::help.perm_request_access') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_request_purpose') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_request_recommended') !!}</li>
                </ul>
                <h5>{{ trans('blueprint-manager::help.perm_manage') }}</h5>
                <ul>
                    <li>{!! trans('blueprint-manager::help.perm_manage_access') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_manage_purpose') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_manage_recommended') !!}</li>
                </ul>
                <h5>{{ trans('blueprint-manager::help.perm_settings') }}</h5>
                <ul>
                    <li>{!! trans('blueprint-manager::help.perm_settings_access') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_settings_purpose') !!}</li>
                    <li>{!! trans('blueprint-manager::help.perm_settings_recommended') !!}</li>
                </ul>
                <h4>{{ trans('blueprint-manager::help.permission_scenarios') }}</h4>
                <div class="feature-grid">
                    <div class="feature-item">
                        <h5>{{ trans('blueprint-manager::help.scenario_basic') }}</h5>
                        <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
                            <li>{{ trans('blueprint-manager::help.scenario_basic_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_basic_2') }}</li>
                        </ul>
                    </div>
                    <div class="feature-item">
                        <h5>{{ trans('blueprint-manager::help.scenario_manager') }}</h5>
                        <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
                            <li>{{ trans('blueprint-manager::help.scenario_manager_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_manager_2') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_manager_3') }}</li>
                        </ul>
                    </div>
                    <div class="feature-item">
                        <h5>{{ trans('blueprint-manager::help.scenario_director') }}</h5>
                        <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
                            <li>{{ trans('blueprint-manager::help.scenario_director_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_director_2') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_director_3') }}</li>
                            <li>{{ trans('blueprint-manager::help.scenario_director_4') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="info-box" style="margin-top: 20px;"><i class="fas fa-user-cog"></i><strong>{{ trans('blueprint-manager::help.configuration_note') }}:</strong> {{ trans('blueprint-manager::help.configuration_desc') }}</div>
            </div>
        </div>

        {{-- FAQ --}}
        <div id="faq" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-question-circle"></i> {{ trans('blueprint-manager::help.faq_title') }}</h3>
                @foreach(range(1, 13) as $i)
                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ trans('blueprint-manager::help.faq_'.$i.'_q') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>{!! trans('blueprint-manager::help.faq_'.$i.'_a') !!}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Troubleshooting --}}
        <div id="troubleshooting" class="help-section">
            <div class="help-card">
                <h3><i class="fas fa-wrench"></i> {{ trans('blueprint-manager::help.troubleshooting_guide') }}</h3>

                <div class="info-box">
                    <i class="fas fa-stethoscope"></i>
                    <strong>Diagnostics page:</strong>
                    Administrators (with the Settings permission) can open the diagnostic dashboard at
                    <code>/blueprint-manager/diagnostic</code> for live health checks, system validation,
                    data-integrity checks, a webhook tester, and a per-request trace. It is deliberately not
                    in the sidebar; reach it by URL. Start there whenever something looks off.
                </div>

                <h4>{{ trans('blueprint-manager::help.trouble_no_blueprints') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_no_bp') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.possible_causes') }}</strong></p>
                <h5>{{ trans('blueprint-manager::help.cause_1') }}</h5>
                {!! trans('blueprint-manager::help.cause_1_fix') !!}
                <h5>{{ trans('blueprint-manager::help.cause_2') }}</h5>
                {!! trans('blueprint-manager::help.cause_2_fix') !!}
                <h5>{{ trans('blueprint-manager::help.cause_3') }}</h5>
                {!! trans('blueprint-manager::help.cause_3_fix') !!}
                <h5>{{ trans('blueprint-manager::help.cause_4') }}</h5>
                {!! trans('blueprint-manager::help.cause_4_fix') !!}

                <h4>{{ trans('blueprint-manager::help.trouble_webhooks') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_webhook') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.webhook_solutions') }}</strong></p>
                <ol>
                    <li>{!! trans('blueprint-manager::help.webhook_sol_1') !!}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_sol_2') !!}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_sol_3') !!}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_sol_4') !!}</li>
                    <li>{!! trans('blueprint-manager::help.webhook_sol_5') !!}</li>
                </ol>

                <h4>{{ trans('blueprint-manager::help.trouble_not_updating') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_update') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.update_solutions') }}</strong></p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.update_sol_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.update_sol_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.update_sol_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.update_sol_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.update_sol_5') }}</li>
                </ul>
                <div class="info-box"><i class="fas fa-info-circle"></i><strong>{{ trans('blueprint-manager::help.understanding_flow') }}:</strong> {{ trans('blueprint-manager::help.data_flow') }}</div>

                <h4>{{ trans('blueprint-manager::help.trouble_permissions') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_perm') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.perm_solutions') }}</strong></p>
                <ol>
                    <li>{{ trans('blueprint-manager::help.perm_sol_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.perm_sol_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.perm_sol_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.perm_sol_4') }}</li>
                </ol>

                <h4>{{ trans('blueprint-manager::help.trouble_statistics') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_stats') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.stats_reasons') }}</strong></p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.stats_reason_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.stats_reason_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.stats_reason_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.stats_reason_4') }}</li>
                </ul>

                <h4>{{ trans('blueprint-manager::help.trouble_detection') }}</h4>
                <div class="purple-box"><strong>{{ trans('blueprint-manager::help.symptom') }}:</strong> {{ trans('blueprint-manager::help.symptom_detect') }}</div>
                <p><strong>{{ trans('blueprint-manager::help.detect_checklist') }}</strong></p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.detect_check_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_check_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_check_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_check_4') }}</li>
                    <li>{{ trans('blueprint-manager::help.detect_check_5') }}</li>
                </ul>

                <h4>{{ trans('blueprint-manager::help.getting_help') }}</h4>
                <p>{{ trans('blueprint-manager::help.help_steps') }}</p>
                <ol>
                    <li>{!! trans('blueprint-manager::help.help_1') !!}</li>
                    <li>{{ trans('blueprint-manager::help.help_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.help_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.help_4') }}
                        <ul>
                            <li>{{ trans('blueprint-manager::help.help_4_1') }}</li>
                            <li>{{ trans('blueprint-manager::help.help_4_2') }}</li>
                            <li>{{ trans('blueprint-manager::help.help_4_3') }}</li>
                            <li>{{ trans('blueprint-manager::help.help_4_4') }}</li>
                            <li>{{ trans('blueprint-manager::help.help_4_5') }}</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('javascript')
<script>
$(document).ready(function() {
    // Only handle clicks on help navigation links, not all nav-links on the page
    $('.help-nav .nav-link').click(function(e) {
        e.preventDefault();
        const section = $(this).data('section');
        $('.help-nav .nav-link').removeClass('active');
        $(this).addClass('active');
        $('.help-section').removeClass('active');
        $('#'+section).addClass('active');
        $('.help-content').scrollTop(0);
    });
    $('.faq-question').click(function() {
        $(this).closest('.faq-item').toggleClass('open');
    });
    $('#helpSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        if (search.length === 0) {
            $('.help-card').show();
            return;
        }
        $('.help-card').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(search));
        });
    });
});
</script>
@endpush
