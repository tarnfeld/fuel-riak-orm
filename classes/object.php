<?php

namespace RiakORM;

use RiakClient, RiakBucket;

class ObjectException extends Exception { }

abstract class Object
{
    /**
	 * Use a bucket named 'test_bucket'
	 * 
	 * @var string
	 */
	protected static $_bucket	= null;

	/**
	 * Instantiate a new object with a key (and option data)
	 * 
	 * @param $key string
	 * @param $data array
	 * @return Object
	 */
	public static function Factory($key, array $data = null)
	{
		$object = new static($key);

		if ($data)
		{
			$object->setData($data);
		}

		return $object;
	}

	/**
	 * Get an object by key
	 * 
	 * @param $key string
	 * @param $default bool If the object cannot be found, default to returning an empty object
	 */
	public static function Get($key, $default = false)
	{
		$object = new static($key);

		if ($object->refresh() || $default)
		{
			return $object;
		}

		return false;
	}

	/**
	 * The object's key
	 * 
	 * @var string
	 */
	protected $_key		= null;

	/**
	 * Object data
	 * 
	 * @var array
	 */
	protected $_data	= null;

	/**
	 * Is this object new in Riak (As far as we know)
	 * 
	 * @var bool
	 */
	protected $_new		= true;

	/**
	 * A locally cached RiakClient object
	 * 
	 * @var RiakClient
	 */
	protected $_riakClient	= null;

	/**
	 * A locally cached RiakBucket object for this object's bucket
	 * 
	 * @var RiakBucket
	 */
	protected $_riakBucket	= null;

	/**
	 * Is this object new?
	 * 
	 * @return bool
	 */
	public function isNew()
	{
		return $this->_new;
	}

	/**
	 * Persist this object to Riak
	 * 
	 * @param $w W-Value (Number of nodes to respond)
	 * @param $dw DW-Value (Number of nodes to confirm the save)
	 * @return bool
	 */
	public function save($w = null, $dw = null)
	{
		if (!$this->_key)
		{
			throw new ObjectException("Cannot save an object with no key");
		}

		$bucket = $this->_bucket();
		$object = $bucket->get($this->_key);

		$object->setData($this->_data);
		$object->setContentType("application/json");
		$object->store($w, $dw);

		return true;
	}

	/**
	 * Reload this object from Riak
	 * 
	 * @return bool
	 */
	public function refresh()
	{
		if (!$this->_key)
		{
			throw new ObjectException("Cannot refresh an object with no key");
		}

		$bucket = $this->_bucket();
		$result = $bucket->get($this->_key);

		if ($result->exists())
		{
			$this->_new = false;
			$this->_data = $result->getData();

			return true;
		}

		$this->_new = true;
		$this->_data = null;

		return false;
	}

	/**
	 * Delete this object
	 * 
	 * @param $dw DW-Value (Number of nodes to confirm the delete)
	 */
	public function delete($dw = null)
	{
		if (!$this->_key)
		{
			throw new ObjectException("Cannot delete an object with no key");
		}

		if ($this->isNew())
		{
			throw new ObjectException("Cannot delete a new object");
		}

		$bucket = $this->_bucket();
		$object = $bucket->get($this->_key);

		if ($object->exists())
		{
			$object->delete($dw);
		}

		$this->_new = true;
		$this->_data = null;

		return true;
	}

	/**
	 * Getter
	 * 
	 * @param $key string
	 * @return mixed
	 */
	public function __get($key)
	{
		if (!$this->_data || !isset($this->_data[$key]))
		{
			return null;
		}

		return $this->_data[$key];
	}

	/**
	 * Setter
	 * 
	 * @param $key string
	 * @param $value mixed
	 */
	public function __set($key, $value)
	{
		if (!$this->_data)
		{
			$this->_data = array();
		}

		$this->_data[$key] = $value;
	}

	/**
	 * Constructor
	 * 
	 * @protected
	 */
	protected function __construct($key = null)
	{
		if (!static::$_bucket)
		{
			throw new ObjectException("Cannot create new object with no bucket");
		}

		if ($key)
		{
			$this->_setKey($key);
		}
	}

	/**
	 * Set the object's key
	 * @param $key string
	 * @return $this
	 */
	protected function _setKey($key)
	{
		$this->_key = $key;

		return $this;
	}

	/**
	 * Get an instance of a RiakClient object
	 * 
	 * @return RiakClient
	 */
	protected function _riak()
	{
		if (!$this->_riakClient)
		{
			$host = \Config::get('riak.host');
			$port = \Config::get('riak.port');

			$this->_riakClient = new RiakClient($host, $port);
		}

		return $this->_riakClient;
	}

	/**
	 * Get an instance of a RiakBucket object for this object
	 * 
	 * @return RiakBucket
	 */
	protected function _bucket()
	{
		if (!$this->_riakBucket)
		{
			if (!$client = $this->_riak())
			{
				throw new ObjectException("Could not create bucket, could not connect to Riak.");
			}

			$this->_riakBucket = new RiakBucket($client, static::$_bucket);
		}

		return $this->_riakBucket;
	}
}
