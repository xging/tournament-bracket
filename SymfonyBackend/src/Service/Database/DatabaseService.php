<?PHP
namespace App\Service\Database;

use App\Entity\Divisions;
use App\Entity\TeamsMatch;
use App\Entity\MatchesHist;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('DatabaseServiceT')]
class DatabaseService implements DatabaseServiceInterface
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    private const AVAILABLE_STAGES = [
        'semifinal' => ['bronzemedal_flag', 'grandfinal_flag'],
        'quarterfinal' => ['semifinal_flag', 'bronzemedal_flag', 'grandfinal_flag'],
        'round_2' => ['quarterfinal_flag', 'semifinal_flag', 'bronzemedal_flag', 'grandfinal_flag'],
        'round_1' => ['round_2_flag', 'quarterfinal_flag', 'semifinal_flag', 'bronzemedal_flag', 'grandfinal_flag']
    ];

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /***** Add methods *****/
    public function addDivision(string $name, int $divisionId): ?Divisions
    {
        $division = new Divisions();
        $division->setName($name);
        $division->setDivisionId($divisionId);
        $this->em->persist($division);
        $this->em->flush();

        return $division;
    }

    public function addMatchesHist(string $team_1, string $team_2, string $result): ?MatchesHist
    {
        $matchesHist = new MatchesHist();
        $matchesHist->setTeam1($team_1);
        $matchesHist->setTeam2($team_2);
        $matchesHist->setResult($result);
        $this->em->persist($matchesHist);
        $this->em->flush();

        return $matchesHist;
    }

    public function addTeamMatch(int $division_id, string $fullname, string $shortname, bool $pickedflag, int $result, bool $round1Flag, bool $round2Flag, bool $quarterfinalflag, bool $semifinalflag, bool $bronzemedalflag, bool $grandfinalflag, string $place): ?TeamsMatch
    {
        $team = new TeamsMatch();
        $team->setFullname($fullname);
        $team->setShortname($shortname);
        $team->setDivisionId($division_id);
        $team->setPickedFlag($pickedflag);
        $team->setResult($result);
        $team->setRound1Flag($round1Flag);
        $team->setRound2Flag($round2Flag);
        $team->setQuarterfinalFlag($quarterfinalflag);
        $team->setSemifinalFlag($semifinalflag);
        $team->setBronzemedalFlag($bronzemedalflag);
        $team->setGrandfinalFlag($grandfinalflag);
        $team->setPlace($place);

        $this->em->persist($team);
        $this->em->flush();
        return $team;
    }


    /***** Get methods *****/
    public function getDivisionName(int $id): ?string
    {
        $division = $this->em->getRepository(Divisions::class)->findOneBy(['division_id' => $id]);
        return $division ? $division->getName() : null;
    }

    public function getShortNameByPickedFlag(bool $flag, int $divisionId): ?array
    {
        $teamsMatch = $this->em->getRepository(TeamsMatch::class)->findBy([
            'picked_flag' => $flag,
            'division_id' => $divisionId,
        ]);
        $this->logger->warning("Flag here: {$flag}, Division ID: {$divisionId}.");

        if (empty($teamsMatch)) {
            return [];
        }

        $shortNames = array_map(function ($team) {
            return $team->getShortName();
        }, $teamsMatch);

        return $shortNames;
    }

    public function getTeamByPickedFlag(bool $flag): array
    {
        $teams = $this->em->getRepository(TeamsMatch::class)->findBy([
            'picked_flag' => $flag
        ]);

        $this->logger->warning(sprintf("Flag here2: %s.", $flag ? 'true' : 'false'));

        return $teams;
    }

    public function getMatchResult(string $shortName): ?int
    {
        $teams = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortName]);

        return $teams ? $teams->getResult() : null;
    }

    public function getTMResultByShortname(string $shortname): ?int
    {
        $dw = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortname]);
        return $dw ? $dw->getResult() : null;
    }


    /***** Set methods *****/
    public function setTMResultByShortname(string $shortname, int $res): ?TeamsMatch
    {
        $dw = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortname]);
        $dw->setResult($res);
        $this->em->flush();
        return $dw;
    }

    public function setMatchResult(string $shortName, string $value): ?TeamsMatch
    {
        $team = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortName]);
        $this->logger->warning("Team with short name {$shortName}.");
        if ($team) {
            $team->setResult($value);
            $this->em->flush();
            $this->logger->warning("Team with short name {$shortName} found.");
        } else {
            $this->logger->warning("Team with short name {$shortName} not found.");
        }

        return $team;
    }

    public function setPlayoffFlagByTeamShortName(string $shortName, string $stage, bool $flag): ?TeamsMatch
    {
        $team = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortName]);
        if ($team) {
            if ($stage == '1') {
                $team->setRound1Flag($flag);
            } else if ($stage == '2') {
                $team->setRound2Flag($flag);
            } else if ($stage == '3') {
                $team->setQuarterFinalFlag($flag);
            } else if ($stage == '4') {
                $team->setSemiFinalFlag($flag);
            } else if ($stage == '5') {
                $team->setGrandFinalFlag($flag);
            } else if ($stage == '6') {
                $team->setBronzeMedalFlag($flag);
            } else {
                return $team;
            }
            $this->em->flush();
            $this->logger->info("Team with short name {$shortName} found.");
        } else {
            $this->logger->warning("Team with short name {$shortName} not found.");
        }
        return $team;
    }

    public function setPlaceByShortName(string $shortName, string $place): ?TeamsMatch
    {
        $team = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortName]);
        if ($team) {
            $team->setPlace($place);
            $this->em->flush();
        }
        return $team;
    }

    public function setPickedFlagByTeamShortName(string $shortName, bool $flag): ?TeamsMatch
    {
        $team = $this->em->getRepository(TeamsMatch::class)->findOneBy(['shortname' => $shortName]);
        $this->logger->warning("Team with short name2 {$shortName}.");
        if ($team) {
            $team->setPickedFlag($flag);
            $this->em->flush();
            $this->logger->warning("Team with short name {$shortName} found.");
        } else {
            $this->logger->warning("Team with short name {$shortName} not found.");
        }

        return $team;
    }

    /***** Other methods *****/
    public function clearTable(string $tableName): void
    {
        $conn = $this->em->getConnection();

        if ($tableName === 'divisions') {
            $conn->executeStatement('DELETE FROM ' . $tableName);
            $conn->executeStatement('ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1');
        } else {
            $conn->executeStatement('TRUNCATE TABLE ' . $tableName);
        }
    }

    public function clearColumnMatches(string $stage): void
    {
        $conn = $this->em->getConnection();
        $stageFlagName=$stage.'_flag';
        $teamCount = $this->em->getRepository(TeamsMatch::class)
            ->count([$stageFlagName => true]);

        $filteredStages = [];
        
        switch (true) {
            case $teamCount == 4:
                $filteredStages = [self::AVAILABLE_STAGES['semifinal']];
                break;
            case $teamCount > 4 && $teamCount <= 8:
                $filteredStages = [self::AVAILABLE_STAGES['quarterfinal']];
                break;
            case $teamCount > 8 && $teamCount <= 16:
                $filteredStages = [self::AVAILABLE_STAGES['round_2']];
                break;
            case $teamCount > 16 && $teamCount <= 32:
                $filteredStages = [self::AVAILABLE_STAGES['round_1']];
                break;
        }

        $sql = 'UPDATE teams_match SET place = :value where '.$stageFlagName.' = 1';
        $conn->executeStatement($sql, ['value' => 0]);

        foreach ($filteredStages as $stageName) {
            foreach ($stageName as $stagex) {
                $sql = 'UPDATE teams_match SET ' . $stagex . ' = :value';
                $conn->executeStatement($sql, ['value' => 0]);
            }
        }
    }

}
