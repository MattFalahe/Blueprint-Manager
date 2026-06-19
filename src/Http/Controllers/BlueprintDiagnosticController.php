<?php

namespace BlueprintManager\Http\Controllers;

use BlueprintManager\Models\BlueprintContainerConfig;
use BlueprintManager\Models\BlueprintRequest;
use BlueprintManager\Models\WebhookConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Seat\Web\Http\Controllers\Controller;

/**
 * Admin-only diagnostic dashboard. Reached via /blueprint-manager/diagnostic,
 * deliberately NOT in the sidebar. Follows the suite diagnostic standard:
 *
 *   Tier 1 (universal): Health Checks (default) / Master Test /
 *                       System Validation / Settings Health / Data Integrity
 *   Tier 2 (notification plugin): Notification Testing
 *   Tier 3 (domain trace): Request Trace
 *
 * Every check is read-only and cheap (DB counts + a few SDE/Schema lookups),
 * so the page builds all tabs in one pass — no lazy-loading needed. Every
 * tab opens with an intro box explaining what it does, since the diagnostic
 * is not part of Help & Documentation.
 */
class BlueprintDiagnosticController extends Controller
{
    /** The blueprint_* tables this plugin owns. */
    private const PLUGIN_TABLES = [
        'blueprint_requests',
        'blueprint_container_configs',
        'blueprint_detection_settings',
        'blueprint_webhook_configs',
    ];

    /** SeAT-synced tables Blueprint Manager reads from. */
    private const REQUIRED_SEAT_TABLES = [
        'corporation_blueprints',
        'character_infos',
        'character_affiliations',
        'refresh_tokens',
        'corporation_assets',
        'invTypes',
    ];

    /** The only request statuses the workflow + migration enum recognise. */
    private const VALID_STATUSES = ['pending', 'approved', 'fulfilled', 'rejected'];

    /** The only container match types the matcher + migration enum support. */
    private const VALID_MATCH_TYPES = ['exact', 'contains', 'starts_with'];

    /** Topics this plugin publishes (must be registered in Manager Core). */
    private const OWNED_TOPICS = [
        'blueprint.request.created',
        'blueprint.request.approved',
        'blueprint.request.rejected',
        'blueprint.request.fulfilled',
    ];

    /** Capabilities this plugin exposes through the PluginBridge. */
    private const OWNED_CAPABILITIES = [
        'blueprint.getCharacterStats',
        'blueprint.getCorpSummary',
    ];

    public function index(Request $request)
    {
        $health           = $this->healthChecks();
        $systemValidation = $this->systemValidation();
        $settingsHealth   = $this->settingsHealth();
        $dataIntegrity    = $this->dataIntegrity();
        $masterTest       = $this->masterTest($systemValidation, $dataIntegrity);
        $webhooks         = $this->webhookList();

        // Request Trace (Tier 3): walk one request through its lifecycle when
        // an id is supplied via the trace form (GET ?trace_id=N).
        $trace = null;
        $traceId = (int) $request->input('trace_id', 0);
        if ($traceId > 0) {
            $trace = $this->traceRequest($traceId);
        }

        $managerCore = $this->managerCorePresent();

        return view('blueprint-manager::diagnostic.index', compact(
            'health',
            'masterTest',
            'systemValidation',
            'settingsHealth',
            'dataIntegrity',
            'webhooks',
            'trace',
            'traceId',
            'managerCore'
        ));
    }

    // -----------------------------------------------------------------
    // Tab 1 — Health Checks (default landing)
    // -----------------------------------------------------------------

    private function healthChecks(): array
    {
        $checks = [];

        $tablesPresent = $this->tablesPresent(self::PLUGIN_TABLES);
        $checks[] = $this->check(
            'Plugin tables',
            count($tablesPresent) === count(self::PLUGIN_TABLES) ? 'ok' : 'fail',
            count($tablesPresent) . ' / ' . count(self::PLUGIN_TABLES) . ' present',
            count($tablesPresent) === count(self::PLUGIN_TABLES) ? null : 'Missing: ' . implode(', ', array_diff(self::PLUGIN_TABLES, $tablesPresent))
        );

        if (Schema::hasTable('blueprint_requests')) {
            $counts = $this->statusCounts();
            $total = array_sum($counts);
            $checks[] = $this->check('Total requests', 'ok', (string) $total);
            $checks[] = $this->check(
                'Pending backlog',
                $counts['pending'] === 0 ? 'ok' : ($this->oldestPendingDays() > 14 ? 'warn' : 'ok'),
                $counts['pending'] . ' pending',
                $counts['pending'] > 0 ? ('Oldest pending: ' . $this->oldestPendingDays() . ' days') : null
            );
            $checks[] = $this->check('Fulfilled / rejected', 'ok', $counts['fulfilled'] . ' / ' . $counts['rejected']);
        }

        if (Schema::hasTable('blueprint_container_configs')) {
            $total = BlueprintContainerConfig::count();
            $enabled = BlueprintContainerConfig::where('enabled', true)->count();
            $checks[] = $this->check(
                'Container patterns',
                $total > 0 ? 'ok' : 'warn',
                $enabled . ' enabled / ' . $total . ' total',
                $total === 0 ? 'No container patterns configured: the library cannot categorise blueprints yet.' : null
            );
        }

        if (Schema::hasTable('blueprint_webhook_configs')) {
            $total = WebhookConfig::count();
            $enabled = WebhookConfig::where('enabled', true)->count();
            $checks[] = $this->check('Webhooks', 'ok', $enabled . ' enabled / ' . $total . ' total');
        }

        $mc = $this->managerCorePresent();
        $checks[] = $this->check(
            'Manager Core',
            'ok',
            $mc ? 'Detected: ecosystem integration active' : 'Not installed: standalone mode',
            $mc ? null : 'Optional. Install Manager Core to publish events + expose stats to HR Manager.'
        );

        return $checks;
    }

    // -----------------------------------------------------------------
    // Tab 2 — Master Test (rollup of the validation + integrity checks)
    // -----------------------------------------------------------------

    private function masterTest(array $systemValidation, array $dataIntegrity): array
    {
        $all = array_merge($systemValidation, $dataIntegrity);
        $pass = $warn = $fail = 0;
        foreach ($all as $c) {
            if ($c['status'] === 'ok') $pass++;
            elseif ($c['status'] === 'warn') $warn++;
            else $fail++;
        }

        return [
            'pass'    => $pass,
            'warn'    => $warn,
            'fail'    => $fail,
            'total'   => count($all),
            'verdict' => $fail > 0 ? 'fail' : ($warn > 0 ? 'warn' : 'ok'),
            'checks'  => $all,
        ];
    }

    // -----------------------------------------------------------------
    // Tab 3 — System Validation (constants + dependencies)
    // -----------------------------------------------------------------

    private function systemValidation(): array
    {
        $checks = [];

        // Plugin tables.
        $present = $this->tablesPresent(self::PLUGIN_TABLES);
        $checks[] = $this->check(
            'Plugin tables',
            count($present) === count(self::PLUGIN_TABLES) ? 'ok' : 'fail',
            count($present) . ' / ' . count(self::PLUGIN_TABLES),
            count($present) === count(self::PLUGIN_TABLES) ? null : 'Missing: ' . implode(', ', array_diff(self::PLUGIN_TABLES, $present))
        );

        // Required SeAT tables.
        $seatPresent = $this->tablesPresent(self::REQUIRED_SEAT_TABLES);
        $missingSeat = array_diff(self::REQUIRED_SEAT_TABLES, $seatPresent);
        $checks[] = $this->check(
            'SeAT source tables',
            empty($missingSeat) ? 'ok' : 'warn',
            count($seatPresent) . ' / ' . count(self::REQUIRED_SEAT_TABLES),
            empty($missingSeat) ? null : 'Missing (older SeAT or pre-sync): ' . implode(', ', $missingSeat)
        );

        // Industry activity IDs (config must match EVE ESI).
        $act = config('blueprint-manager.industry_activities', []);
        $actOk = ($act['te_research'] ?? null) === 3 && ($act['me_research'] ?? null) === 4 && ($act['copying'] ?? null) === 5;
        $checks[] = $this->check(
            'Industry activity IDs',
            $actOk ? 'ok' : 'fail',
            'TE=' . ($act['te_research'] ?? '?') . ' ME=' . ($act['me_research'] ?? '?') . ' Copying=' . ($act['copying'] ?? '?'),
            $actOk ? null : 'Per EVE ESI these must be TE=3, ME=4, Copying=5.'
        );

        // Request statuses config matches the canonical set.
        $statuses = array_keys(config('blueprint-manager.request_statuses', []));
        $statusOk = empty(array_diff($statuses, self::VALID_STATUSES)) && empty(array_diff(self::VALID_STATUSES, $statuses));
        $checks[] = $this->check(
            'Request statuses',
            $statusOk ? 'ok' : 'warn',
            implode(', ', $statuses),
            $statusOk ? null : 'Expected exactly: ' . implode(', ', self::VALID_STATUSES)
        );

        // Manager Core integration (topics + capabilities), when MC present.
        if ($this->managerCorePresent()) {
            $topicsOk = $this->ownedTopicsRegistered();
            $checks[] = $this->check(
                'EventBus topics registered',
                $topicsOk['all'] ? 'ok' : 'warn',
                $topicsOk['count'] . ' / ' . count(self::OWNED_TOPICS) . ' in Manager Core registry',
                $topicsOk['all'] ? null : 'Unregistered topics are silently dropped. Update Manager Core. Missing: ' . implode(', ', $topicsOk['missing'])
            );

            $capsOk = $this->ownedCapabilitiesRegistered();
            $checks[] = $this->check(
                'PluginBridge capabilities',
                $capsOk['all'] ? 'ok' : 'warn',
                $capsOk['count'] . ' / ' . count(self::OWNED_CAPABILITIES) . ' registered',
                $capsOk['all'] ? null : 'Missing: ' . implode(', ', $capsOk['missing'])
            );
        } else {
            $checks[] = $this->check('Manager Core integration', 'ok', 'Standalone (Manager Core not installed)', 'Events + capabilities are optional and dormant without Manager Core.');
        }

        return $checks;
    }

    // -----------------------------------------------------------------
    // Tab 4 — Settings Health (config values)
    // -----------------------------------------------------------------

    private function settingsHealth(): array
    {
        $rows = [];

        $version = (string) config('blueprint-manager.version', '?');
        $rows[] = ['key' => 'version', 'value' => $version, 'status' => 'ok', 'note' => 'Fallback for the Version Status card'];

        $bpPage = (int) config('blueprint-manager.pagination.blueprints', 0);
        $rows[] = ['key' => 'pagination.blueprints', 'value' => (string) $bpPage, 'status' => $bpPage >= 1 ? 'ok' : 'warn', 'note' => $bpPage >= 1 ? null : 'Should be >= 1'];

        $reqPage = (int) config('blueprint-manager.pagination.requests', 0);
        $rows[] = ['key' => 'pagination.requests', 'value' => (string) $reqPage, 'status' => $reqPage >= 1 ? 'ok' : 'warn', 'note' => $reqPage >= 1 ? null : 'Should be >= 1'];

        $cacheEnabled = config('blueprint-manager.cache.enabled', null);
        $rows[] = ['key' => 'cache.enabled', 'value' => $cacheEnabled ? 'true' : 'false', 'status' => 'ok', 'note' => null];

        $cacheTtl = (int) config('blueprint-manager.cache.ttl', 0);
        $rows[] = ['key' => 'cache.ttl', 'value' => $cacheTtl . 's', 'status' => $cacheTtl >= 0 ? 'ok' : 'warn', 'note' => null];

        // Per-corp detection settings.
        $detection = [];
        if (Schema::hasTable('blueprint_detection_settings')) {
            foreach (DB::table('blueprint_detection_settings')->get() as $row) {
                $divs = json_decode((string) ($row->hangar_divisions ?? '[]'), true);
                $detection[] = [
                    'corporation_id' => (int) $row->corporation_id,
                    'corp_name'      => $this->corpName((int) $row->corporation_id),
                    'division_count' => is_array($divs) ? count($divs) : 0,
                ];
            }
        }

        return ['config' => $rows, 'detection' => $detection];
    }

    // -----------------------------------------------------------------
    // Tab 5 — Data Integrity (orphan rows + consistency)
    // -----------------------------------------------------------------

    private function dataIntegrity(): array
    {
        $checks = [];

        if (Schema::hasTable('blueprint_requests')) {
            // Invalid status values.
            $badStatus = BlueprintRequest::whereNotIn('status', self::VALID_STATUSES)->count();
            $checks[] = $this->check('Requests with invalid status', $badStatus === 0 ? 'ok' : 'fail', (string) $badStatus, $badStatus > 0 ? 'Status outside ' . implode('/', self::VALID_STATUSES) : null);

            // Lifecycle integrity: approved/rejected/fulfilled must carry an actor.
            $approvedNoActor = BlueprintRequest::where('status', 'approved')->whereNull('approved_by')->count();
            $rejectedNoActor = BlueprintRequest::where('status', 'rejected')->whereNull('rejected_by')->count();
            $fulfilledNoActor = BlueprintRequest::where('status', 'fulfilled')->whereNull('fulfilled_by')->count();
            $lifecycle = $approvedNoActor + $rejectedNoActor + $fulfilledNoActor;
            $checks[] = $this->check('Decided requests missing an actor', $lifecycle === 0 ? 'ok' : 'warn', (string) $lifecycle, $lifecycle > 0 ? "approved w/o approver: {$approvedNoActor}, rejected w/o rejector: {$rejectedNoActor}, fulfilled w/o fulfiller: {$fulfilledNoActor}" : null);

            // Orphan blueprint type (not in the SDE). Left-join + whereNull is
            // far cheaper than NOT IN against the ~400k-row invTypes table.
            if (Schema::hasTable('invTypes')) {
                $orphanType = DB::table('blueprint_requests as br')
                    ->leftJoin('invTypes as t', 'br.blueprint_type_id', '=', 't.typeID')
                    ->whereNull('t.typeID')
                    ->count();
                $checks[] = $this->check('Requests for unknown blueprint type', $orphanType === 0 ? 'ok' : 'warn', (string) $orphanType, $orphanType > 0 ? 'blueprint_type_id not found in invTypes (SDE may be out of date)' : null);
            }

            // Stale pending (> 30 days).
            $stalePending = BlueprintRequest::where('status', 'pending')->where('created_at', '<', now()->subDays(30))->count();
            $checks[] = $this->check('Stale pending requests (>30d)', $stalePending === 0 ? 'ok' : 'warn', (string) $stalePending, $stalePending > 0 ? 'Long-pending requests: review or clear the backlog.' : null);

            // Approved but never fulfilled (> 14 days).
            $stuckApproved = BlueprintRequest::where('status', 'approved')->where('approved_at', '<', now()->subDays(14))->count();
            $checks[] = $this->check('Approved but unfulfilled (>14d)', $stuckApproved === 0 ? 'ok' : 'warn', (string) $stuckApproved, $stuckApproved > 0 ? 'Approved requests awaiting in-game delivery for over two weeks.' : null);
        }

        if (Schema::hasTable('blueprint_container_configs')) {
            $badMatch = BlueprintContainerConfig::whereNotIn('match_type', self::VALID_MATCH_TYPES)->count();
            $checks[] = $this->check('Container patterns with invalid match type', $badMatch === 0 ? 'ok' : 'fail', (string) $badMatch, $badMatch > 0 ? 'match_type outside ' . implode('/', self::VALID_MATCH_TYPES) . ' never matches anything.' : null);

            $emptyName = BlueprintContainerConfig::whereNull('container_name')->orWhere('container_name', '')->count();
            $checks[] = $this->check('Container patterns with empty name', $emptyName === 0 ? 'ok' : 'warn', (string) $emptyName);
        }

        if (Schema::hasTable('blueprint_webhook_configs')) {
            $insecure = WebhookConfig::where('enabled', true)
                ->where('webhook_url', 'not like', 'https://%')
                ->count();
            $checks[] = $this->check('Enabled webhooks not using HTTPS', $insecure === 0 ? 'ok' : 'fail', (string) $insecure, $insecure > 0 ? 'Webhook URLs must use https:// (Discord and Slack always do).' : null);
        }

        return $checks;
    }

    // -----------------------------------------------------------------
    // Tab 6 — Notification Testing (webhook list)
    // -----------------------------------------------------------------

    private function webhookList(): array
    {
        if (!Schema::hasTable('blueprint_webhook_configs')) {
            return [];
        }

        return WebhookConfig::orderBy('name')->get()->map(function ($w) {
            $cats = [];
            if ($w->notify_created)   $cats[] = 'created';
            if ($w->notify_approved)  $cats[] = 'approved';
            if ($w->notify_rejected)  $cats[] = 'rejected';
            if ($w->notify_fulfilled) $cats[] = 'fulfilled';

            return [
                'id'            => (int) $w->id,
                'name'         => (string) $w->name,
                'enabled'      => (bool) $w->enabled,
                'scope'        => $w->corporation_id ? $this->corpName((int) $w->corporation_id) : 'All corporations',
                'categories'   => $cats,
                'masked_url'   => $this->maskWebhookUrl($w->webhook_url),
                'webhook_url'  => (string) $w->webhook_url,
            ];
        })->all();
    }

    // -----------------------------------------------------------------
    // Tab 7 — Request Trace (walk one request through the pipeline)
    // -----------------------------------------------------------------

    private function traceRequest(int $requestId): array
    {
        $req = Schema::hasTable('blueprint_requests') ? BlueprintRequest::find($requestId) : null;
        if (!$req) {
            return ['found' => false, 'request_id' => $requestId];
        }

        $steps = [];

        // 1. Requester resolution.
        $requesterName = $this->characterName((int) $req->character_id);
        $corpId = (int) ($this->characterCorp((int) $req->character_id) ?? $req->corporation_id);
        $steps[] = [
            'label'  => 'Requester',
            'status' => $requesterName !== null ? 'ok' : 'warn',
            'detail' => ($requesterName ?? ('#' . $req->character_id)) . ' (corp: ' . $this->corpName((int) $req->corporation_id) . ')',
        ];

        // 2. Submission.
        $steps[] = [
            'label'  => 'Submitted',
            'status' => 'ok',
            'detail' => 'Qty ' . (int) $req->quantity . ($req->runs ? (' × ' . (int) $req->runs . ' runs') : '') . ' of ' . $this->typeName((int) $req->blueprint_type_id) . ' on ' . (string) $req->created_at,
        ];

        // 3. Current status + decision actor.
        $statusActor = null;
        if ($req->status === 'approved' && $req->approved_by) $statusActor = 'approved by ' . ($this->characterName((int) $req->approved_by) ?? ('#' . $req->approved_by)) . ' at ' . $req->approved_at;
        elseif ($req->status === 'rejected' && $req->rejected_by) $statusActor = 'rejected by ' . ($this->characterName((int) $req->rejected_by) ?? ('#' . $req->rejected_by)) . ' at ' . $req->rejected_at;
        elseif ($req->status === 'fulfilled' && $req->fulfilled_by) $statusActor = 'fulfilled by ' . ($this->characterName((int) $req->fulfilled_by) ?? ('#' . $req->fulfilled_by)) . ' at ' . $req->fulfilled_at;
        $steps[] = [
            'label'  => 'Status',
            'status' => in_array($req->status, self::VALID_STATUSES, true) ? 'ok' : 'fail',
            'detail' => strtoupper((string) $req->status) . ($statusActor ? (': ' . $statusActor) : ''),
        ];

        // 4. Webhooks that would fire for this corp + action.
        $action = $req->status === 'pending' ? 'created' : $req->status;
        $applicable = [];
        if (Schema::hasTable('blueprint_webhook_configs')) {
            $applicable = WebhookConfig::where('enabled', true)
                ->where(function ($q) use ($req) {
                    $q->whereNull('corporation_id')->orWhere('corporation_id', $req->corporation_id);
                })
                ->where('notify_' . $action, true)
                ->pluck('name')->all();
        }
        $steps[] = [
            'label'  => 'Discord/Slack routing',
            'status' => 'ok',
            'detail' => empty($applicable)
                ? 'No webhook subscribes to "' . $action . '" for this corp.'
                : count($applicable) . ' webhook(s): ' . implode(', ', $applicable),
        ];

        // 5. Manager Core EventBus.
        $mc = $this->managerCorePresent();
        $steps[] = [
            'label'  => 'Manager Core EventBus',
            'status' => 'ok',
            'detail' => $mc
                ? 'Would publish blueprint.request.' . $action . ' (request_id ' . $req->id . ').'
                : 'Manager Core not installed: no event published (standalone).',
        ];

        return [
            'found'      => true,
            'request_id' => $requestId,
            'steps'      => $steps,
            'notes'      => (string) ($req->notes ?? ''),
            'response'   => (string) ($req->response_notes ?? ''),
        ];
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function check(string $label, string $status, string $value, ?string $detail = null): array
    {
        return ['label' => $label, 'status' => $status, 'value' => $value, 'detail' => $detail];
    }

    private function tablesPresent(array $tables): array
    {
        return array_values(array_filter($tables, fn ($t) => Schema::hasTable($t)));
    }

    private function statusCounts(): array
    {
        $counts = ['pending' => 0, 'approved' => 0, 'fulfilled' => 0, 'rejected' => 0];
        foreach (BlueprintRequest::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status') as $status => $c) {
            if (isset($counts[$status])) $counts[$status] = (int) $c;
        }
        return $counts;
    }

    private function oldestPendingDays(): int
    {
        $oldest = BlueprintRequest::where('status', 'pending')->min('created_at');
        return $oldest ? (int) \Carbon\Carbon::parse($oldest)->diffInDays(now()) : 0;
    }

    private function managerCorePresent(): bool
    {
        return class_exists('\\ManagerCore\\Topics');
    }

    private function ownedTopicsRegistered(): array
    {
        $missing = [];
        foreach (self::OWNED_TOPICS as $topic) {
            try {
                if (\ManagerCore\Topics::describe($topic) === null) $missing[] = $topic;
            } catch (\Throwable $e) {
                $missing[] = $topic;
            }
        }
        return ['all' => empty($missing), 'count' => count(self::OWNED_TOPICS) - count($missing), 'missing' => $missing];
    }

    private function ownedCapabilitiesRegistered(): array
    {
        $missing = [];
        if (class_exists('\\ManagerCore\\Services\\PluginBridge')) {
            try {
                $bridge = app(\ManagerCore\Services\PluginBridge::class);
                foreach (self::OWNED_CAPABILITIES as $cap) {
                    if (!$bridge->hasCapability('blueprint-manager', $cap)) $missing[] = $cap;
                }
            } catch (\Throwable $e) {
                $missing = self::OWNED_CAPABILITIES;
            }
        } else {
            $missing = self::OWNED_CAPABILITIES;
        }
        return ['all' => empty($missing), 'count' => count(self::OWNED_CAPABILITIES) - count($missing), 'missing' => $missing];
    }

    private function corpName(int $corpId): string
    {
        if ($corpId <= 0 || !Schema::hasTable('corporation_infos')) return '#' . $corpId;
        return (string) (DB::table('corporation_infos')->where('corporation_id', $corpId)->value('name') ?? ('#' . $corpId));
    }

    private function characterName(int $characterId): ?string
    {
        if ($characterId <= 0 || !Schema::hasTable('character_infos')) return null;
        $name = DB::table('character_infos')->where('character_id', $characterId)->value('name');
        return $name !== null ? (string) $name : null;
    }

    private function characterCorp(int $characterId): ?int
    {
        if ($characterId <= 0 || !Schema::hasTable('character_affiliations')) return null;
        $corp = DB::table('character_affiliations')->where('character_id', $characterId)->value('corporation_id');
        return $corp !== null ? (int) $corp : null;
    }

    private function typeName(int $typeId): string
    {
        if ($typeId <= 0 || !Schema::hasTable('invTypes')) return '#' . $typeId;
        return (string) (DB::table('invTypes')->where('typeID', $typeId)->value('typeName') ?? ('#' . $typeId));
    }

    private function maskWebhookUrl(?string $url): string
    {
        if (empty($url)) return '(none)';
        $parts = parse_url($url);
        $host = $parts['host'] ?? 'unknown';
        $path = $parts['path'] ?? '';
        // Discord: /api/webhooks/{id}/{token} — keep the id, hide the token.
        if (preg_match('#/api/webhooks/(\d+)/#', $path, $m)) {
            return $host . '/api/webhooks/' . $m[1] . '/****';
        }
        return $host . '/****';
    }
}
