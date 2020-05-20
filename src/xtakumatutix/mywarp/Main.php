<?php

namespace xtakumatutix\mywarp;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\CompoundTag;
use onebone\economyapi\EconomyAPI;

Class Main extends PluginBase implements Listener {

    public function onEnable() 
    {
        $this->getLogger()->notice("読み込み完了 - ver.".$this->getDescription()->getVersion());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool 
    {
        if ($sender instanceof Player) {
            $mymoney = EconomyAPI::getInstance()->myMoney($sender);
            if (!$mymoney < 3000){
                $x = $sender->getFloorX();
                $y = $sender->getFloorY();
                $z = $sender->getFloorZ();
                $level = $sender->getLevel()->getName();
                if(isset($args[0])){
                    $item = Item::get(445, 0, 1);
                    $item->setLore(["X:".$x." Y:".$y." Z:".$z." World:".$level]);
                    $item->setCustomName($args[0]);
                    if($sender->getInventory()->canAddItem($item)){
                        $tag = $item->getNamedTag() ?? new CompoundTag('', []);
                        $tag->setTag(new IntTag("x", $x, true));
                        $tag->setTag(new IntTag("y", $y, true));
                        $tag->setTag(new IntTag("z", $z, true));
                        $tag->setTag(new StringTag("level", $level, true));
                        $item->setNamedTag($tag);
                        $sender->getInventory()->addItem($item);
                        EconomyAPI::getInstance()->reduceMoney($sender, 3000);
                        $sender->sendMessage("§a >> §fMyWarpItemを入手しました！！ §7(地点名:".$args[0]." X:".$x." Y:".$y." Z:".$z." World:".$level.")");
                        return true;
                    }else{
                        $sender->sendMessage("§c >> §fインベントリに空きがありません");
                        return true;
                    }
                }else{
                    $sender->sendMessage("§c >> §fワープ名を入力してください");
                    return true;
                }
            }else{
                $sender->sendMessage("§c >> §fお金がたりません <3000KG必要です>");
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
                $tpx = $tag->getInt('x');
                $tpy = $tag->getInt('y');
                $tpz = $tag->getInt('z');
                $tplevel = $this->getServer()->getLevelByName($tag->getString('level'));
                $tplevelname = $tag->getString('level');
                $tpos = new Position($tpx, $tpy, $tpz, $tplevel);
                $point = $item->getCustomName();
                $player->teleport($tpos);
                $player->sendMessage("§a >> §f地点名:".$point." X:".$tpx." Y:".$tpy." Z:".$tpz." World:".$tplevelname." にテレポートしました！");
            }
        }
    }
}