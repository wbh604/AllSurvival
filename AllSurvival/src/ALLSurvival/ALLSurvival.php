<?php
namespace ALLSurvival;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerGameModeChangeEvent;

class ALLSurvival extends PluginBase implements CommandExecutor, Listener {
  public function onEnable() {
    @mkdir($this->getDataFolder());
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->config = new Config($this->getDataFolder()."clist.yml", Config::YAML, array(
	"clist" => array()
	));
    $this->perm = $this->getServer()->getPluginManager()->getPermission("ALLSurvival.ac"); 
    $this->perm = $this->getServer()->getPluginManager()->getPermission("ALLSurvival.dc"); 
	$this->perm = $this->getServer()->getPluginManager()->getPermission("ALLSurvival.clist"); 
	$this->clist = $this->config->getAll();
    $this->getLogger()->info("ALLSurvival loaded!\n");
  }
  
   public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    switch($cmd->getName()) {
     case "ac":
     if ($sender instanceof Player){
        $sender->sendMessage("Please run command in console.");
        return true;
      }else{
	  if(isset($args[0])){
	  if (!in_array($args[0], $this->config->get("clist"))) 
        {
      $c = $this->config->get("clist");
      $c[] = $args[0];
	  $this->config->set("clist", $c);
       $this->config->save();
        $sender->sendMessage("[ALLSurvival]Allow player " .$args[0]." to use creative");
        return true;
		break;
        }else{
		$sender->sendMessage("[ALLSurvival]player " .$args[0]." is already allowed to use creative");
		return true;
		break;
		}
      }
	  }
      break;
	  case "dc":
	  if ($sender instanceof Player){
        $sender->sendMessage("Please run command in console.");
        return true;
      }else{
	  if(isset($args[0])){
	  if (!in_array($args[0], $this->config->get("clist"))) 
        {
		$sender->sendMessage("[ALLSurvival]player " .$args[0]." is already disallowed to use creative");
		return true;
		break;
		}else{
        $c = $this->config->get("clist");
        $key = array_search($args[0], $c);
        unset($c[$key]);
        $this->config->set("clist", $c);
        $this->config->save();
        $sender->sendMessage("[ALLSurvival]Disallow player ".$args[0]." to use creative");
		return true;
		break;
        }
		}
		}
		break;
	 case "clist":
	 foreach($this->config->get("clist") as $i){
        $sender->sendMessage($i);
		}
}
}

   public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
		$name = $player->getDisplayName();
		$gm = $player->getgamemode();
		//Server::getInstance()->broadcastMessage($gm);
		if($gm == "1" ){
		 if(in_array($name, $this->config->get("clist"))){
		  Server::getInstance()->broadcastMessage("[ALLSURVIVAL] Player $name is allowed to use creative.");
		}else{
		 $player->setgamemode("0");
        Server::getInstance()->broadcastMessage("[ALLSURVIVAL] Player $name is not allowed to use creative. the system will turn $name to survival.");
		}
    }
	}
  
   public function clist($name){
        return in_array($name, $this->config->get("clist"));
    }
	
   public function ongamemodechange(PlayerGameModeChangeEvent $event){
   $player = $event->getPlayer();
   $name = $player->getDisplayName();
   $ngm = $event->getNewGamemode();
   if( $ngm  == "1" ){
 if(in_array($name, $this->config->get("clist"))){
  Server::getInstance()->broadcastMessage("[ALLSURVIVAL]Success!");
  return true;
		}else{
        Server::getInstance()->broadcastMessage("[ALLSURVIVAL]Please allow him/she to use creative first");
		$event->SetCancelled();
		return false;
		}
    }
	}
  
  }
