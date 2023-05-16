<?php

namespace Mohachi\Session;

use Nette\Utils\Random;
use Mohachi\Session\Contracts\Stockist;
use Mohachi\Session\Contracts\Identifier;

class Session
{
	
	private $data;
	
	
	public function __construct(
		private Stockist $stockist,
		private Identifier $identifier
	){}
	
	
	public function start(): void
	{
		$id = $this->identifier->get();
		
		if( $id != null && $this->stockist->exists($id) )
		{
			$this->data = $this->stockist->restore($id);
		}
		else
		{
			$id = Random::generate();
			$this->identifier->set($id);
			$this->stockist->store($id, []);
		}
	}
	
	
	public function data()
	{
		return $this->data;
	}
	
}
