@extends('web::layouts.grids.12')

@section('title', 'Blueprint Manager - Diagnostics')
@section('page_header', 'Blueprint Manager - Diagnostics')

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/blueprint-manager/css/blueprint-manager.css') }}?v=3">
<style>
    /* Diagnostic chrome — scoped to .blueprint-manager-wrapper.diagnostic-page
       so it cannot leak into other Blueprint Manager views. Mirrors the
       suite-standard underline-tab diagnostic pattern. */
    .blueprint-manager-wrapper.diagnostic-page .diag-tabs {
        display: flex; gap: 0; border-bottom: 2px solid #454d55;
        margin: 1.25rem 0; padding: 0; list-style: none; flex-wrap: wrap;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab {
        padding: 0.6rem 1.1rem; color: #8b95a5; cursor: pointer;
        border-bottom: 3px solid transparent; font-weight: 500; font-size: 0.9rem;
        transition: all 0.15s; user-select: none; display: flex; align-items: center; gap: 0.5rem;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab:hover { color: #c2c7d0; border-bottom-color: #3a4049; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab.active { color: #667eea; border-bottom-color: #667eea; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-pane { display: none; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-pane.active { display: block; }

    .blueprint-manager-wrapper.diagnostic-page .diag-tab-intro {
        padding: 0.85rem 1.1rem; background: rgba(102, 126, 234, 0.08);
        border-left: 3px solid #667eea; border-radius: 5px; margin-bottom: 1.25rem;
        color: #c2c7d0; font-size: 0.9rem; line-height: 1.5;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-intro strong { color: #c7d2fe; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-intro p { margin-bottom: 0.35rem; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-intro p:last-child { margin-bottom: 0; }
    .blueprint-manager-wrapper.diagnostic-page .diag-tab-intro code { color: #a5b4fc; background: rgba(0,0,0,0.25); padding: 0 0.25rem; border-radius: 3px; }

    .blueprint-manager-wrapper.diagnostic-page .diag-section {
        background: #2a2f3a; border: 1px solid #454d55; border-radius: 8px;
        margin-bottom: 1.5rem; overflow: hidden;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-section-header {
        padding: 0.75rem 1.1rem; background: #343a45; border-bottom: 1px solid #454d55;
        display: flex; align-items: center; justify-content: space-between;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-section-title { margin: 0; font-size: 1rem; font-weight: 600; color: #fff; }
    .blueprint-manager-wrapper.diagnostic-page .diag-section-body { padding: 0.5rem 1.1rem; color: #c2c7d0; }

    .blueprint-manager-wrapper.diagnostic-page .diag-row {
        display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.6rem 0;
        border-bottom: 1px solid #353b46;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-row:last-child { border-bottom: none; }
    .blueprint-manager-wrapper.diagnostic-page .diag-row-label { flex: 0 0 240px; color: #e2e8f0; font-weight: 500; }
    .blueprint-manager-wrapper.diagnostic-page .diag-row-body { flex: 1; }
    .blueprint-manager-wrapper.diagnostic-page .diag-row-value { color: #c2c7d0; }
    .blueprint-manager-wrapper.diagnostic-page .diag-row-detail { color: #8b95a5; font-size: 0.82rem; margin-top: 2px; }

    /* SEMANTIC status badges — DO NOT CHANGE colours */
    .blueprint-manager-wrapper.diagnostic-page .diag-badge {
        font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem;
        border-radius: 0.25rem; letter-spacing: 0.04em; text-transform: uppercase; white-space: nowrap;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-badge.ok   { background: #1c6f3e; color: #d4f4e2; }
    .blueprint-manager-wrapper.diagnostic-page .diag-badge.warn { background: #8a6d1a; color: #fdf0c8; }
    .blueprint-manager-wrapper.diagnostic-page .diag-badge.fail { background: #8b2b2b; color: #f8d7da; }

    .blueprint-manager-wrapper.diagnostic-page .diag-verdict {
        font-size: 1.1rem; font-weight: 700; padding: 0.5rem 1rem; border-radius: 6px; display: inline-block;
    }
    .blueprint-manager-wrapper.diagnostic-page .diag-verdict.ok   { background: rgba(28,111,62,0.25); color: #6ee7b7; }
    .blueprint-manager-wrapper.diagnostic-page .diag-verdict.warn { background: rgba(138,109,26,0.25); color: #fcd34d; }
    .blueprint-manager-wrapper.diagnostic-page .diag-verdict.fail { background: rgba(139,43,43,0.25); color: #fca5a5; }

    .blueprint-manager-wrapper.diagnostic-page .diag-test-result { margin-top: 0.4rem; font-size: 0.85rem; }
</style>
@endpush

@section('full')
@php
    $badge = function ($status) {
        $cls = in_array($status, ['ok','warn','fail'], true) ? $status : 'warn';
        $txt = ['ok' => 'PASS', 'warn' => 'WARN', 'fail' => 'FAIL'][$cls];
        return '<span class="diag-badge ' . $cls . '">' . $txt . '</span>';
    };
    $initialTab = ($traceId ?? 0) > 0 ? 'trace' : 'health';
@endphp
<div class="blueprint-manager-wrapper diagnostic-page" data-initial-tab="{{ $initialTab }}">
    <div class="card card-dark">
        <div class="card-body">

    <ul class="diag-tabs">
        <li class="diag-tab" data-tab="health"><i class="fas fa-heartbeat"></i> Health Checks</li>
        <li class="diag-tab" data-tab="master"><i class="fas fa-vial"></i> Master Test</li>
        <li class="diag-tab" data-tab="system"><i class="fas fa-microchip"></i> System Validation</li>
        <li class="diag-tab" data-tab="settings"><i class="fas fa-sliders-h"></i> Settings Health</li>
        <li class="diag-tab" data-tab="integrity"><i class="fas fa-database"></i> Data Integrity</li>
        <li class="diag-tab" data-tab="notifications"><i class="fas fa-bell"></i> Notification Testing</li>
        <li class="diag-tab" data-tab="trace"><i class="fas fa-route"></i> Request Trace</li>
    </ul>

    {{-- ============ HEALTH CHECKS (default landing) ============ --}}
    <div class="diag-tab-pane" data-pane="health">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> a fast, at-a-glance pulse on Blueprint Manager: tables, request volume by status, the pending backlog, container patterns, webhooks, and whether Manager Core is connected.</p>
            <p><strong>When to use:</strong> first stop after install or upgrade, and any time something looks off.</p>
            <p><strong>Heads up:</strong> a <span class="diag-badge warn">WARN</span> is "worth a look", not "broken". Empty container patterns or a long pending backlog are common, expected states.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">At a glance</h4></div>
            <div class="diag-section-body">
                @foreach($health as $c)
                    <div class="diag-row">
                        <div class="diag-row-label">{{ $c['label'] }}</div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $c['value'] }}</span>
                            @if($c['detail'])<div class="diag-row-detail">{{ $c['detail'] }}</div>@endif
                        </div>
                        {!! $badge($c['status']) !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ MASTER TEST ============ --}}
    <div class="diag-tab-pane" data-pane="master">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> runs every System Validation and Data Integrity check and rolls the results into one pass / warn / fail verdict.</p>
            <p><strong>When to use:</strong> a single go/no-go before cutting a release or after a big config change.</p>
            <p><strong>Heads up:</strong> the verdict is the worst result across all checks. Open the System Validation and Data Integrity tabs for the per-check detail.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header">
                <h4 class="diag-section-title">Verdict</h4>
                <span class="diag-verdict {{ $masterTest['verdict'] }}">
                    @if($masterTest['verdict'] === 'ok') ✓ All checks pass
                    @elseif($masterTest['verdict'] === 'warn') ⚠ {{ $masterTest['warn'] }} warning(s)
                    @else ✗ {{ $masterTest['fail'] }} failure(s)
                    @endif
                </span>
            </div>
            <div class="diag-section-body">
                <p class="text-muted" style="padding-top: 0.5rem;">{{ $masterTest['pass'] }} passed · {{ $masterTest['warn'] }} warnings · {{ $masterTest['fail'] }} failures · {{ $masterTest['total'] }} checks total</p>
                @foreach($masterTest['checks'] as $c)
                    <div class="diag-row">
                        <div class="diag-row-label">{{ $c['label'] }}</div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $c['value'] }}</span>
                            @if($c['detail'])<div class="diag-row-detail">{{ $c['detail'] }}</div>@endif
                        </div>
                        {!! $badge($c['status']) !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ SYSTEM VALIDATION ============ --}}
    <div class="diag-tab-pane" data-pane="system">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> validates the things that should never drift: plugin + SeAT tables, the hardcoded industry activity IDs (TE=3 / ME=4 / Copying=5), the canonical request statuses, and the Manager Core wiring (topics registered, capabilities exposed).</p>
            <p><strong>When to use:</strong> after a SeAT or plugin upgrade, or when an integration "should work but doesn't".</p>
            <p><strong>Heads up:</strong> the Manager Core rows only appear when Manager Core is installed; everything here is optional and dormant without it.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Constants &amp; dependencies</h4></div>
            <div class="diag-section-body">
                @foreach($systemValidation as $c)
                    <div class="diag-row">
                        <div class="diag-row-label">{{ $c['label'] }}</div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $c['value'] }}</span>
                            @if($c['detail'])<div class="diag-row-detail">{{ $c['detail'] }}</div>@endif
                        </div>
                        {!! $badge($c['status']) !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ SETTINGS HEALTH ============ --}}
    <div class="diag-tab-pane" data-pane="settings">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> lists the effective configuration values and the per-corporation detection settings (which hangar divisions get scanned).</p>
            <p><strong>When to use:</strong> to confirm a config change took effect, or to see which corps have detection configured.</p>
            <p><strong>Heads up:</strong> config lives in <code>blueprint-manager.config.php</code>; publish + restart for edits to apply.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Configuration</h4></div>
            <div class="diag-section-body">
                @foreach($settingsHealth['config'] as $row)
                    <div class="diag-row">
                        <div class="diag-row-label"><code>{{ $row['key'] }}</code></div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $row['value'] }}</span>
                            @if(!empty($row['note']))<div class="diag-row-detail">{{ $row['note'] }}</div>@endif
                        </div>
                        {!! $badge($row['status']) !!}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Per-corporation detection settings</h4></div>
            <div class="diag-section-body">
                @forelse($settingsHealth['detection'] as $d)
                    <div class="diag-row">
                        <div class="diag-row-label">{{ $d['corp_name'] }}</div>
                        <div class="diag-row-body"><span class="diag-row-value">{{ $d['division_count'] }} hangar division(s) scanned</span></div>
                    </div>
                @empty
                    <p class="text-muted" style="padding: 0.5rem 0;">No per-corp detection settings saved (defaults apply: CorpSAG1-7 + AssetSafety).</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============ DATA INTEGRITY ============ --}}
    <div class="diag-tab-pane" data-pane="integrity">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> looks for inconsistent rows: invalid statuses, decided requests with no actor recorded, requests for unknown blueprint types, a stale pending backlog, stuck-approved requests, bad container match types, and non-HTTPS webhooks.</p>
            <p><strong>When to use:</strong> periodically, and whenever the numbers on a page don't add up.</p>
            <p><strong>Heads up:</strong> these are read-only counts. Nothing here changes data; it just points at what to review.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Consistency checks</h4></div>
            <div class="diag-section-body">
                @foreach($dataIntegrity as $c)
                    <div class="diag-row">
                        <div class="diag-row-label">{{ $c['label'] }}</div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $c['value'] }}</span>
                            @if($c['detail'])<div class="diag-row-detail">{{ $c['detail'] }}</div>@endif
                        </div>
                        {!! $badge($c['status']) !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ NOTIFICATION TESTING ============ --}}
    <div class="diag-tab-pane" data-pane="notifications">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> lists every configured webhook (scope, enabled categories, masked URL) and sends a live test embed to one so you can confirm Discord/Slack delivery end to end.</p>
            <p><strong>When to use:</strong> after creating or editing a webhook, or when notifications aren't arriving.</p>
            <p><strong>Heads up:</strong> the test posts a real message to the channel. Webhook tokens are masked here; manage URLs on <code>Settings &rarr; Webhooks</code>.</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Configured webhooks</h4></div>
            <div class="diag-section-body">
                @forelse($webhooks as $w)
                    <div class="diag-row">
                        <div class="diag-row-label">
                            {{ $w['name'] }}
                            @if(!$w['enabled'])<span class="diag-badge warn" style="margin-left: 4px;">disabled</span>@endif
                        </div>
                        <div class="diag-row-body">
                            <span class="diag-row-value">{{ $w['scope'] }} · {{ $w['masked_url'] }}</span>
                            <div class="diag-row-detail">Fires on: {{ empty($w['categories']) ? 'nothing' : implode(', ', $w['categories']) }}</div>
                            <div class="diag-test-result" id="bp-test-result-{{ $w['id'] }}"></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-bp-primary bp-test-webhook" data-url="{{ $w['webhook_url'] }}" data-id="{{ $w['id'] }}">
                            <i class="fas fa-paper-plane"></i> Send test
                        </button>
                    </div>
                @empty
                    <p class="text-muted" style="padding: 0.5rem 0;">No webhooks configured. Add one on Settings &rarr; Webhooks to receive Discord/Slack notifications.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============ REQUEST TRACE ============ --}}
    <div class="diag-tab-pane" data-pane="trace">
        <div class="diag-tab-intro">
            <p><strong>What this tab does:</strong> walks a single blueprint request through its whole pipeline: who requested it, what they asked for, its current status and the actor behind it, which webhooks would fire, and whether Manager Core would publish an event.</p>
            <p><strong>When to use:</strong> when a specific request behaved unexpectedly. The most powerful debugging surface on this page.</p>
            <p><strong>Heads up:</strong> enter the numeric request id (the <code>id</code> column on <code>blueprint_requests</code>).</p>
        </div>
        <div class="diag-section">
            <div class="diag-section-header"><h4 class="diag-section-title">Trace a request</h4></div>
            <div class="diag-section-body">
                <form method="GET" action="{{ route('blueprint-manager.diagnostic') }}" style="display: flex; gap: 0.5rem; align-items: center; margin: 0.5rem 0;">
                    <input type="number" name="trace_id" value="{{ $traceId ?: '' }}" min="1" placeholder="Request id" class="form-control" style="max-width: 200px;">
                    <button type="submit" class="btn btn-sm btn-bp-primary"><i class="fas fa-route"></i> Trace</button>
                </form>

                @if($trace !== null)
                    @if(empty($trace['found']))
                        <p class="diag-row-detail" style="margin-top: 0.75rem;">No request found with id <strong>{{ $trace['request_id'] }}</strong>.</p>
                    @else
                        <div style="margin-top: 0.75rem;">
                            @foreach($trace['steps'] as $s)
                                <div class="diag-row">
                                    <div class="diag-row-label">{{ $s['label'] }}</div>
                                    <div class="diag-row-body"><span class="diag-row-value">{{ $s['detail'] }}</span></div>
                                    {!! $badge($s['status']) !!}
                                </div>
                            @endforeach
                            @if(!empty($trace['notes']))
                                <div class="diag-row"><div class="diag-row-label">Requester notes</div><div class="diag-row-body"><span class="diag-row-value">{{ $trace['notes'] }}</span></div></div>
                            @endif
                            @if(!empty($trace['response']))
                                <div class="diag-row"><div class="diag-row-label">Manager response</div><div class="diag-row-body"><span class="diag-row-value">{{ $trace['response'] }}</span></div></div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

        </div>{{-- /.card-body --}}
    </div>{{-- /.card.card-dark --}}
</div>
@endsection

@push('javascript')
<script>
(function () {
    var root = document.querySelector('.blueprint-manager-wrapper.diagnostic-page');
    if (!root) return;

    function activate(tab) {
        root.querySelectorAll('.diag-tab').forEach(function (t) {
            t.classList.toggle('active', t.getAttribute('data-tab') === tab);
        });
        root.querySelectorAll('.diag-tab-pane').forEach(function (p) {
            p.classList.toggle('active', p.getAttribute('data-pane') === tab);
        });
    }

    root.querySelectorAll('.diag-tab').forEach(function (t) {
        t.addEventListener('click', function () { activate(t.getAttribute('data-tab')); });
    });

    // Default landing is always Health Checks (no localStorage restore),
    // except when a trace was just requested — land on the Trace tab then.
    activate(root.getAttribute('data-initial-tab') || 'health');

    // Notification test: post the webhook URL to the existing test endpoint.
    var csrf = document.querySelector('meta[name="csrf-token"]');
    csrf = csrf ? csrf.getAttribute('content') : '';
    root.querySelectorAll('.bp-test-webhook').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            var url = btn.getAttribute('data-url');
            var out = document.getElementById('bp-test-result-' + id);
            btn.disabled = true;
            if (out) { out.textContent = 'Sending…'; out.style.color = '#8b95a5'; }
            fetch('{{ route('blueprint-manager.settings.webhooks.test') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ webhook_url: url })
            }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
              .then(function (res) {
                  if (out) {
                      out.textContent = res.j.message || (res.ok ? 'Sent.' : 'Failed.');
                      out.style.color = res.ok ? '#6ee7b7' : '#fca5a5';
                  }
              }).catch(function () {
                  if (out) { out.textContent = 'Request failed.'; out.style.color = '#fca5a5'; }
              }).finally(function () { btn.disabled = false; });
        });
    });
})();
</script>
@endpush
