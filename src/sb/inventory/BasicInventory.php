<?php

declare(strict_types= 1);

namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\InvMenuTypeIds;

abstract class BasicInventory extends InvMenu implements InvMenuTypeIds{
	public function __construct(public string $typeId){
		parent::__construct(InvMenuHandler::getTypeRegistry()->get($this->typeId));

		$this->createInventory();
		$this->createListener();
	}

	abstract public function createInventory(): void;
	abstract public function createListener(): void;

	public function getType(): InvMenuType{
		return $this->type;
	}
}