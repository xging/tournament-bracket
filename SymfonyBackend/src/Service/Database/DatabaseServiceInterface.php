<?PHP
namespace App\Service\Database;

use App\Entity\Divisions;
use App\Entity\MatchesHist;
use App\Entity\TeamsMatch;

interface DatabaseServiceInterface
{

     /***** Add methods *****/
    public function addDivision(string $name, int $divisionId): ?Divisions;

    public function addMatchesHist(string $team_1, string $team_2, string $result ): ?MatchesHist;
    public function addTeamMatch(int $division_id, string $fullname, string $shortname, bool $pickedflag, int $result,bool $round1Flag, bool $round2Flag, bool $quarterfinalflag, bool $semifinalflag, bool $bronzemedalflag, bool $grandfinalflag, string $place): ?TeamsMatch;

     /***** Get methods *****/
    public function getDivisionName(int $id): ?string;
    public function getShortNameByPickedFlag(bool $flag, int $divisionId): ?array;
    public function getTeamByPickedFlag(bool $flag): ?array;
    public function getMatchResult(string $shortName): ?int;
    public function getTMResultByShortname(string $shortname): ?Int;

    /***** Set methods *****/
    public function setTMResultByShortname(string $shortname, int $res): ?TeamsMatch;
    public function setMatchResult(string $shortName, string $value): ?TeamsMatch;
    public function setPlayoffFlagByTeamShortName(string $shortName, string $stage ,bool $flag): ?TeamsMatch;
    public function setPlaceByShortName(string $shortName, string $place): ?TeamsMatch;
    public function setPickedFlagByTeamShortName(string $shortName, bool $flag): ?TeamsMatch;

    /***** Other methods *****/
    public function clearTable(string $tableName): void;
    public function clearColumnMatches(string $stage): void;
}