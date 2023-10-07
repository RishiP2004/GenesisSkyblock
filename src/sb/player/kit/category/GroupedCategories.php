<?php
namespace sb\player\kit\category;

class GroupedCategories{
	public function __construct(public readonly array $categories = []){
	}

	public function getCategories(): array{
		return $this->categories;
	}

	public function getCategory(string $category): ?KitCategory{
		return $this->categories[$category] ?? null;
	}

	public function size(): int{
		return count($this->categories);
	}

	public function foreach(callable $callback): void{
		foreach($this->categories as $category){
			$callback($category);
		}
	}
}