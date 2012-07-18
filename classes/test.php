<?php

namespace RiakORM;

class TestException extends \Exception { }

class Test extends \TestCase
{

    /**
     * Test connection to riak
     */
    public function testConnection()
    {
        $riak = $this->_riak();

        // Assert we have a RiakClient object
        $this->assertInstanceOf('RiakClient', $riak);
    }

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
        if (!$host)
        {
            $host = \Config::get("riak.host");
        }

        if (!$port)
        {
            $port = \Config::get("riak.port");
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
    protected function _bucket($bucket)
    {
        return $this->_riak()->bucket($bucket);
    }
}