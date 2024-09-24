<?php

namespace App\Service\Matches\PlayoffMatches;

class PlayoffConfig
{
    public const POINTS_PER_STAGE = [
        'round_1' => 10,
        'round_2' => 100,
        'quarterfinal' => 1000,
        'semifinal' => 10000,
        'bronzemedal' => 100000,
        'grandfinal' => 500000
    ];

    public const POSSIBLE_SCORES = [
        'default' => ['2:0', '2:1', '1:2', '0:2'],
        'grandfinal' => ['3:0', '3:1', '3:2', '2:3', '1:3', '0:3']
    ];


    private const STAGE_PLACEMENTS = [
        'grandfinal' => [1, 2],
        'bronzemedal' => [3, 4],
        'quarterfinal' => '5-8',
        'round_2' => '9-',
        'round_1' => '17-'
    ];
    private const STAGES_LIST = ['round_1', 'round_2', 'quarterfinal', 'semifinal', 'bronzemedal', 'grandfinal','all'];

    public static function getPointsForStage(string $stage): int
    {
        return self::POINTS_PER_STAGE[$stage] ?? 100;
    }

    public static function getPossibleScoresForStage(string $stage): array
    {
        return self::POSSIBLE_SCORES[$stage] ?? self::POSSIBLE_SCORES['default'];
    }

    public static function getStagePlacements($stage): mixed{
        return self::STAGE_PLACEMENTS[$stage] ?? 999;
    }
    
    public static function getStagesList(): array {
        return self::STAGES_LIST ?? ['default'];
    }
    
}

