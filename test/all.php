<?php

/*****************************************************************************\
	This is the testing framework for PUDL. This testing framework is
	designed to be launched exclusively form within an Altaform based
	application. The instructions on how to set this up will be provided
	at a later time. This file, however, still gives some clear examples
	as to the types of SQL statements that can be generated through the
	PUDL library.

	IMPORTANT NOTE: The ->string() part of these queries means that they
	will *NOT* be executed, but instead ONLY return an object containing
	the SQL query statement generated. Removing ->string() from each line
	will allow execution of the generated statement. This is simply added
	here to compare the generated statements to their expected results, to
	ensure that all queries are generated by PUDL correctly.

	To execute this test from the command line, simply run the following:

	php _pudl/test/cli.php

	For HHVM users, simply run the following instead:

	hhvm _pudl/test/cli.php
\*****************************************************************************/


function pudlTest($expected) {
	global $db;
	if (is_string($expected)	&&	$expected === $db->query()) return;
	if (is_bool($expected)		&&	$expected) return;
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	echo "'" . (is_bool($expected) ? 'TRUE' : $expected) . "'\n\n";
	echo "QUERY:\n";
	echo "'" . $db->query() . "'\n\n";
	exit(1);
}


function pudlError($exception, $expected) {
	if (is_array($expected)) {
		foreach ($expected as $item) {
			if ($exception->getMessage() === $expected) {
				return;
			}
		}
	} else if ($exception->getMessage() === $expected) {
		return;
	}
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	echo "'" . $expected . "'\n\n";
	echo "ERROR:\n";
	echo "'" . $exception->getMessage() . "'\n\n";
	echo "\n\n";
	exit(1);
}


function pudlUnit($result, $expected=true) {
	if ($result === $expected) return;
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	var_dump($expected);
	echo "\n\n";
	echo "RESULT:\n";
	var_dump($result);
	exit(1);
}


//PHP 5.x COMPATIBILITY
if (!defined('PHP_INT_MIN'))		define('PHP_INT_MIN',		~PHP_INT_MAX);
if (!defined('PHP_FLOAT_MIN'))		define('PHP_FLOAT_MIN',		2.2250738585072E-308);
if (!defined('PHP_FLOAT_MAX'))		define('PHP_FLOAT_MAX',		1.7976931348623E+308);
if (!defined('PHP_FLOAT_EPSILON'))	define('PHP_FLOAT_EPSILON',	2.2204460492503E-16);



//RUN ALL UNIT TESTS
$list = scandir(__DIR__);
foreach ($list as $item) {
	if (strtolower(substr($item, -8)) !== '.inc.php') continue;
	require_once(__DIR__ . '/' . $item);
}

if (!empty($found)) {
	require_once(__DIR__ . '/timeout.php');
}
