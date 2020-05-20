<?php

namespace xtakumatutix\mywarp;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use onebone\economyapi\EconomyAPI;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

Class Main extends PluginBase implements Listener {

    public function onEnable() 
    {
        $this->getLogger()->notice("読み込み完了_ver.1.0.0");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool 
    {
        if ($sender instanceof Player) {
        	$mymoney = EconomyAPI::getInstance()->myMoney($sender);
        	if ($mymoney < 1500){
        		$x = $sender->getFloorX();
                $y = $sender->getFloorY();
                $z = $sender->getFloorZ();
                $level = $sender->getLevel()->getName();
                if(isset($args[0])){
                	$item = Item::get(445, 0, 1);
                	$item->setLore(["X:".$x. "Y:" .$y. "Z:" .$z. "World:" .$level]);
                	$item->setCustomName($args[0]);
                	if($sender->getInventory()->canAddItem($item)){
                        $tag = $item->getNamedTag() ?? new CompoundTag('', []);
                        $tag->setTag(new IntTag("x", $x, true));
                        $tag->setTag(new IntTag("y", $y, true));
                        $tag->setTag(new IntTag("z", $z, true));
                        $tag->setTag(new StringTag("level", $level, true));
                        $item->setNamedTag($tag);
                        $sender->getInventory()->addItem($item);
                        return true;
                	}else{
                		$sender->sendMessage("イベントリにはいらない");
                		return true;
                	}
                }else{
                	$sender->sendMessage("ワープ名を入力してください");
                	return true;
                }
            }else{
            	$sender->sendMessage("お金がたりません");
            	return true;
            }
        }else{
        	$sender->sendMessage("ゲーム内で使用してください");
        	return true;
        }
    }

    public function tap(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $itemid = $item->getID();
        if ($itemid === 445) {
            $tag = $item->getNamedTag();
            if ($tag->offsetExists("x")) {
                    $tpx = $tag->getTag('x');
                    $tpy = $tag->getTag('y');
                    $tpz = $tag->getTag('z');
                    $tplevel = $tag->getString('level');
                    $tpos = new Position($tpx, $tpy, $tpz, $tplevel);
                    $player->teleport($tpos);
            }
        }
    }
}