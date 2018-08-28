<?php
/**
 * Created by PhpStorm.
 * User: UramnOIL
 * Date: 2018/08/28
 * Time: 19:37
 */

namespace Core\Commands;


use Core\Entity\VectorNPC\VectorNPCFactory;
use Core\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class Npc extends PluginCommand
{
	private $factory;
	public function __construct(Main $owner)
	{
		parent::__construct("npc", $owner);
		$this->setPermission("vector.network.admin");
		$this->factory = new VectorNPCFactory($owner);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!$this->testPermissionSilent($sender))
		{
			return;
		}
		if (!isset($args[0])) {
			$sender->sendMessage("NPCを指定してください");
			return;
		}
		switch(strtolower($args[0])) {
			case "gamemaster":
				$this->factory->createGameMaster();
				$sender->sendMessage("配置しました");
				break;
			case "mazai":
				$this->factory->createMazai();
				$sender->sendMessage("配置しました");
				break;
			case "mazaimaster":
				$this->factory->createMazaiMaster();
				$sender->sendMessage("配置しました");
				break;
			default:
				$sender->sendMessage("NPCが存在しません");
				break;
		}
	}
}