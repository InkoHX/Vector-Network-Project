<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/08/02
 * Time: 16:54
 */

namespace Core\Entity;


use Core\Commands\MessagesEnum;
use Core\Player\MazaiPoint;
use Core\Player\Money;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;
use tokyo\pmmp\libform\element\Button;
use tokyo\pmmp\libform\FormApi;

class Mazai extends VectorNPC
{
	const ENTITY_ID = 2;

	private $money;
	private $mazai;

	public function __construct()
	{
		$this->money = new Money();
		$this->mazai = new MazaiPoint();
	}

	public function ClickEntity(DataPacketReceiveEvent $event): void
	{
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if ($packet instanceof InventoryTransactionPacket) {
			if ($packet->transactionType === $packet::TYPE_USE_ITEM_ON_ENTITY) {
				if ($packet->trData->entityRuntimeId === self::getEid($player, self::ENTITY_ID)) {
					FormApi::makeListForm(function (Player $player, ?int $key) {
						if (!FormApi::formCancelled($key)) {
							switch ($key) {
								case 0:
									if ($this->money->reduceMoney($player->getName(), 10000)) {
										$player->sendMessage(MessagesEnum::BUY_SUCCESS);
										$this->mazai->addMazai($player->getName(), 1);
									} else {
										$player->sendMessage(MessagesEnum::BUY_ERROR);
									}
									break;
							}
						}
					})->setTitle("§a魔剤さんの§e変換所")
						->setContent("§6V§bN§eCoin§rを§aMAZAI§rにします。")
						->addButton(new Button("§e1§aMAZAI\n§e10000§6V§bN§eCoin"))
						->sendToPlayer($player);
				}
			}
		}
	}
}