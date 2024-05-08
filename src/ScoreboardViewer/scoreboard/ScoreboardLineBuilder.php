<?php

namespace ScoreboardViewer\scoreboard;

use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class ScoreboardLineBuilder
{
    public string $objective = "";
    public int $index = 0;
    public string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function build(string $identifier): self{
        return new self($identifier);
    }

    /**
     * @param string $objective
     * @return ScoreboardLineBuilder
     */
    public function setObjective(string $objective): self
    {
        $this->objective = $objective;
        return $this;
    }

    /**
     * @param int $index
     * @return ScoreboardLineBuilder
     */
    public function setIndex(int $index): self
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getObjective(): string
    {
        return $this->objective;
    }

    public function update(Player $player, array $arguments): void
    {
        $objective = $this->getObjective();
        foreach ($arguments as $argument => $value){
            $objective = str_replace("{" . $argument . "}", $value, $objective);
        }
        $entry = new ScorePacketEntry();
        $entry->scoreboardId = $this->index;
        $entry->objectiveName = $objective;
        $entry->score = $this->index;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $objective;
        $pack = new SetScorePacket();
        $pack->type = $pack::TYPE_CHANGE;
        $pack->entries[] = $entry;
        $player->getNetworkSession()->sendDataPacket($pack);
    }
}