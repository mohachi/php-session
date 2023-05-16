<?php

namespace Mohachi\Session\Contracts;

interface Identifier
{
	
	public function get(): ?string;
	public function set(string $id): void;
	
}
