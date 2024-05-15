<?php

namespace ScoreboardViewer;

use pocketmine\player\Player;
use ScoreboardViewer\scoreboard\ScoreboardBuilder;

class PlayerSession
{

    private ?Player $player;

    /**
     * @var ScoreboardBuilder[]
     */
    public array $scoreboards = [];
    private ?string $currentIdentifier = null;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @param Player $player
     * @param ScoreboardBuilder[] $scoreboards
     * @return $this
     */
    public static function init(Player $player, array $scoreboards): self{
        $self = new self($player);
        $sb = [];
        foreach ($scoreboards as $scoreboard){
            $scoreboard->setPlayer($player);
            $sb[$scoreboard->getIdentifier()] = $scoreboard;
        }
        $self->setScoreboards($sb);
        return $self;
    }

    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    /**
     * @return string|null
     */
    public function getCurrent(): ?ScoreboardBuilder
    {
        return $this->scoreboards[$this->currentIdentifier] ?? null;
    }

    public function getScoreboard(string $identifier): ?ScoreboardBuilder{
        return $this->scoreboards[$identifier] ?? null;
    }

    public function setCurrent(string $identifier): void{
        if(!is_null($current = $this->getCurrent())){
            $current->close();
            $current->setIsCurrent(false);
        }
        $this->currentIdentifier = $identifier;
        $this->getCurrent()->setIsCurrent(true);
        $this->getCurrent()->init();
    }

    /**
     * @param array $scoreboards
     */
    public function setScoreboards(array $scoreboards): void
    {
        $this->scoreboards = $scoreboards;
    }
}