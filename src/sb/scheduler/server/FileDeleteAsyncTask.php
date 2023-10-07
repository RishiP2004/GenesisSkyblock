<?php

declare(strict_types=1);

namespace sb\scheduler\server;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;
use sb\Skyblock;

class FileDeleteAsyncTask extends AsyncTask {
	public function __construct(private readonly string $dir, \Closure $closure) {
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void{
		try {
			Filesystem::recursiveUnlink($this->dir);
			$this->setResult(true);
		} catch (\Throwable $e) {
			$this->setResult(false);
		}
	}

	public function onCompletion() : void{
		parent::onCompletion();

		$closure = $this->fetchLocal("closure");
		$result = $this->getResult();

		if($closure !== null){
			if($result === true){
				Skyblock::getInstance()->getLogger()->debug("Successfully deleted directory {$this->dir}");
			} else Skyblock::getInstance()->getLogger()->debug("Failed to delete directory {$this->dir}");

			$closure($result);
		}
	}
}