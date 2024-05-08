<?php

namespace ScoreboardViewer\scoreboard;

use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class ScoreboardLineBuilder
{
    public string $content = "";
    public int $index = 0;
    public string $identifier;
    private string $objective = "";

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function build(string $identifier): self{
        return new self($identifier);
    }

    public function setObjective(string $objective): self{
        $this->objective = $objective;
        return $this;
    }

    /**
     * @param string $content
     * @return ScoreboardLineBuilder
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
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
     */public function getContent(): string
{
    return $this->content;
}

    public function update(Player $player, array $arguments): void
    {
        $content = $this->getContent();
        foreach ($arguments as $argument => $value){
            $content = str_replace("{" . $argument . "}", $value, $content);
        }
        $entry = new ScorePacketEntry();
        $entry->scoreboardId = $this->index;
        $entry->objectiveName = $this->objective;
        $entry->score = $this->index;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $content;
        $pack = new SetScorePacket();
        $pack->type = $pack::TYPE_CHANGE;
        $pack->entries[] = $entry;
        $player->getNetworkSession()->sendDataPacket($pack);
    }

    public function close(Player $player): void{
        $entry = new ScorePacketEntry();
        $entry->scoreboardId = $this->index;
        $entry->objectiveName = $this->objective;
        $entry->score = $this->index;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $pack = new SetScorePacket();
        $pack->type = $pack::TYPE_REMOVE;
        $pack->entries[] = $entry;
        $player->getNetworkSession()->sendDataPacket($pack);
    }
}