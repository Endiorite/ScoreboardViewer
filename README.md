
In the `onEnable` or `onLoad` of your plugin, register the new Scoreboard
```php
$scoreboard = ScoreboardBuilder::build("new_scoreboard")
            ->setDisplayName("Scoreboard")
            ->setObjectiveName("new_scoreboard")
            ->addLine(ScoreboardLineBuilder::build("line1")
                ->setIndex(0) //position of line
                ->setObjective("Line 1"))
            ->addLine(ScoreboardLineBuilder::build("line2")
                ->setIndex(1)
                ->setObjective("Player connected: {connected}")); //with argument
        
ViewerManager::getInstance()->registerScoreboard($scoreboard); //register new scoreboard
```

and you can modify argument `{connected}` when a player join
```php
public function onJoin(PlayerJoinEvent $event): void{
    $player = $event->getPlayer();
    ViewerManager::getInstance()->setCurrent($player, "new_scoreboard"); //set to player the scoreboard
    
    ViewerManager::getInstance()->updateArgumentsForAll(
    "new_scoreboard", //scoreboard identifier
    "connected", //argument without the {}
    count(Server::getInstance()->getOnlinePlayers()) //value
    );
}
```

get current scoreboard of a player and update and update his arguments
```php
$currentScoreboard = ViewerManager::getInstance()->getCurrent($player);
$currentScoreboard->updateArguments("connected", -1); //modify the arguments only to this player and are current scoreboard
```

create a new scoreboard and set it as current scoreboard
```php
$scoreboard = ScoreboardBuilder::build("second_scoreboard")
            ->setDisplayName("Scoreboard 2")
            ->setObjectiveName("second_scoreboard")
            ->addLine(ScoreboardLineBuilder::build("line1")
                ->setIndex(0) //position of line
                ->setObjective("First Line of Second Scoreboard"))
        
ViewerManager::getInstance()->registerScoreboard($scoreboard); //register new scoreboard

/** and you can easily change current scoreboard
And no need to redefine these arguments!**/
ViewerManager::getInstance()->setCurrent($player, "second_scoreboard"); //we switch to the scoreboard that we have just created
ViewerManager::getInstance()->setCurrent($player, "new_scoreboard"); //and here on the old scoreboard
```
