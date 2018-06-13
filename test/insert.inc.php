<?php

//INSERT statement - all default values
$db->string()->insert('table', false);
pudlTest("INSERT INTO `table` () VALUES ()");




//INSERT statement - all default values
$db->string()->insert('table', []);
pudlTest("INSERT INTO `table` () VALUES ()");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>'']);
pudlTest("INSERT INTO `table` (`column`) VALUES ('')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>'0']);
pudlTest("INSERT INTO `table` (`column`) VALUES ('0')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>'value']);
pudlTest("INSERT INTO `table` (`column`) VALUES ('value')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>' value ']);
pudlTest("INSERT INTO `table` (`column`) VALUES (' value ')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>' `value` ']);
pudlTest("INSERT INTO `table` (`column`) VALUES (' `value` ')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>' va"lue ']);
pudlTest("INSERT INTO `table` (`column`) VALUES (' va\\\"lue ')");




//INSERT statement - using associative array - string
$db->string()->insert('table', ['column'=>" va'lue "]);
pudlTest('INSERT INTO `table` (`column`) VALUES (\' va\\\'lue \')');




//INSERT statement - using associative array - null
$db->string()->insert('table', ['column'=>NULL]);
pudlTest("INSERT INTO `table` (`column`) VALUES (NULL)");




//INSERT statement - using associative array - boolean
$db->string()->insert('table', ['column'=>false]);
pudlTest("INSERT INTO `table` (`column`) VALUES (FALSE)");




//INSERT statement - using associative array - boolean
$db->string()->insert('table', ['column'=>true]);
pudlTest("INSERT INTO `table` (`column`) VALUES (TRUE)");




//INSERT statement - using associative array - integer
$db->string()->insert('table', ['column'=>2]);
pudlTest("INSERT INTO `table` (`column`) VALUES (2)");




//INSERT statement - using associative array - float
$db->string()->insert('table', ['column'=>3.4]);
pudlTest("INSERT INTO `table` (`column`) VALUES (3.4)");




//INSERT statement - using associative array - float
$db->string()->insert('table', ['column'=>-5.6]);
pudlTest("INSERT INTO `table` (`column`) VALUES (-5.6)");




//INSERT statement - using associative array - float (null)
$db->string()->insert('table', ['column'=>NAN]);
pudlTest("INSERT INTO `table` (`column`) VALUES (NULL)");




//INSERT statement - using associative array - float (null)
$db->string()->insert('table', ['column'=>INF]);
pudlTest("INSERT INTO `table` (`column`) VALUES (NULL)");




//INSERT statement - using associative array - float (null)
$db->string()->insert('table', ['column'=>-INF]);
pudlTest("INSERT INTO `table` (`column`) VALUES (NULL)");




//INSERT statement - using associative array - float
$db->string()->insert('table', ['column'=>1e56]);
pudlTest("INSERT INTO `table` (`column`) VALUES (1.0E+56)");




//INSERT statement - using associative array - float
$db->string()->insert('table', ['column'=>-1e78]);
pudlTest("INSERT INTO `table` (`column`) VALUES (-1.0E+78)");




//INSERT statement - using associative array - integer constant
$db->string()->insert('table', ['column'=>PHP_INT_MAX]);
pudlTest("INSERT INTO `table` (`column`) VALUES (9223372036854775807)");




//INSERT statement - using associative array - integer constant
$db->string()->insert('table', ['column'=>PHP_INT_MIN]);
pudlTest("INSERT INTO `table` (`column`) VALUES (-9223372036854775808)");




//INSERT statement - using associative array - float constant
$db->string()->insert('table', ['column'=>PHP_FLOAT_EPSILON]);
pudlTest("INSERT INTO `table` (`column`) VALUES (2.2204460492503E-16)");




//INSERT statement - using associative array - float constant
$db->string()->insert('table', ['column'=>PHP_FLOAT_MIN]);
pudlTest("INSERT INTO `table` (`column`) VALUES (2.2250738585072E-308)");




//INSERT statement - using associative array - float constant
$db->string()->insert('table', ['column'=>PHP_FLOAT_MAX]);
pudlTest("INSERT INTO `table` (`column`) VALUES (1.7976931348623E+308)");




//INSERT statement - using associative array - array (empty)
$db->string()->insert('table', ['column'=>[]]);
pudlTest("INSERT INTO `table` (`column`) VALUES (NULL)");




//INSERT statement - using associative array - array
$db->string()->insert('table', ['column'=>['item']]);
pudlTest('INSERT INTO `table` (`column`) VALUES (\'[\"item\"]\')');




//INSERT statement - using associative array - array
$db->string()->insert('table', ['column'=>['dynamic'=>'item']]);
pudlTest('INSERT INTO `table` (`column`) VALUES (\'{\"dynamic\":\"item\"}\')');




//INSERT statement - using associative array - array
$db->string()->insert('table', ['column'=>['item1','item2']]);
pudlTest('INSERT INTO `table` (`column`) VALUES (\'[\"item1\",\"item2\"]\')');




//INSERT statement - using associative array - array
$db->string()->insert('table', ['column'=>['dynamic1'=>'item1', 'dynamic2'=>'item2']]);
pudlTest('INSERT INTO `table` (`column`) VALUES (\'{\"dynamic1\":\"item1\",\"dynamic2\":\"item2\"}\')');




//INSERT statement - using associative array, duplicate key update
$db->string()->insert('table', ['column'=>'value'], true);
pudlTest("INSERT INTO `table` (`column`) VALUES ('value') ON DUPLICATE KEY UPDATE `column`='value'");




//INSERT statement - using associative array, custom duplicate key update
$db->string()->insert('table', ['column'=>'value'], 'x=x+1');
pudlTest("INSERT INTO `table` (`column`) VALUES ('value') ON DUPLICATE KEY UPDATE x=x+1");




//INSERT statement - using associative array, custom duplicate key update using UPDATE syntax
$db->string()->insert('table', ['column'=>'value'], ['y'=>2]);
pudlTest("INSERT INTO `table` (`column`) VALUES ('value') ON DUPLICATE KEY UPDATE `y`=2");




//INSERT statement - with ON DUPLICATE KEY returning row ID
$db->string()->insertUpdate('table', [
	'column1' => 1,
	'column2' => 2,
], 'column1');

pudlTest('INSERT INTO `table` (`column1`, `column2`) VALUES (1, 2) ON DUPLICATE KEY UPDATE `column1`=LAST_INSERT_ID(`column1`)');



//INSERT statement - with ON DUPLICATE KEY returning row ID, using custom UPDATE syntax
$db->string()->insertUpdate(
	'table',
	['column1' => 1, 'column2' => 2],
	'column1',
	['column3' => 3]
);

pudlTest('INSERT INTO `table` (`column1`, `column2`) VALUES (1, 2) ON DUPLICATE KEY UPDATE `column3`=3, `column1`=LAST_INSERT_ID(`column1`)');




$db->string()->replace('table', ['column'=>'value']);
pudlTest("REPLACE INTO `table` (`column`) VALUES ('value')");




$db->string()->insert('table', [
	'column1' => 1,
	'column2' => 2,
], 'column1', false);
pudlTest('INSERT INTO `table` VALUES (1, 2) ON DUPLICATE KEY UPDATE `column1`=LAST_INSERT_ID(`column1`)');




$db->string()->insert('table', [
	'column1' => 1,
	'column2' => 2,
], 'column1', true);
pudlTest('INSERT INTO `table` (`column1`, `column2`) VALUES (1, 2) ON DUPLICATE KEY UPDATE `column1`=LAST_INSERT_ID(`column1`)');




$db->string()->insertValues('table', [
	'column1'=>'value1',
	'column2'=>'value2',
]);
pudlTest("INSERT INTO `table` VALUES ('value1', 'value2')");




$testdata = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$db->string()->insert('table', pudl::extract($testdata, ['b','d']));
pudlTest('INSERT INTO `table` (`b`, `d`) VALUES (2, 4)');




$testdata = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$db->string()->insert('table', pudl::extract($testdata, 'a','c'));
pudlTest('INSERT INTO `table` (`a`, `c`) VALUES (1, 3)');




$testdata = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$db->string()->insert('table', pudl::extract($testdata, ['b'=>1,'c'=>1]));
pudlTest('INSERT INTO `table` (`b`, `c`) VALUES (2, 3)');




//INSERT statement - removing table prefix
$db->string()->insert('table', ['prefix.column'=>'test']);
pudlTest("INSERT INTO `table` (`column`) VALUES ('test')");




//UPSERT statement
$db->string()->upsert('table', ['column'=>'value']);
pudlTest("INSERT INTO `table` (`column`) VALUES ('value') ON DUPLICATE KEY UPDATE `column`='value'");




//UPSERT statement
$db->string()->upsert('table', ['column'=>'value'], 'id');
pudlTest("INSERT INTO `table` (`column`) VALUES ('value') ON DUPLICATE KEY UPDATE `column`='value', `id`=LAST_INSERT_ID(`id`)");




$db->string()->upsert('table', ['column'=>['param'=>[1,2,3]]]);
pudlTest("INSERT INTO `table` (`column`) VALUES ('{\\\"param\\\":[1,2,3]}') ON DUPLICATE KEY UPDATE `column`=JSON_SET(IFNULL(NULLIF(TRIM(`column`), ''), '{}'),'$.param',JSON_COMPACT('[1,2,3]'))");



//TODO: THIS IS BUGGED
//THIS SHOULD CREATE: JSON_COMPACT('[1,2,3]')
//NOT: JSON_SET(IFNULL(NULLIF(TRIM(`column`), ''), '{}'),'$.0',1,'$.1',2,'$.2',3)
//$db->string()->upsert('table', ['column'=>[1,2,3]]);
//pudlTest('sss');