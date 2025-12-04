@extends('web::layouts.grids.12')

@section('title', trans('blueprint-manager::help.help_documentation'))
@section('page_header', trans('blueprint-manager::help.help_documentation'))

@push('head')
<style>
    .help-wrapper { display: flex; gap: 20px; }
    .help-sidebar { flex: 0 0 280px; position: sticky; top: 20px; max-height: calc(100vh - 120px); overflow-y: auto; }
    .help-content { flex: 1; min-width: 0; }
    .help-nav .nav-link { color: #e2e8f0; border-radius: 5px; margin-bottom: 5px; padding: 10px 15px; transition: all 0.3s; font-size: 0.95rem; }
    .help-nav .nav-link:hover { background: rgba(23, 162, 184, 0.2); }
    .help-nav .nav-link.active { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
    .help-nav .nav-link i { width: 24px; text-align: center; margin-right: 10px; }
    .help-section { display: none; animation: fadeIn 0.3s; }
    .help-section.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .help-card { background: #2d3748; border-radius: 10px; padding: 25px; margin-bottom: 20px; border: 1px solid rgba(23, 162, 184, 0.2); }
    .help-card h3 { color: #17a2b8; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    .help-card h4 { color: #9ca3af; margin-top: 20px; margin-bottom: 10px; font-size: 1.1rem; }
    .help-card h5 { color: #9ca3af; margin-top: 15px; margin-bottom: 8px; font-size: 1rem; }
    .help-card p { color: #d1d5db; line-height: 1.6; margin-bottom: 1rem; }
    .help-card ul, .help-card ol { color: #d1d5db; line-height: 1.8; margin: 15px 0; padding-left: 25px; }
    .help-card ul li, .help-card ol li { margin-bottom: 8px; }
    .help-card code { background: rgba(0, 0, 0, 0.3); padding: 2px 8px; border-radius: 4px; color: #f56565; font-family: 'Courier New', monospace; }
    .help-card pre { background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8; overflow-x: auto; margin: 15px 0; }
    .help-card pre code { background: none; padding: 0; color: #d1d5db; }
    .info-box { background: rgba(23, 162, 184, 0.15); border-left: 4px solid #17a2b8; padding: 15px; margin: 15px 0; border-radius: 5px; color: #d1d5db; line-height: 1.6; }
    .info-box i { margin-right: 8px; vertical-align: middle; }
    .warning-box { background: rgba(251, 191, 36, 0.15); border-left: 4px solid #fbbf24; padding: 15px; margin: 15px 0; border-radius: 5px; color: #d1d5db; line-height: 1.6; }
    .warning-box i { margin-right: 8px; vertical-align: middle; }
    .success-box { background: rgba(28, 200, 138, 0.15); border-left: 4px solid #1cc88a; padding: 15px; margin: 15px 0; border-radius: 5px; color: #d1d5db; line-height: 1.6; }
    .success-box i { margin-right: 8px; vertical-align: middle; }
    .purple-box { background: rgba(156, 39, 176, 0.15); border-left: 4px solid #9c27b0; padding: 15px; margin: 15px 0; border-radius: 5px; color: #d1d5db; line-height: 1.6; }
    .purple-box i { margin-right: 8px; vertical-align: middle; }
    .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0; }
    .feature-item { background: rgba(23, 162, 184, 0.1); padding: 15px; border-radius: 8px; border: 1px solid rgba(23, 162, 184, 0.3); }
    .feature-item i { font-size: 2rem; color: #17a2b8; margin-bottom: 10px; }
    .feature-item h5 { color: #e2e8f0; margin-bottom: 8px; }
    .feature-item p { color: #9ca3af; font-size: 0.9rem; margin: 0; }
    .search-box { position: relative; margin-bottom: 20px; }
    .search-box input { width: 100%; padding: 12px 45px 12px 15px; background: #2d3748; border: 1px solid rgba(23, 162, 184, 0.3); border-radius: 8px; color: #e2e8f0; }
    .search-box i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    .faq-item { background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; margin-bottom: 15px; overflow: hidden; transition: all 0.3s; }
    .faq-item:hover { border-color: rgba(23, 162, 184, 0.3); }
    .faq-question { padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; }
    .faq-question:hover { background: rgba(23, 162, 184, 0.1); }
    .faq-question i { transition: transform 0.3s; }
    .faq-item.open .faq-question i { transform: rotate(180deg); }
    .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; padding: 0 20px; }
    .faq-item.open .faq-answer { max-height: 500px; padding: 0 20px 20px; }
    .plugin-info { background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); border: 1px solid rgba(23, 162, 184, 0.3); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
    .plugin-info .info-row { color: #9ca3af; margin: 5px 0; }
    .plugin-info .author { color: #17a2b8; margin: 10px 0; }
    .plugin-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 15px; }
    .plugin-link { background: rgba(23, 162, 184, 0.1); padding: 10px; border-radius: 5px; border: 1px solid rgba(23, 162, 184, 0.3); color: #17a2b8; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
    .plugin-link:hover { background: rgba(23, 162, 184, 0.2); color: #40d3ff; text-decoration: none; transform: translateX(5px); }
    .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin: 20px 0; }
    .quick-link { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 15px; border-radius: 8px; text-align: center; color: white; text-decoration: none; transition: transform 0.2s, box-shadow 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .quick-link:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4); color: white; text-decoration: none; }
    @media (max-width: 768px) { .help-wrapper { flex-direction: column; } .help-sidebar { position: static; flex: 1; max-height: none; } }
</style>
@endpush

@section('content')
<div class="help-wrapper">
    <div class="help-sidebar">
        <div class="search-box">
            <input type="text" id="helpSearch" placeholder="{{ trans('blueprint-manager::help.search_placeholder') }}">
            <i class="fas fa-search"></i>
        </div>
        <nav class="help-nav">
            <a href="#" class="nav-link active" data-section="overview"><i class="fas fa-home"></i> {{ trans('blueprint-manager::help.overview') }}</a>
            <a href="#" class="nav-link" data-section="getting-started"><i class="fas fa-rocket"></i> {{ trans('blueprint-manager::help.getting_started') }}</a>
            <a href="#" class="nav-link" data-section="features"><i class="fas fa-star"></i> {{ trans('blueprint-manager::help.features') }}</a>
            <a href="#" class="nav-link" data-section="library"><i class="fas fa-book"></i> {{ trans('blueprint-manager::help.library') }}</a>
            <a href="#" class="nav-link" data-section="requests"><i class="fas fa-paper-plane"></i> {{ trans('blueprint-manager::help.requests') }}</a>
            <a href="#" class="nav-link" data-section="statistics"><i class="fas fa-chart-line"></i> {{ trans('blueprint-manager::help.statistics') }}</a>
            <a href="#" class="nav-link" data-section="settings"><i class="fas fa-cog"></i> {{ trans('blueprint-manager::help.settings') }}</a>
            <a href="#" class="nav-link" data-section="permissions"><i class="fas fa-shield-alt"></i> {{ trans('blueprint-manager::help.permissions') }}</a>
            <a href="#" class="nav-link" data-section="faq"><i class="fas fa-question-circle"></i> {{ trans('blueprint-manager::help.faq') }}</a>
            <a href="#" class="nav-link" data-section="troubleshooting"><i class="fas fa-wrench"></i> {{ trans('blueprint-manager::help.troubleshooting') }}</a>
        </nav>
    </div>
    
    <div class="help-content">
        {{-- Overview --}}
        <div id="overview" class="help-section active">
            <div class="plugin-info">
                <h3 style="color: #17a2b8; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> {{ trans('blueprint-manager::help.plugin_info_title') }}</h3>
                <div class="info-row"><strong>{{ trans('blueprint-manager::help.version') }}:</strong> <img src="https://img.shields.io/github/v/release/MattFalahe/blueprint-manager" alt="Version" style="vertical-align: middle;"> <img src="https://img.shields.io/badge/SeAT-5.0-green" alt="SeAT" style="vertical-align: middle;"></div>
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

            <div class="help-card">
                <h3><i class="fas fa-rocket"></i> {{ trans('blueprint-manager::help.welcome_title') }}</h3>
                <p class="lead">{{ trans('blueprint-manager::help.welcome_desc') }}</p>
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
                <h3><i class="fas fa-download"></i> {{ trans('blueprint-manager::help.installation') }}</h3>
                <p>{{ trans('blueprint-manager::help.installation_desc') }}</p>
                <pre><code>{{ trans('blueprint-manager::help.installation_command') }}</code></pre>
                <p>{{ trans('blueprint-manager::help.installation_automatic') }}</p>
                <ul>
                    <li>{{ trans('blueprint-manager::help.installation_auto_1') }}</li>
                    <li>{{ trans('blueprint-manager::help.installation_auto_2') }}</li>
                    <li>{{ trans('blueprint-manager::help.installation_auto_3') }}</li>
                    <li>{{ trans('blueprint-manager::help.installation_auto_4') }}</li>
                </ul>
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
                @foreach(range(1, 12) as $i)
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
