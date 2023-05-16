<?php

namespace Mohachi\Session\Contracts;

interface Stockist
{
	
	public function restore(string $key): array;
	public function exists(string $key): bool;
	public function store(string $key, array $data): void;
	
}
