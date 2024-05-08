<?php

namespace ScoreboardViewer;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ScoreboardViewer\scoreboard\ScoreboardBuilder;
use ScoreboardViewer\scoreboard\ScoreboardLineBuilder;

class ViewerManager
{
    use SingletonTrait;

    protected array $scoreboards = [];
    /**
     * @var PlayerSession[]
     */
    protected array $players = [];
    protected bool $registered = false;

    public function example(): void{
        $scoreboard = ScoreboardBuilder::build("new_scoreboard")
            ->setDisplayName("Scoreboard")
            ->addLine(ScoreboardLineBuilder::build("line1")
                ->setIndex(0) //position of line
                ->setContent("Line 1"))
            ->addLine(ScoreboardLineBuilder::build("line2")
                ->setIndex(1)
                ->setContent("Player connected: {connected}"));

        ViewerManager::getInstance()->registerScoreboard($scoreboard); //register new scoreboard
    }

    public function register(Plugin $plugin): void{
        if (!$this->registered){
            $this->registered = true;
            Server::getInstance()->getPluginManager()->registerEvents(new class() implements Listener{
                public function onQuit(PlayerQuitEvent $event){
                    ViewerManager::getInstance()->closeSession($event->getPlayer());
                }
            }, $plugin);
        }
    }

    public function updateArgumentsForAll(string $scoreboardIdentifier, string $argument, string $value, bool $updateLines = true): void{
        foreach ($this->players as $player){
            $player->getScoreboard($scoreboardIdentifier)?->updateArguments($argument, $value, $updateLines);
        }
    }

    public function closeSession(Player $player): void{
        if (isset($this->players[$player->getName()])){
            unset($this->players[$player->getName()]);
        }
    }

    public function registerScoreboard(ScoreboardBuilder $scoreboardBuilder): void{
        $this->scoreboards[$scoreboardBuilder->getIdentifier()] = $scoreboardBuilder;
    }

    public function showScoreboard(Player $player, string $identifier): ?ScoreboardBuilder{
        $session = $this->getSession($player);
        $scoreboard = $session->getScoreboard($identifier);
        $scoreboard->init();
        return $scoreboard;
    }

    public function closeScoreboard(Player $player, string $identifier): void{
        $session = $this->getSession($player);
        $session->getScoreboard($identifier)->close();
    }

    public function getCurrent(Player $player): ?ScoreboardBuilder{
        return $this->getSession($player)->getCurrent();
    }

    public function setCurrent(Player $player, string $identifier): ?ScoreboardBuilder{
        $session = $this->getSession($player);
        $session->setCurrent($identifier);
        return $session->getCurrent();
    }

    public function getSession(Player $player): PlayerSession{
        if (!isset($this->players[$player->getName()])){
            $this->players[$player->getName()] = PlayerSession::init($player, $this->scoreboards);
        }
        return $this->players[$player->getName()];
    }

}