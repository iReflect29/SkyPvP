<?php

namespace Fludixx\SkyPvP;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\utils\Terminal;
use pocketmine\utils\Color;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\utils\TextFormat as f;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\entity\Item as ItemEntity;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
class Main extends PluginBase implements Listener{

    public $prefix = f::AQUA . "Sky". f::GREEN ."PvP" . f::GRAY . " | " . f::WHITE;
    public $sucess = f::GREEN;
    public $failure = f::RED;
    
    public function StartInventar($spieler) {
        $spielername = $spieler->getName();
        $color = Item::get(299, 80, 1); //1 HIT ARMOR
		$protection = Enchantment::getEnchantment(0);
        $protection = new EnchantmentInstance($protection, 5);
		$color->addEnchantment($protection);
        if($spieler->getArmorInventory()->getChestplate()->isNull() == true){
            $spieler->getArmorInventory()->setChestplate($color);
        }
        // INVENTORY
        $back = Item::get(351, 1, 1);
        $back->setCustomName(f::RED . "Zurück");
        $random = Item::get(76, 0, 1);
        $random->setCustomName(f::GOLD . "Last Kit");
        $stats = Item::get(397, 0, 1);
        $stats->setCustomName(f::GOLD . "Stats");
        $kits = Item::get(299, 0, 1);
        $kits->setCustomName(f::GOLD . "Kits");
        $inventar = $spieler->getInventory();
        $this->clearHotbar($spieler);
        $inventar->setItem(0, $kits);
        $inventar->setItem(1, $stats);
        $inventar->setItem(8, $random);
    }
    public function KitInventar($spieler) {
        $spielername = $spieler->getName();
        $inventar = $spieler->getInventory();
        $this->clearHotbar($spieler);
        // KITS
        $back = Item::get(351, 1, 1);
        $back->setCustomName(f::RED . "Zurück");
        $knight = Item::get(303, 0, 1);
        $knight->setCustomName(f::GOLD . "Knight");
        $iron = Item::get(307, 0, 1);
        $iron->setCustomName(f::GOLD . "Iron");
        $diamond = Item::get(311, 0, 1);
        $diamond->setCustomName(f::GOLD . "Diamond");
        $glider = Item::get(288, 0, 1);
        $glider->setCustomName(f::GOLD . "Jumper");
        $spammer = Item::get(261, 0, 1);
        $spammer->setCustomName(f::GOLD . "Spammer");
        $tntmaster = Item::get(280, 0, 1);
        $tntmaster->setCustomName(f::GOLD . "Knockback");
        $inventar->setItem(0, $knight);
        $inventar->setItem(1, $iron);
        $inventar->setItem(2, $diamond);
        $inventar->setItem(3, $glider);
        $inventar->setItem(4, $spammer);
        $inventar->setItem(5, $tntmaster);
        $inventar->setItem(8, $back);
    }
    public function clearHotbar($spieler) {
        $spielername = $spieler->getName();
        $inventar = $spieler->getInventory();
        $air = Item::get(0, 0, 0);
        $inventar->setItem(0, $air);
        $inventar->setItem(1, $air);
        $inventar->setItem(2, $air);
        $inventar->setItem(3, $air);
        $inventar->setItem(4, $air);
        $inventar->setItem(5, $air);
        $inventar->setItem(6, $air);
        $inventar->setItem(7, $air);
        $inventar->setItem(8, $air);
    }
    
    public function onEnable()
	{
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info($this->prefix . f::WHITE . f::AQUA . "SkyPvP by Fludixx" . f::GREEN .  " wurde Erfolgreich Aktiviert!");
        $this->getLogger()->info(f::RED . "Be sure to have EloSystem by Fludixx installed!");
        $this->getLogger()->info(f::RED . "Without this Plugin SkyPvP won't work properly! " . f::AQUA . "https://github.com/Fludixx/EloSystem");
	}
    
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $event->getPlayer()->getName();
     $kconfig = new Config("/cloud/users/".$name.".yml", Config::YAML);
     if(!$kconfig->get("skykills") && !$kconfig->get("skytode")){
        $kconfig->set("skykills", 1);
        $kconfig->set("skytode", 1);
        $kconfig->save();
        $this->StartInventar($player);
     }
    }
    public function onRespawn(PlayerRespawnEvent $event){
        $player = $event->getPlayer();
        $this->StartInventar($player);
    }

    // PROCESSING AREA 
    public function onInteract(PlayerInteractEvent $event) {
    	$player = $event->getPlayer();
        $playername = $player->getName();
        $nametag = $player->getName();
        $inventar = $player->getInventory();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getCustomName() == f::RED . "Zurück") {
            $this->StartInventar($player);
        }
        if ($item->getCustomName() == f::GOLD . "Kits") {
            $this->KitInventar($player);
        }
        if ($item->getCustomName() == f::GOLD . "Knight") {
            $this->clearHotbar($player);
            $schwert = Item::get(267, 0, 1);
            $gapple = Item::get(322, 0, 2);
            $ah = Item::get(306, 0, 1);
            $ac = Item::get(303, 0, 1);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
		    $ac->addEnchantment($protection);
            $al = Item::get(308, 0, 1);
            $ab = Item::get(309, 0, 1);
            $inventar->setItem(0, $schwert);
            $inventar->setItem(1, $gapple);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-4);
            $elo->save();
            $elonow = $currentelo - 4;
            $player->sendMessage($this->prefix . "Knight Kit: " . f::RED . "-4 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "knight");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Iron") {
            $this->clearHotbar($player);
            $schwert = Item::get(267, 0, 1);
            $gapple = Item::get(322, 0, 3);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
            $ah = Item::get(306, 0, 1);
            $ah->addEnchantment($protection);
            $ac = Item::get(307, 0, 1);
            $protection2 = Enchantment::getEnchantment(0);
            $protection2 = new EnchantmentInstance($protection2, 2);
		    $ac->addEnchantment($protection2);
            $al = Item::get(308, 0, 1);
            $al->addEnchantment($protection);
            $ab = Item::get(309, 0, 1);
            $ab->addEnchantment($protection);
            $inventar->setItem(0, $schwert);
            $inventar->setItem(1, $gapple);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-10);
            $elo->save();
            $elonow = $currentelo - 10;
            $player->sendMessage($this->prefix . "Iron Kit: " . f::RED . "-10 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "iron");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Diamond") {
            $this->clearHotbar($player);
            $schwert = Item::get(276, 0, 1);
            $gapple = Item::get(322, 0, 3);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
            $ah = Item::get(306, 0, 1);
            $ah->addEnchantment($protection);
            $ac = Item::get(311, 0, 1);
            $protection2 = Enchantment::getEnchantment(0);
            $protection2 = new EnchantmentInstance($protection2, 2);
		    $ac->addEnchantment($protection2);
            $al = Item::get(312, 0, 1);
            $al->addEnchantment($protection2);
            $ab = Item::get(309, 0, 1);
            $ab->addEnchantment($protection);
            $inventar->setItem(0, $schwert);
            $inventar->setItem(1, $gapple);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-22);
            $elo->save();
            $elonow = $currentelo - 22;
            $player->sendMessage($this->prefix . "Diamond Kit: " . f::RED . "-22 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "diamond");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Jumper") {
            $this->clearHotbar($player);
            $schwert = Item::get(267, 0, 1);
            $gapple = Item::get(322, 0, 3);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
            $ah = Item::get(306, 0, 1);
            $ah->addEnchantment($protection);
            $ac = Item::get(307, 0, 1);
            $protection2 = Enchantment::getEnchantment(0);
            $protection2 = new EnchantmentInstance($protection2, 2);
		    $ac->addEnchantment($protection);
            $al = Item::get(308, 0, 1);
            $al->addEnchantment($protection);
            $feather2 = Enchantment::getEnchantment(2);
            $feather2 = new EnchantmentInstance($feather2, 4);
            $ab = Item::get(313, 0, 1);
            $ab->addEnchantment($protection2);
            $ab->addEnchantment($feather2);
            $inventar->setItem(0, $schwert);
            $inventar->setItem(1, $gapple);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $effect = Effect::getEffect(8);
            $player->addEffect(new EffectInstance($effect, 9999, 1));
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-14);
            $elo->save();
            $elonow = $currentelo - 14;
            $player->sendMessage($this->prefix . "Jumper Kit: " . f::RED . "-14 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "jumper");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Spammer") {
            $this->clearHotbar($player);
            $schwert = Item::get(267, 0, 1);
            $bow = Item::get(261, 0, 1);
            $arrows = Item::get(262, 0, 64);
            $gapple = Item::get(322, 0, 3);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
            $feather2 = Enchantment::getEnchantment(4);
            $feather2 = new EnchantmentInstance($feather2, 4);
            $ah = Item::get(310, 0, 1);
            $ah->addEnchantment($protection);
            $ah->addEnchantment($feather2);
            $ac = Item::get(307, 0, 1);
            $protection2 = Enchantment::getEnchantment(0);
            $protection2 = new EnchantmentInstance($protection2, 2);
		    $ac->addEnchantment($protection);
            $al = Item::get(308, 0, 1);
            $al->addEnchantment($protection);
            $ab = Item::get(309, 0, 1);
            $ab->addEnchantment($protection2);
            $ab->addEnchantment($feather2);
            $inventar->setItem(0, $schwert);
            $inventar->setItem(1, $bow);
            $inventar->setItem(2, $gapple);
            $inventar->setItem(8, $arrows);
            $inventar->setItem(7, $arrows);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-14);
            $elo->save();
            $elonow = $currentelo - 14;
            $player->sendMessage($this->prefix . "Spammer Kit: " . f::RED . "-14 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "spammer");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Knockback") {
            $this->clearHotbar($player);
            $schwert = Item::get(267, 0, 1);
            $gapple = Item::get(322, 0, 2);
            $stick = Item::get(280, 0, 1);
            $ah = Item::get(302, 0, 1);
            $ac = Item::get(307, 0, 1);
            $protection = Enchantment::getEnchantment(0);
            $protection = new EnchantmentInstance($protection, 1);
		    $ac->addEnchantment($protection);
            $al = Item::get(304, 0, 1);
            $ab = Item::get(305, 0, 1);
            $inventar->setItem(0, $stick);
            $inventar->setItem(1, $schwert);
            $inventar->setItem(2, $gapple);
            $player->getArmorInventory()->setHelmet($ah);
            $player->getArmorInventory()->setChestplate($ac);
            $player->getArmorInventory()->setLeggings($al);
            $player->getArmorInventory()->setBoots($ab);
            $elo = new Config("/cloud/elo/".$nametag.".yml", Config::YAML);
            $currentelo = $elo->get("elo");
            $elo->set("elo", $currentelo-6);
            $elo->save();
            $elonow = $currentelo - 6;
            $player->sendMessage($this->prefix . "Kockback Kit: " . f::RED . "-6 Elo " . f::WHITE . "(" . f::GOLD . $elonow . f::WHITE . ")");
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lkit->set("lkit", "knockback");
            $lkit->save();
        }
        if ($item->getCustomName() == f::GOLD . "Stats") {
                $playername = $player->getName();
                $stats = new Config("/cloud/users/".$playername.".yml", Config::YAML);
                $eloc = new Config("/cloud/elo/".$playername.".yml", Config::YAML);
                $elo = $eloc->get("elo");
                $tode = $stats->get("skytode");
                $kills = $stats->get("skykills");
                $kd = $kills / $tode;
                $player->sendMessage($this->prefix . "Kills: " . f::GREEN . "$kills");
                $player->sendMessage($this->prefix . "Tode: " . f::GREEN . "$tode");
                $player->sendMessage($this->prefix . "KD: " . f::GREEN . "$kd");
                $player->sendMessage($this->prefix . "Elo: " . f::GREEN . "$elo");
                return true;
        }
        if ($item->getCustomName() == f::GOLD . "Last Kit") {
            $lkit = new Config("/cloud/users/".$playername.".yml", Config::YAML);
            $lastkit = $lkit->get("lkit");
            if(!$lastkit) {
                $player->sendMessage($this->prefix . f::RED . "Dein letztes Kit konnte nicht gefunden werden!");
                return false;
            }
            $this->clearHotbar($player);
            $back = Item::get(351, 1, 1);
        $back->setCustomName(f::RED . "Zurück");
        $knight = Item::get(303, 0, 1);
        $knight->setCustomName(f::GOLD . "Knight");
        $iron = Item::get(307, 0, 1);
        $iron->setCustomName(f::GOLD . "Iron");
        $diamond = Item::get(311, 0, 1);
        $diamond->setCustomName(f::GOLD . "Diamond");
        $glider = Item::get(288, 0, 1);
        $glider->setCustomName(f::GOLD . "Jumper");
        $spammer = Item::get(261, 0, 1);
        $spammer->setCustomName(f::GOLD . "Spammer");
        $tntmaster = Item::get(280, 0, 1);
        $tntmaster->setCustomName(f::GOLD . "Knockback");
            if($lastkit == "knight") {
                $inventar->setItem(0, $knight);
                $inventar->setItem(8, $back);
            } elseif($lastkit == "iron") {
                $inventar->setItem(0, $iron);
                $inventar->setItem(8, $back);
            } elseif($lastkit == "diamond") {
                $inventar->setItem(0, $diamond);
                $inventar->setItem(8, $back);
            } elseif($lastkit == "jumper") {
                $inventar->setItem(0, $glider);
                $inventar->setItem(8, $back);
            } elseif($lastkit == "spammer") {
                $inventar->setItem(0, $spammer);
                $inventar->setItem(8, $back);
            } elseif($lastkit == "knockback") {
                $inventar->setItem(0, $tntmaster);
                $inventar->setItem(8, $back);
            }
        }
    }
    public function onEntityDamage(EntityDamageEvent $event){
        if($event->getCause() == EntityDamageEvent::CAUSE_FALL){
            $event->setCancelled();
        }elseif($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            $entity = $event->getEntity();
            if($damager instanceof Player && $entity instanceof Player){
                $damagerinv = $damager->getInventory();
                $iteminhand = $damagerinv->getItemInHand()->getId();
                if($iteminhand == 280) {
                $event->setKnockBack(1);
                $event->setDamage(0);
                }
            }
        }
    }
    public function onDeath(PlayerDeathEvent $event){
        $loser = $event->getPlayer();
        $losername = $loser->getName();
        $cause = $loser->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
        $winner = $cause->getDamager();
        $winnername = $winner->getName();
        $effect = Effect::getEffect(10);
        $winner->addEffect(new EffectInstance($effect, 5, 1));
        // KILLS
        $kills = new Config("/cloud/users/".$winnername.".yml", Config::YAML);
        $pkills = $kills->get("skykills");
        $killsnow = $pkills+1;
        $kills->set("skykills", $killsnow);
        $kills->save();
        // TODE
        $tode = new Config("/cloud/users/".$losername.".yml", Config::YAML);
        $ptode = $tode->get("skytode");
        $todenow = $ptode+1;
        $tode->set("skytode", $todenow);
        $tode->save();
        // DROPS
        $kopf = Item::get(397, 0, 1);
        $kopf->setCustomName(f::AQUA . "$losername's" . f::GREEN . " Kopf");
        $drops = $event->getDrops();
        array_push($drops, $kopf);
        $event->setDrops($drops);
            }
        }
    // SOME COMMANDS
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {
        $name = $sender->getName();
        if ($command->getName() == "kits") {
            $sender->sendMessage($this->prefix . "Wähle ein Kit aus.");
            $this->KitInventar($sender);
            return true;
        }
        if ($command->getName() == "stats") {
            if(!empty($args['0'])){
                $player = $this->getServer()->getPlayer($args['0']);
                $playername = $player->getName();
                $stats = new Config("/cloud/users/".$playername.".yml", Config::YAML);
                $eloc = new Config("/cloud/elo/".$playername.".yml", Config::YAML);
                $elo = $eloc->get("elo");
                $tode = $stats->get("skytode");
                $kills = $stats->get("skykills");
                $kd = $kills / $tode;
                $sender->sendMessage($this->prefix . "Kills: " . f::GREEN . "$kills");
                $sender->sendMessage($this->prefix . "Tode: " . f::GREEN . "$tode");
                $sender->sendMessage($this->prefix . "KD: " . f::GREEN . "$kd");
                $sender->sendMessage($this->prefix . "Elo: " . f::GREEN . "$elo");
                return true;
            } else {
                $player = $sender;
                $playername = $player->getName();
                $stats = new Config("/cloud/users/".$playername.".yml", Config::YAML);
                $eloc = new Config("/cloud/elo/".$playername.".yml", Config::YAML);
                $elo = $eloc->get("elo");
                $tode = $stats->get("skytode");
                $kills = $stats->get("skykills");
                $kd = $kills / $tode;
                $sender->sendMessage($this->prefix . "Kills: " . f::GREEN . "$kills");
                $sender->sendMessage($this->prefix . "Tode: " . f::GREEN . "$tode");
                $sender->sendMessage($this->prefix . "KD: " . f::GREEN . "$kd");
                $sender->sendMessage($this->prefix . "Elo: " . f::GREEN . "$elo");
                return true;
            }
        }
}
    public function onPlace(BlockPlaceEvent $event) // Hindert das platzieren von Blöcken.
	{
		$nametag = $event->getPlayer();
        if(!$nametag->hasPermission("skypvp.build")) {
            $event->setCancelled();
            $nametag->sendMessage($this->prefix . f::RED . "Das Darfst du nicht!");
        } else {
        }
	}
        public function onBreak(BlockBreakEvent $event) // Hindert das zerstören von Blöcken.
	{
		$nametag = $event->getPlayer();
        if(!$nametag->hasPermission("skypvp.build")) {
            $event->setCancelled();
            $nametag->sendMessage($this->prefix . f::RED . "Das Darfst du nicht!");
        } else {
        }
	}
    public function onHunger(PlayerExhaustEvent $event) {
    $player = $event->getPlayer();
    $player->setFood(20);
}
    
}
