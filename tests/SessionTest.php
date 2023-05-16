<?php

use Nette\Utils\Random;
use Mohachi\Session\Session;
use PHPUnit\Framework\TestCase;
use Mohachi\Session\Contracts\Stockist;
use Mohachi\Session\Contracts\Identifier;

class SessionTest extends TestCase
{
	
	public function test_starting_with_not_null_stored_id_will_load_data()
	{
		/* Arrange */
		$id = Random::generate();
		$data = [
			'key1' => 'val1',
			'key2' => 'val2'
		];
		$stockist = new TestStockist;
		$identifier = new TestIdentifier;
		$identifier->set($id);
		$stockist->store($id, $data);
		$session = new Session($stockist, $identifier);
		
		/* Act */
		$session->start();
		
		/* Assert */
		$this->assertSame($data, $session->data());
	}
	
	
	public function test_starting_with_null_id_will_update_client_id_and_store_it()
	{
		/* Arrange */
		$stockist = new TestStockist;
		$identifier = new TestIdentifier;
		$session = new Session($stockist, $identifier);
		
		/* Act */
		$session->start();
		
		/* Assert */
		$this->assertIsString($identifier->get());
		$this->assertNotEmpty($identifier->get());
		$this->assertTrue($stockist->exists($identifier->get()));
	}
	
	
	public function test_starting_with_unstored_id_will_update_client_id_and_store_it()
	{
		/* Arrange */
		$id = Random::generate();
		$stockist = new TestStockist;
		$identifier = new TestIdentifier;
		$identifier->set($id);
		$session = new Session($stockist, $identifier);
		
		/* Act */
		$session->start();
		
		/* Assert */
		$this->assertIsString($identifier->get());
		$this->assertNotEmpty($identifier->get());
		$this->assertNotSame($id, $identifier->get());
		$this->assertTrue($stockist->exists($identifier->get()));
	}
	
	
	public function test_starting_with_null_id_will_generate_unique_id()
	{
		/* Arrange */
		$stockist = new TestStockist;
		$identifier = new TestIdentifier;
		$session = new Session($stockist, $identifier);
		$session->start();
		$identifier = new TestIdentifier;
		$stockist_sample = clone $stockist;
		$session = new Session($stockist, $identifier);
		
		/* Act */
		$session->start();
		
		/* Assert */
		$this->assertFalse($stockist_sample->exists($identifier->get()));
	}
	
	
	public function test_starting_with_unstored_id_will_generate_unique_id()
	{
		/* Arrange */
		$stockist = new TestStockist;
		$identifier = new TestIdentifier;
		$identifier->set(Random::generate());
		$session = new Session($stockist, $identifier);
		$session->start();
		$identifier = new TestIdentifier;
		$stockist_sample = clone $stockist;
		$session = new Session($stockist, $identifier);
		
		/* Act */
		$session->start();
		
		/* Assert */
		$this->assertFalse($stockist_sample->exists($identifier->get()));
	}
	
}

class TestStockist implements Stockist
{
	private $sessions = [];
	
	public function restore(string $key): array
	{
		return $this->sessions[$key];
	}
	
	public function exists(string $key): bool
	{
		return array_key_exists($key, $this->sessions);
	}
	
	public function store(string $key, array $data): void
	{
		$this->sessions[$key] = $data;
	}
	
}

class TestIdentifier implements Identifier
{
	private $id;
	
	public function get(): ?string
	{
		return $this->id;
	}
	
	public function set(string $id): void
	{
		$this->id = $id;
	}
	
}
