<?php

namespace BlueprintManager\Services;

use Illuminate\Support\Facades\DB;

/**
 * Read-only stats surface exposed to other plugins (HR Manager) through the
 * Manager Core PluginBridge. Computes per-character and per-corp blueprint
 * request engagement from the blueprint_requests table. No writes, no ESI,
 * no knowledge of who's calling — it just answers questions about the
 * request ledger so consumers can build their own member-engagement views.
 */
class BlueprintBridgeService
{
    /**
     * Per-character request engagement, scoped to one corporation. Mirrors a
     * row of BlueprintStatisticsController::getCharacterStats plus the
     * character's favourite blueprint types. Returns a zeroed shape (never
     * null) when the character has no requests, so callers can render a
     * consistent panel.
     *
     * @return array{total_requests:int, pending:int, approved:int, fulfilled:int, rejected:int, total_quantity:int, rejection_rate:float, last_request:?string, first_request:?string, favourite_types:array}
     */
    public function getCharacterStats(int $characterId, int $corporationId): array
    {
        $row = DB::table('blueprint_requests')
            ->where('character_id', $characterId)
            ->where('corporation_id', $corporationId)
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled_count,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
                SUM(quantity) as total_quantity,
                MAX(created_at) as last_request,
                MIN(created_at) as first_request
            ')
            ->first();

        $total = (int) ($row->total_requests ?? 0);
        if ($total === 0) {
            return $this->emptyCharacterStats();
        }

        $rejected = (int) ($row->rejected_count ?? 0);

        $favourites = DB::table('blueprint_requests')
            ->leftJoin('invTypes', 'blueprint_requests.blueprint_type_id', '=', 'invTypes.typeID')
            ->where('blueprint_requests.character_id', $characterId)
            ->where('blueprint_requests.corporation_id', $corporationId)
            ->groupBy('blueprint_requests.blueprint_type_id', 'invTypes.typeName')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get([
                'blueprint_requests.blueprint_type_id as type_id',
                'invTypes.typeName as type_name',
                DB::raw('COUNT(*) as cnt'),
            ])
            ->map(fn ($r) => [
                'type_id'   => (int) $r->type_id,
                'type_name' => $r->type_name ?: ('Type #' . (int) $r->type_id),
                'count'     => (int) $r->cnt,
            ])->all();

        return [
            'total_requests'  => $total,
            'pending'         => (int) ($row->pending_count ?? 0),
            'approved'        => (int) ($row->approved_count ?? 0),
            'fulfilled'       => (int) ($row->fulfilled_count ?? 0),
            'rejected'        => $rejected,
            'total_quantity'  => (int) ($row->total_quantity ?? 0),
            'rejection_rate'  => round(($rejected / $total) * 100, 1),
            'last_request'    => $row->last_request !== null ? (string) $row->last_request : null,
            'first_request'   => $row->first_request !== null ? (string) $row->first_request : null,
            'favourite_types' => $favourites,
        ];
    }

    /**
     * Corp-wide request rollup: totals by status, unique requesters, the
     * aging of the pending backlog, and the top requesters. Drives a
     * corp-level "blueprint engagement" card.
     *
     * @return array
     */
    public function getCorpSummary(int $corporationId): array
    {
        $totals = DB::table('blueprint_requests')
            ->where('corporation_id', $corporationId)
            ->selectRaw('
                COUNT(*) as total_requests,
                COUNT(DISTINCT character_id) as unique_requesters,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled_count,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
                MIN(CASE WHEN status = "pending" THEN created_at ELSE NULL END) as oldest_pending
            ')
            ->first();

        $total = (int) ($totals->total_requests ?? 0);

        $topRequesters = DB::table('blueprint_requests')
            ->leftJoin('character_infos', 'blueprint_requests.character_id', '=', 'character_infos.character_id')
            ->where('blueprint_requests.corporation_id', $corporationId)
            ->groupBy('blueprint_requests.character_id', 'character_infos.name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get([
                'blueprint_requests.character_id',
                'character_infos.name as character_name',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled_count'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count'),
            ])
            ->map(fn ($r) => [
                'character_id'   => (int) $r->character_id,
                'character_name' => $r->character_name ?: ('#' . (int) $r->character_id),
                'total_requests' => (int) $r->total_requests,
                'fulfilled'      => (int) $r->fulfilled_count,
                'rejected'       => (int) $r->rejected_count,
            ])->all();

        return [
            'total_requests'    => $total,
            'unique_requesters' => (int) ($totals->unique_requesters ?? 0),
            'pending'           => (int) ($totals->pending_count ?? 0),
            'approved'          => (int) ($totals->approved_count ?? 0),
            'fulfilled'         => (int) ($totals->fulfilled_count ?? 0),
            'rejected'          => (int) ($totals->rejected_count ?? 0),
            'rejection_rate'    => $total > 0 ? round(((int) ($totals->rejected_count ?? 0) / $total) * 100, 1) : 0.0,
            'oldest_pending'    => $totals->oldest_pending !== null ? (string) $totals->oldest_pending : null,
            'top_requesters'    => $topRequesters,
        ];
    }

    /**
     * @return array
     */
    private function emptyCharacterStats(): array
    {
        return [
            'total_requests'  => 0,
            'pending'         => 0,
            'approved'        => 0,
            'fulfilled'       => 0,
            'rejected'        => 0,
            'total_quantity'  => 0,
            'rejection_rate'  => 0.0,
            'last_request'    => null,
            'first_request'   => null,
            'favourite_types' => [],
        ];
    }
}
