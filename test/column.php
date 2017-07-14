<?php

//SELECT statement, choosing which columns to return
$db->string()->select('column', 'table');
pudlTest('SELECT column FROM `table`');




//SELECT statement, choosing which columns to return
$db->string()->select(['column'], 'table');
pudlTest('SELECT column FROM `table`');




//SELECT statement, choosing which columns to return
$db->string()->select(['column1', 'column2'], 'table');
pudlTest('SELECT column1, column2 FROM `table`');




//SELECT statement, choosing which columns to return
$db->string()->select(['column1', pudl::unhex('01fa')], 'table');
pudlTest("SELECT column1, UNHEX('01fa') FROM `table`");




//SELECT statement, choosing which columns to return
$db->string()->select(['x'=>'column1', 'y'=>'column2'], 'table');
pudlTest('SELECT `column1` AS `x`, `column2` AS `y` FROM `table`');




//BRAVO CUSTOM FUNCTION
$db->string()->select('*', 'table', pudl::bravo(123, 'column1', 'column2'));
pudlTest('SELECT * FROM `table` WHERE (123 IN (`column1`, `column2`))');




//BRAVO CUSTOM FUNCTION
$db->string()->select('*', 'table', [pudl::bravo(123, 'column3', 'column4')]);
pudlTest('SELECT * FROM `table` WHERE (123 IN (`column3`, `column4`))');




//BRAVO CUSTOM FUNCTION
$db->string()->select('*', 'table', pudl::bravo(123, ['column5', 'column6']));
pudlTest('SELECT * FROM `table` WHERE (123 IN (`column5`, `column6`))');




//BRAVO CUSTOM FUNCTION
$db->string()->select('*', 'table', [pudl::bravo(123, ['column7', 'column8'])]);
pudlTest('SELECT * FROM `table` WHERE (123 IN (`column7`, `column8`))');




//BRAVO CUSTOM FUNCTION
$db->string()->select('*', 'table', [
	pudl::bravo(123, ['column7', 'column8']),
	'column' => 'value',
]);
pudlTest("SELECT * FROM `table` WHERE (123 IN (`column7`, `column8`) AND `column`='value')");
