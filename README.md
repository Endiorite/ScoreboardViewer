
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
    ViewerManager::getInstance()->updateArgumentsForAll(
    "new_scoreboard", //scoreboard identifier
    "connected", //argument without the {}
    count(Server::getInstance()->getOnlinePlayers()) //value
    );
}
```

