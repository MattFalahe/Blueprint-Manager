<?php

namespace BlueprintManager\Models;

use Illuminate\Database\Eloquent\Model;

class BlueprintDetectionSettings extends Model
{
    protected $table = 'blueprint_detection_settings';
    
    protected $primaryKey = 'corporation_id';
    
    public $incrementing = false;
    
    protected $fillable = [
        'corporation_id',
        'hangar_divisions',
    ];

    protected $casts = [
        'hangar_divisions' => 'array',
    ];

    /**
     * Get settings for a corporation, or create default
     *
     * @param int $corporationId
     * @return BlueprintDetectionSettings
     */
    public static function getOrCreateDefault(int $corporationId): self
    {
        $settings = self::find($corporationId);

        if (!$settings) {
            $settings = new self();
            $settings->corporation_id = $corporationId;
            $settings->hangar_divisions = [
                'CorpSAG1',
                'CorpSAG2',
                'CorpSAG3',
                'CorpSAG4',
                'CorpSAG5',
                'CorpSAG6',
                'CorpSAG7',
                'AssetSafety',
            ];
        }

        return $settings;
    }

    /**
     * Get enabled hangar divisions as array
     *
     * @return array
     */
    public function getEnabledDivisions(): array
    {
        return $this->hangar_divisions ?? [];
    }

    /**
     * Check if a hangar division is enabled
     *
     * @param string $division
     * @return bool
     */
    public function isDivisionEnabled(string $division): bool
    {
        return in_array($division, $this->getEnabledDivisions());
    }

    /**
     * Get all available hangar divisions
     *
     * @return array
     */
    public static function getAvailableDivisions(): array
    {
        return [
            'CorpSAG1' => 'Division 1',
            'CorpSAG2' => 'Division 2',
            'CorpSAG3' => 'Division 3',
            'CorpSAG4' => 'Division 4',
            'CorpSAG5' => 'Division 5',
            'CorpSAG6' => 'Division 6',
            'CorpSAG7' => 'Division 7',
            'AssetSafety' => 'Asset Safety',
        ];
    }

    /**
     * Get available divisions with actual in-game hangar names
     *
     * @param int $corporationId
     * @return array
     */
    public static function getAvailableDivisionsWithNames(int $corporationId): array
    {
        // Load custom division names from corporation_divisions table
        // Filter for type = 'hangar' to exclude wallet divisions
        $divisions = \DB::table('corporation_divisions')
            ->where('corporation_id', $corporationId)
            ->where('type', 'hangar')
            ->get()
            ->keyBy('division');

        $divisionNames = [];

        // Map division numbers (1-7) to CorpSAG identifiers
        for ($i = 1; $i <= 7; $i++) {
            $corpSagId = "CorpSAG{$i}";
            
            // Check if custom name exists for this division
            if (isset($divisions[$i]) && !empty($divisions[$i]->name)) {
                $divisionNames[$corpSagId] = $divisions[$i]->name;
            } else {
                $divisionNames[$corpSagId] = "Division {$i}";
            }
        }

        // Asset Safety doesn't have a custom name
        $divisionNames['AssetSafety'] = 'Asset Safety';

        return $divisionNames;
    }
}
