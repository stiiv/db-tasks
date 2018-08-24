<?php

namespace App\Core\Storage;

use Symfony\Component\Cache\Simple\FilesystemCache;

class GatherAnswers
{
	/**
	 * FilesystemCache instance
	 * @var object
	 */
	protected $cache;

	/**
	 * namespace for cache storage
	 * @var string
	 */
	protected $namespace;

	/**
	 * data to store
	 * @var array
	 */
	protected $storage = [];
	
	/**
	 * @param [string] $namespace for cache
	 * @param array  $storage   data
	 */
	public function __construct($namespace)
	{
		$this->namespace = $namespace;
		$this->cache = new FilesystemCache();
	}

	public function all()
	{
		return $this->storage;
	}

	public function add($key, $value) 
	{
		$this->storage[$key] = $value;
		$this->setCachedItem($key, $value);
		return $this;
	}

	/**
	 * get storage item by key
	 * @param  mixed  $key     storage key
	 * @param  mixed  $default fallback value
	 * @return mixed
	 */
	public function get($key, $default = '') 
	{
		return array_key_exists($key, $this->storage) 
			? $this->storage[$key] 
			: $default;
	}

	public function remove($key) 
	{
		if( array_key_exists($key, $this->storage) ) {
			unset($this->storage[$key]);
			$this->deleteCachedItem($key);
		}
		
		return $this;
	}

	public function getNamespace($value = '')
	{
		if(!$value)
			return $this->namespace;

		return $this->namespace.'.'.$value;
	}

	public function getCache()
	{
		return $this->cache;
	}

	public function clearCache()
	{
		return $this->cache->clear();
	}

	public function getCachedItem($item, $default = '')
	{
		return $this->cache->has($this->getNamespace($item)) 
			? $this->cache->get($this->getNamespace($item)) 
			: $default;
	}

	protected function setCachedItem($item, $value)
	{
		$this->cache->set($this->getNamespace($item), $value);
		return $this->cache;
	}

	protected function deleteCachedItem($item)
	{
		$cached_item = $this->getNamespace($item);
		if( $this->cache->has($cached_item) )
			$this->cache->delete($cached_item);

		return $this->cache;
	}
}