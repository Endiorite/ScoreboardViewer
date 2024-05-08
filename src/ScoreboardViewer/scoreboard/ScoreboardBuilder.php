<?php

namespace ScoreboardViewer\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\player\Player;

class ScoreboardBuilder
{
    private string $displayName = "";
    private string $objectiveName = "";
    private array $lines = [];
    private ?Player $player = null;
    private string $identifier;
    private array $arguments = [];

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function build(string $identifier): self{
        return new self($identifier);
    }

    public function setDisplayName(string $displayName): self{
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $objectiveName
     * @return ScoreboardBuilder
     */
    public function setObjectiveName(string $objectiveName): self
    {
        $this->objectiveName = $objectiveName;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectiveName(): string
    {
        return $this->objectiveName;
    }

    public function addLine(ScoreboardLineBuilder $builder): self{
        $this->lines[$builder->getIdentifier()] = $builder;
        return $this;
    }

    public function updateArguments(string $arguments, mixed $newValue, bool $updateLines = true){
        $this->arguments[$arguments] = $newValue;
        if ($updateLines){
            $this->updateLines();
        }
    }

    public function updateLines(): void{
        foreach ($this->lines as $identifier => $line){
            $this->updateLine($identifier);
        }
    }

    public function updateLine(string $lineIdentifier): void{
        if (isset($this->lines[$lineIdentifier])){
            $line = $this->lines[$lineIdentifier];
            if (!is_null($player = $this->getPlayer())){
                $line->update($player, $this->arguments);
            }
        }
    }

    public function init(): void{
        $pack = new SetDisplayObjectivePacket();
        $pack->displaySlot = "sidebar";
        $pack->objectiveName = $this->getObjectiveName();
        $pack->displayName = $this->getDisplayName();
        $pack->criteriaName = "dummy";
        $pack->sortOrder = 0;
        $this->player?->getNetworkSession()->sendDataPacket($pack);
    }

    public function close(): void{
        $pack = new RemoveObjectivePacket();
        $pack->objectiveName = $this->objectiveName;
        $this->player?->getNetworkSession()->sendDataPacket($pack);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}