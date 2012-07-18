<?php

namespace RiakORM;

use \TestCase as FuelTestCase;

class TestException extends \Exception { }

class TestCase extends FuelTestCase
{
    /**
     * The host running riak on which to test
     * 
     * @var _riakHost
     */
    protected $_riakHost    = "127.0.0.1";

    /**
     * The port used to connect to the host
     * 
     * @var _riakPort
     */
    protected $_riakPort    = 8098;

    /**
     * The bucket to test operations with
     * 
     * @var _riakBucket
     */
    protected $_riakBucket  = "fuel_test_bucket";

    /**
     * Local cache of the RiakClient object
     * 
     * @var _riakClient
     */
    protected $_riakClient  = null;

    /**
     * Get a connection to riak
     * 
     * @param string $host
     * @param int $port
     * @return RiakClient
     * @throws TestException
     */
    protected function _riak($host = null, $port = null)
    {
        if ($host)
        {
            $this->_riakHost = $host;
        }

        if ($port)
        {
            $this->_riakPort = $port;
        }

        if (!$this->_riakClient)
        {
            $this->_riakClient = new \RiakClient($host, $port);
        }

        if (!$this->_riakClient->isAlive())
        {
            throw new TestException("Could not establish a connection to riak");
        }

        return $this->_riakClient;
    }

    /**
     * Get a riak bucket
     * 
     * @param string $bucket
     * @return RiakBucket
     * @throws TestException
     */
    protected function _bucket($bucket = null)
    {
        if ($bucket)
        {
            $this->_riakBucket = $bucket;
        }

        return $this->_riak()->bucket($this->_riakBucket);
    }
}