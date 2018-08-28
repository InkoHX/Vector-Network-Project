<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/06/17
 * Time: 14:41
 */

namespace Core;

use Core\Entity\GameMaster;
use Core\Entity\Mazai;
use Core\Entity\MazaiMaster;
use Core\Event\BlockBreak;
use Core\Event\BlockPlace;
use Core\Event\EntityDamage;
use Core\Event\EntityInventoryChange;
use Core\Event\EntityShootBow;
use Core\Event\PlayerCommandPreprocess;
use Core\Event\PlayerDeath;
use Core\Event\PlayerExhaust;
use Core\Event\PlayerInteract;
use Core\Event\PlayerJoin;
use Core\Event\PlayerLogin;
use Core\Event\PlayerMove;
use Core\Event\PlayerPreLogin;
use Core\Event\PlayerQuit;
use Core\Event\PlayerRespawn;
use Core\Game\Athletic\AthleticCore;
use Core\Game\FFAPvP\FFAPvPCore;
use Core\Game\SpeedCorePvP\SpeedCorePvPCore;
use Core\Game\Survival\SurvivalCore;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;

class EventListener implements Listener
{
	private $plugin = null;
	protected $ffapvp;
	protected $speedcorepvp;
	protected $athletic;
	protected $survival;
	protected $playerjoinevent;
	protected $playerquitevent;
	protected $playerloginevent;
	protected $playerdeathevent;
	protected $playerprelogin;
	protected $playermoveevent;
	protected $entitydamage;
	protected $blockbreakevent;
	protected $blockplaceevent;
	protected $playerinteractevent;
	protected $playercommandpreprocessevent;
	protected $playerrespawnevent;
	protected $entityinventorychange;
	protected $entityshootbowevent;
	protected $playerexhaustevent;

	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
		$this->ffapvp = new FFAPvPCore($this->plugin);
		$this->speedcorepvp = new SpeedCorePvPCore($this->plugin);
		$this->athletic = new AthleticCore();
		$this->survival = new SurvivalCore($this->plugin);
		$this->playerjoinevent = new PlayerJoin($this->plugin);
		$this->playerquitevent = new PlayerQuit($this->plugin);
		$this->playerloginevent = new PlayerLogin($this->plugin);
		$this->playerdeathevent = new PlayerDeath($this->plugin);
		$this->playerprelogin = new PlayerPreLogin($this->plugin);
		$this->playermoveevent = new PlayerMove($this->plugin);
		$this->entitydamage = new EntityDamage($this->plugin);
		$this->blockbreakevent = new BlockBreak($this->plugin);
		$this->blockplaceevent = new BlockPlace($this->plugin);
		$this->playerinteractevent = new PlayerInteract($this->plugin);
		$this->playercommandpreprocessevent = new PlayerCommandPreprocess($this->plugin);
		$this->playerrespawnevent = new PlayerRespawn($this->plugin);
		$this->entityinventorychange = new EntityInventoryChange($this->plugin);
		$this->entityshootbowevent = new EntityShootBow($this->plugin);
		$this->playerexhaustevent = new PlayerExhaust($this->plugin);
	}

	public function onJoin(PlayerJoinEvent $event)
	{
		$this->playerjoinevent->event($event);
	}

	public function onQuit(PlayerQuitEvent $event)
	{
		$this->playerquitevent->event($event);
		$player = $event->getPlayer();
		$this->speedcorepvp->GameQuit($player);
		$this->survival->SaveData($event);
		// $this->athletic->onQuit($event);
	}

	public function onLogin(PlayerLoginEvent $event)
	{
		$this->playerloginevent->event($event);
	}

	public function onDeath(PlayerDeathEvent $event)
	{
		$this->playerdeathevent->event($event);
	}

	public function pnPreLogin(PlayerPreLoginEvent $event)
	{
		$this->playerprelogin->event($event);
	}

	public function onMove(PlayerMoveEvent $event)
	{
		$this->playermoveevent->event($event);
		$this->athletic->loop($event);
	}

	public function onEntityDamage(EntityDamageEvent $event)
	{
		$this->entitydamage->event($event);
		$this->speedcorepvp->Damage($event);
	}

	public function onBreak(BlockBreakEvent $event)
	{
		$this->blockbreakevent->event($event);
		$this->speedcorepvp->BreakCore($event);
		$this->speedcorepvp->DropItem($event);
		$this->survival->BreakBlock($event);
	}

	public function onPlace(BlockPlaceEvent $event)
	{
		$this->blockplaceevent->event($event);
		$this->speedcorepvp->AntiPlace($event);
	}

	public function onInteract(PlayerInteractEvent $event)
	{
		$this->playerinteractevent->event($event);
		$this->speedcorepvp->GameJoin($event->getPlayer(), $event->getBlock());
		$this->speedcorepvp->Interact($event);
		$this->survival->Join($event);
		//$this->athletic->isAthleticFinish($event, $event->getPlayer());
		//$this->athletic->touch($event);
		//$this->athletic->getAthleticData($event);
	}

	public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event)
	{
		$this->playercommandpreprocessevent->event($event);
	}

	public function onRespawn(PlayerRespawnEvent $event)
	{
		$this->playerrespawnevent->event($event);
		$this->speedcorepvp->Respawn($event->getPlayer());
	}

	public function onEntityInventoryChange(EntityInventoryChangeEvent $event)
	{
		$this->entityinventorychange->event($event);
		$this->speedcorepvp->CancelChange($event);
	}

	public function onEntityShootBow(EntityShootBowEvent $event)
	{
		$this->entityshootbowevent->event($event);
	}

	public function EntityLevelChange(EntityLevelChangeEvent $event)
	{
		$this->speedcorepvp->LevelChange($event);
		$this->survival->LoadData($event);
	}

	public function onChat(PlayerChatEvent $event)
	{
		$this->speedcorepvp->TeamChat($event);
	}

	public function onPlayerAchievementAwarded(PlayerAchievementAwardedEvent $event)
	{
		$event->setCancelled(true);
	}

	public function onSignChange(SignChangeEvent $event)
	{
		$this->survival->Sign($event);
		$this->speedcorepvp->Sign($event);
	}

	public function onPlayerExhaust(PlayerExhaustEvent $event)
	{
		$this->playerexhaustevent->event($event);
	}
}
