<?php

/**
 * Bootstrap file for fuel-riak-orm
 *
 * @package fuel-riak-orm
 * @see http://github.com/tarnfeld/fuel-riak-orm
 * @author Tom Arnfeld <tarnfeld@me.com>
 */

Autoloader::add_classes(array(

	"RiakClient"			=> __DIR__ . '/riak-php/riak.php',
	"RiakMapReduce"			=> __DIR__ . '/riak-php/riak.php',
	"RiakMapReducePhase"	=> __DIR__ . '/riak-php/riak.php',
	"RiakLinkPhase"			=> __DIR__ . '/riak-php/riak.php',
	"RiakLink"				=> __DIR__ . '/riak-php/riak.php',
	"RiakBucket"			=> __DIR__ . '/riak-php/riak.php',
	"RiakObject"			=> __DIR__ . '/riak-php/riak.php',
	"RiakStringIO"			=> __DIR__ . '/riak-php/riak.php',
	"RiakUtils"				=> __DIR__ . '/riak-php/riak.php'
));
