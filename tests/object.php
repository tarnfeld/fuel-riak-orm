<?php

use RiakORM\Test, RiakORM\Test_Object;

class Object extends Test
{

    /**
     * Test creation of an object
     */
    public function testCreate()
    {
    	// Create a test value
    	$value = md5(time() . uniqid());

    	// Create an instance of the test object
    	$object = Test_Object::Factory("test_key");

    	// Assert we have a new instance of the "test_key" object
    	$this->assertInstanceOf('\\RiakORM\\Test_Object', $object);
    	$this->assertTrue($object->isNew());

    	// Update the objects value
    	$object->foo = $value;

    	// Assert the object saved
    	$this->assertTrue($object->save());

    	// Reload the object from riak to ensure the save was persisted
    	$object->refresh();

    	// Assert the freshly read object has the value we set
    	$this->assertEquals($object->foo, $value);
    }

    /**
     * Test deletion of an object
     * @depends testCreate
     */
    public function testDelete()
    {
    	// Get an object by the test key
    	$object = Test_Object::Get("test_key");

    	// Assert we have an existing instance of the "test_key" object
    	$this->assertInstanceOf('\\RiakORM\\Test_Object', $object);
    	$this->assertFalse($object->isNew());

    	// Delete the object and assert the deletion was a success
    	$this->assertTrue($object->delete());
    	
    	// Assert that this object now appears new.
    	$this->assertTrue($object->isNew());
    }
}
