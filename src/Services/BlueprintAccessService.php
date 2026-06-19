<?php

namespace BlueprintManager\Services;

use BlueprintManager\Models\BlueprintLibraryVisibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves which corporations the current user may interact with.
 *
 * Two distinct sets, deliberately kept apart:
 *
 *  - visibleCorporationIds()    — corps whose library the user may VIEW and
 *    REQUEST FROM. This is the user's own corps plus any corp that has shared
 *    its library with them (allowlist / alliance / everyone).
 *
 *  - manageableCorporationIds() — corps whose requests/statistics the user may
 *    MANAGE. Strictly the user's own corps. Library sharing never hands request
 *    management or statistics to outsiders.
 *
 * Both return null for a SeAT superuser (meaning "all corporations") and an
 * array (possibly empty) for everyone else. An empty array is "no access",
 * never "all corporations".
 */
class BlueprintAccessService
{
    /**
     * Corporations whose libraries the current user may view and request from.
     *
     * @return array|null
     */
    public function visibleCorporationIds(): ?array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        if ($user->isAdmin()) {
            return null;
        }

        try {
            $ownCorpIds = $this->ownCorporationIds($user->id);
        } catch (\Throwable $e) {
            Log::error('Blueprint Manager - Failed to resolve own corporations: ' . $e->getMessage());
            return [];
        }

        $visible = $ownCorpIds;

        // Widen the set with any corp that has shared its library. A failure
        // here degrades to own-corps-only (the pre-sharing behaviour) rather
        // than breaking the page.
        try {
            $visible = $this->applySharedLibraries($user->id, $ownCorpIds, $visible);
        } catch (\Throwable $e) {
            Log::error('Blueprint Manager - Library sharing resolution failed, using own corps only: ' . $e->getMessage());
        }

        return array_values(array_unique($visible));
    }

    /**
     * Corporations whose requests and statistics the current user may manage.
     *
     * @return array|null
     */
    public function manageableCorporationIds(): ?array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        if ($user->isAdmin()) {
            return null;
        }

        try {
            return $this->ownCorporationIds($user->id);
        } catch (\Throwable $e) {
            Log::error('Blueprint Manager - Failed to resolve manageable corporations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fold every shared library the user qualifies for into the visible set.
     *
     * @param  int    $userId
     * @param  array  $ownCorpIds
     * @param  array  $visible
     * @return array
     */
    private function applySharedLibraries(int $userId, array $ownCorpIds, array $visible): array
    {
        if (!Schema::hasTable('blueprint_library_visibility')) {
            return $visible;
        }

        $shares = BlueprintLibraryVisibility::all();

        if ($shares->isEmpty()) {
            return $visible;
        }

        $ownAllianceIds = $this->ownAllianceIds($userId);

        // Resolve owner-corp alliances once for any alliance-mode rows.
        $allianceModeCorpIds = $shares
            ->where('visibility_mode', BlueprintLibraryVisibility::MODE_ALLIANCE)
            ->pluck('corporation_id')
            ->all();

        $corpAllianceMap = [];
        if (!empty($allianceModeCorpIds)) {
            $corpAllianceMap = DB::table('corporation_infos')
                ->whereIn('corporation_id', $allianceModeCorpIds)
                ->pluck('alliance_id', 'corporation_id')
                ->toArray();
        }

        foreach ($shares as $share) {
            $ownerCorpId = (int) $share->corporation_id;

            // Own corp is already visible; sharing rules only ever widen.
            if (in_array($ownerCorpId, $visible, true)) {
                continue;
            }

            switch ($share->visibility_mode) {
                case BlueprintLibraryVisibility::MODE_ALL:
                    $visible[] = $ownerCorpId;
                    break;

                case BlueprintLibraryVisibility::MODE_ALLIANCE:
                    $ownerAllianceId = $corpAllianceMap[$ownerCorpId] ?? null;
                    if ($ownerAllianceId && in_array((int) $ownerAllianceId, $ownAllianceIds, true)) {
                        $visible[] = $ownerCorpId;
                    }
                    break;

                case BlueprintLibraryVisibility::MODE_CORPORATIONS:
                    $allowed = array_map('intval', (array) ($share->shared_corporation_ids ?? []));
                    if (!empty(array_intersect($ownCorpIds, $allowed))) {
                        $visible[] = $ownerCorpId;
                    }
                    break;

                // MODE_CORP (and any unexpected value): private, no widening.
            }
        }

        return $visible;
    }

    /**
     * Corporation IDs the user belongs to, via their linked refresh tokens.
     *
     * @param  int  $userId
     * @return array
     */
    private function ownCorporationIds(int $userId): array
    {
        return DB::table('refresh_tokens')
            ->join('character_affiliations', 'refresh_tokens.character_id', '=', 'character_affiliations.character_id')
            ->where('refresh_tokens.user_id', $userId)
            ->whereNull('refresh_tokens.deleted_at')
            ->pluck('character_affiliations.corporation_id')
            ->unique()
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();
    }

    /**
     * Alliance IDs the user belongs to, via their linked refresh tokens.
     *
     * @param  int  $userId
     * @return array
     */
    private function ownAllianceIds(int $userId): array
    {
        return DB::table('refresh_tokens')
            ->join('character_affiliations', 'refresh_tokens.character_id', '=', 'character_affiliations.character_id')
            ->where('refresh_tokens.user_id', $userId)
            ->whereNull('refresh_tokens.deleted_at')
            ->whereNotNull('character_affiliations.alliance_id')
            ->pluck('character_affiliations.alliance_id')
            ->unique()
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();
    }
}
