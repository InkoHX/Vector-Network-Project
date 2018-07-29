<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/21
 * Time: 21:02
 */

namespace Core\Task\Teleport;

use Core\Game\SpeedCorePvP\SpeedCorePvPCore;
use Core\Main;
use Core\Task\PluginTask;
use pocketmine\level\Position;
use pocketmine\Player;

class TeleportLobbyTask extends PluginTask
{
	protected $player, $plugin, $speedcorepvp;

	public function __construct(Main $plugin, Player $player)
	{
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->player = $player;
		$this->speedcorepvp = new SpeedCorePvPCore($this->plugin);
	}

	public function onRun(int $currentTick)
	{
		$level = $this->owner->getServer()->getLevelByName("lobby");
		$this->player->teleport(new Position(257, 8, 257, $level));
		$this->player->setSpawn(new Position(257, 8, 257, $level));
		$this->player->setHealth(20);
		$this->player->setMaxHealth(20);
		$this->player->setFood(20);
		$this->player->getInventory()->clearAll(true);
		$this->speedcorepvp->GameQuit($this->player);
		$this->player->sendMessage("§aテレポートしました。");
	}
}
