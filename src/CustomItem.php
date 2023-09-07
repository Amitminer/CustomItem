<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use AmitxD\CustomItem\Manager\FormManager;
use AmitxD\CustomItem\Utils\Utils;
use AmitxD\CustomItem\Utils\EnchantmentIds;
use AmitxD\CustomItem\Items\CustomItems;
use pocketmine\player\Player;
use Symfony\Component\Filesystem\Path;
use pocketmine\resourcepacks\ZippedResourcePack;
use customiesdevs\customies\Customies;

class CustomItem extends PluginBase{

    protected static $instance;

    protected function onLoad(): void {
        self::$instance = $this;
        $this->getLogger()->info("§aEnabled CustomItem!");
    }

    protected function onEnable(): void {
        $this->checkDependencies();
        $this->loadResources();
        $this->registerEnchantments();
        $this->runEvents();
    }

    private function checkDependencies(): void {
        if (class_exists(Customies::class)) {
            /** @phpstan-ignore-next-line */
            CustomItems::loadItems();
        } else {
            $this->getLogger()->error("Customies plugin not found. Make sure the Customies is installed. You can download it from https://poggit.pmmp.io/p/Customies");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    private function loadResources(): void {
        $this->saveResource("CustomItem.mcpack");
        $rpManager = $this->getServer()->getResourcePackManager();
        $rpManager->setResourceStack($rpManager->getResourceStack() + [new ZippedResourcePack(Path::join($this->getDataFolder(), "CustomItem.mcpack"))]);
        ($serverForceResources = new \ReflectionProperty($rpManager, "serverForceResources"))->setAccessible(true);
        $serverForceResources->setValue($rpManager, true);
    }

    public function registerEnchantments(): void {
        $enchantments = ["Teleportation",
            "Freezing",
            "TimeStopper",
            "Lightning"];
        $ids = [EnchantmentIds::FREEZING,
            EnchantmentIds::TELEPORTATION,
            EnchantmentIds::TIMESTOPPER,
            EnchantmentIds::LIGHTNING];
        foreach ($ids as $id)
        foreach ($enchantments as $enchantment) {
            Utils::createEnchantment($enchantment, $id);
        }

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
        if (!$sender instanceof Player) {
            return false;
        }
        switch ($command->getName()) {
            case "customitems":
                FormManager::displayCustomItemsForm($sender);
                break;
        }
        return true;
    }


    private function runEvents(): void {
     //   $this->callEvent("WeaponsEvents");
      //  $this->callEvent("TeleportationSword");
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
    public static function getInstance(): self {
        return self::$instance;
    }
}