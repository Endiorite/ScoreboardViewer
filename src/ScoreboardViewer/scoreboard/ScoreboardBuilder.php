<?php

namespace ScoreboardViewer\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\player\Player;

class ScoreboardBuilder
{
    private string $displayName = "";
    /**
     * @var ScoreboardLineBuilder[]
     */
    private array $lines = [];
    private ?Player $player = null;
    private string $identifier;
    private array $arguments = [];
    private bool $isCurrent = false;

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

    public function addLine(ScoreboardLineBuilder $builder): self{
        $this->lines[$builder->getIdentifier()] = $builder->setObjective($this->identifier);
        return $this;
    }

    public function updateArguments(string $arguments, mixed $newValue, bool $updateLines = true): void
    {
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
        if (isset($this->lines[$lineIdentifier]) && $this->isCurrent){
            $line = $this->lines[$lineIdentifier];
            if (!is_null($player = $this->getPlayer())){
                $line->update($player, $this->arguments);
            }
        }
    }

    public function init(): void{
        $pack = new SetDisplayObjectivePacket();
        $pack->displaySlot = "sidebar";
        $pack->objectiveName = $this->identifier;
        $pack->displayName = $this->getDisplayName();
        $pack->criteriaName = "dummy";
        $pack->sortOrder = 0;
        $this->player?->getNetworkSession()->sendDataPacket($pack);
        $this->updateLines();
    }

    public function close(): void{
        $pack = new RemoveObjectivePacket();
        $pack->objectiveName = $this->identifier;
        $this->player?->getNetworkSession()->sendDataPacket($pack);

        if (!is_null($this->player)){
            foreach ($this->lines as $line){
                $line->close($this->player);
            }
        }
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

    /**
     * @param bool $isCurrent
     */
    public function setIsCurrent(bool $isCurrent): void
    {
        $this->isCurrent = $isCurrent;
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }
}