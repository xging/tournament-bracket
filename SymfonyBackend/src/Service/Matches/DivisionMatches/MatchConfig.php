<?php
namespace App\Service\Matches\DivisionMatches;

class MatchConfig
{
    public const AVAILABLE_TEAM_TYPE = ['list', 'random'];
    public const AVAILABLE_MATCH_TYPE = ['all', 'single'];
    public const AVAILABLE_DIV_NUMS = [1, 2, 3, 4, 5, 6, 7, 8];
    public const AVAILABLE_DIV_ID = ['all', '1', '2','3', '4', '5', '6', '7', '8'];
    

    public static function getAvailableTeamType(): mixed
    {
        return self::AVAILABLE_TEAM_TYPE ?? '';
    }

    public static function getAvailableMatchType(): mixed
    {
        return self::AVAILABLE_MATCH_TYPE ?? '';
    }

    public static function getAvailableDivisionNumber(): mixed
    {
        return self::AVAILABLE_DIV_NUMS ?? 999;
    }
    
    public static function getAvailableDivisionId(): mixed {
        return self::AVAILABLE_DIV_ID ?? '';
    }
    
}

