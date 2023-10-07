<?php

declare(strict_types=1);

namespace sb\scheduler\server;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use sb\Skyblock;

class FileCopyAsyncTask extends AsyncTask {
	public function __construct(private readonly string $from, private readonly string $to, \Closure $closure) {
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void {
		try{
			Filesystem::recursiveCopy($this->from, $this->to);
			$this->setResult(true);
		} catch(\Exception $e) {
			$this->setResult(false);
		}
	}

	public function onCompletion() : void {
		$result = $this->getResult();
		$closure = $this->fetchLocal("closure");

		if($closure !== null){
			if($result === true){
				Skyblock::getInstance()->getLogger()->debug("Successfully copied file from {$this->from} to {$this->to}");
			} else 	Skyblock::getInstance()->getLogger()->debug("Failed to copy file from {$this->from} to {$this->to}");

			$closure($result);
		}
	}
}