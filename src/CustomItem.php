<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use AmitxD\CustomItem\Manager\FormManager;

class CustomItem extends PluginBase {

    public function onLoad(): void {
        $this->getLogger()->info("§aEnabled CustomItem!");
    }

    public function onEnable(): void {
       // $this->saveDefaultConfig();
        $this->runEvents();
    }

    /**
     * Executes a command on the command sender's behalf.
     * @param CommandSender $sender The sender of the command.
     * @param Command $command The command that was executed.
     * @param string $label The alias or label used to execute the command.
     * @param array $args An array of arguments passed to the command.
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "customitems":
                FormManager::displayCustomItemsForm($sender);
                break;
        }
        return true;
    }
    
    private function runEvents(): void {
        $this->callEvent("FrezzingSword");
        $this->callEvent("TeleportationSword");
        $this->callEvent("TimeController");
    }
    /**
     * Call a custom event dynamically based on the event name.
     * @param string $eventName The name of the event to call.
     */
    private function callEvent(string $eventName): void {
        $eventClass = "\\AmitxD\\CustomItem\\Events\\" . $eventName;
        $eventListener = new $eventClass($this);
        $this->getServer()->getPluginManager()->registerEvents($eventListener, $this);
    }
}
