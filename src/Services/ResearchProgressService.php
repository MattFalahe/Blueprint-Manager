<?php

namespace BlueprintManager\Services;

use Illuminate\Support\Collection;
use Seat\Eveapi\Models\Corporation\CorporationBlueprint;
use Seat\Eveapi\Models\Industry\CorporationIndustryJob;
use Seat\Eveapi\Models\Sde\InvType;
use Carbon\Carbon;

class ResearchProgressService
{
    /**
     * Get active research and copying jobs for a corporation
     *
     * @param int $corporationId
     * @param array|null $blueprintTypeIds Filter by specific blueprint types
     * @return Collection
     */
    public function getActiveResearchJobs(int $corporationId, ?array $blueprintTypeIds = null): Collection
    {
        $query = CorporationIndustryJob::where('corporation_id', $corporationId)
            ->whereIn('activity_id', [3, 4, 5]) // Copying, ME Research, TE Research
            ->where('status', 'active') // Show both running and waiting for delivery
            ->with(['blueprint']);

        if ($blueprintTypeIds) {
            $query->whereIn('blueprint_type_id', $blueprintTypeIds);
        }

        $jobs = $query->orderBy('end_date')->get();

        return $jobs->map(function ($job) use ($corporationId) {
            // Get current blueprint stats
            $blueprint = CorporationBlueprint::where('corporation_id', $corporationId)
                ->where('item_id', $job->blueprint_id)
                ->first();

            return (object) [
                'job_id' => $job->job_id,
                'blueprint_type_id' => $job->blueprint_type_id,
                'blueprint_name' => $job->blueprint->typeName ?? 'Unknown',
                'activity_id' => $job->activity_id,
                'activity_name' => $this->getActivityName($job->activity_id),
                'job_description' => $this->getJobDescription($job, $blueprint),
                'runs' => $job->runs,
                'start_date' => $job->start_date,
                'end_date' => $job->end_date,
                'time_remaining' => $this->getTimeRemaining(Carbon::parse($job->end_date)),
                'progress_percentage' => $this->getProgressPercentage(Carbon::parse($job->start_date), Carbon::parse($job->end_date)),
                'current_me' => $blueprint->material_efficiency ?? null,
                'current_te' => $blueprint->time_efficiency ?? null,
            ];
        });
    }

    /**
     * Get research jobs for a specific blueprint type
     *
     * @param int $corporationId
     * @param int $blueprintTypeId
     * @return Collection
     */
    public function getJobsForBlueprint(int $corporationId, int $blueprintTypeId): Collection
    {
        return $this->getActiveResearchJobs($corporationId, [$blueprintTypeId]);
    }

    /**
     * Get activity name from activity ID
     *
     * @param int $activityId
     * @return string
     */
    private function getActivityName(int $activityId): string
    {
        return match($activityId) {
            3 => 'Copying',
            4 => 'ME Research',
            5 => 'TE Research',
            default => 'Unknown',
        };
    }

    /**
     * Get human-readable job description
     *
     * @param CorporationIndustryJob $job
     * @param CorporationBlueprint|null $blueprint
     * @return string
     */
    private function getJobDescription(CorporationIndustryJob $job, ?CorporationBlueprint $blueprint): string
    {
        if (!$blueprint) {
            return $this->getActivityName($job->activity_id);
        }

        return match($job->activity_id) {
            3 => sprintf('Copying (%d runs)', $job->runs),
            4 => sprintf('ME Research: %d → %d', $blueprint->material_efficiency, $blueprint->material_efficiency + 1),
            5 => sprintf('TE Research: %d → %d', $blueprint->time_efficiency, min($blueprint->time_efficiency + 2, 20)),
            default => $this->getActivityName($job->activity_id),
        };
    }

    /**
     * Calculate time remaining in human-readable format
     *
     * @param Carbon $endDate
     * @return string
     */
    private function getTimeRemaining(Carbon $endDate): string
    {
        $now = now();
        
        if ($endDate <= $now) {
            return 'Ready for Delivery';
        }

        $diff = $now->diff($endDate);

        $parts = [];
        
        if ($diff->d > 0) {
            $parts[] = $diff->d . 'd';
        }
        if ($diff->h > 0 || !empty($parts)) {
            $parts[] = $diff->h . 'h';
        }
        if ($diff->i > 0 || empty($parts)) {
            $parts[] = $diff->i . 'm';
        }

        return implode(' ', $parts);
    }

    /**
     * Calculate job progress percentage
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    private function getProgressPercentage(Carbon $startDate, Carbon $endDate): float
    {
        $now = now();
        
        if ($now >= $endDate) {
            return 100.0;
        }

        if ($now <= $startDate) {
            return 0.0;
        }

        $totalSeconds = $startDate->diffInSeconds($endDate);
        $elapsedSeconds = $startDate->diffInSeconds($now);

        return round(($elapsedSeconds / $totalSeconds) * 100, 1);
    }

    /**
     * Get research job statistics for a corporation
     *
     * @param int $corporationId
     * @return array
     */
    public function getResearchStatistics(int $corporationId): array
    {
        $jobs = $this->getActiveResearchJobs($corporationId);

        return [
            'total_jobs' => $jobs->count(),
            'copying_jobs' => $jobs->where('activity_id', 3)->count(),
            'me_research_jobs' => $jobs->where('activity_id', 4)->count(),
            'te_research_jobs' => $jobs->where('activity_id', 5)->count(),
            'jobs_completing_today' => $jobs->filter(function ($job) {
                return Carbon::parse($job->end_date)->isToday();
            })->count(),
            'jobs_completing_this_week' => $jobs->filter(function ($job) {
                return Carbon::parse($job->end_date)->isBetween(now(), now()->addWeek());
            })->count(),
        ];
    }

    /**
     * Check if a blueprint is currently being researched
     *
     * @param int $corporationId
     * @param int $blueprintItemId
     * @return bool
     */
    public function isBlueprintInResearch(int $corporationId, int $blueprintItemId): bool
    {
        return CorporationIndustryJob::where('corporation_id', $corporationId)
            ->where('blueprint_id', $blueprintItemId)
            ->whereIn('activity_id', [3, 4, 5])
            ->where('status', 'active') // Includes both running and waiting for delivery
            ->exists();
    }
}
