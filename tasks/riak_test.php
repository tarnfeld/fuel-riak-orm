<?php

namespace Fuel\Tasks;

use \RiakClient, \RiakBucket, \RiakObject;

class Riak_Test
{
    public function run()
    {
        $client = new RiakClient("127.0.0.1");
		$bucket = $client->bucket("test_bucket");

		$test_object = $bucket->newObject("test_object");
		$test_object->setData(array(
			"key1" => "value1",
			"key2" => array("foo", "bar"),
			"key3" => 123
		));
		$test_object->store();

        
    }
}
