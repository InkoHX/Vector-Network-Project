<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/30
 * Time: 8:17
 */

namespace Core\Event;

use Core\Game\SpeedCorePvP\SpeedCorePvPCore;
use Core\Main;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\Listener;

class EntityInventoryChange implements Listener
{
	private $speedcorepvp;

	public function __construct(Main $plugin)
	{
		$this->speedcorepvp = new SpeedCorePvPCore($plugin);
	}

	public function event(EntityInventoryChangeEvent $event): void
	{
		$this->speedcorepvp->CancelChange($event);
	}
}