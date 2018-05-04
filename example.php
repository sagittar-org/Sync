<?php
require_once 'vendor/autoload.php';

class Example implements \pieni\Sync\Driver
{
	public $columns = ['time' => ['value']];

	public function __construct($params = [])
	{
	}

	public function mtime($name = '')
	{
		return 0;
	}

	public function get($name = '')
	{
		return ['time' => ['dummy' => ['value' => time()]]];
	}

	public function put($data, $mtime, $name = '')
	{
	}
}

$database = 'sync';
$db = new mysqli('localhost', 'root', '');

$example = new \pieni\Sync\Handler('example', [
	['\pieni\Sync\Json', ['path' => __DIR__]],
	['\pieni\Sync\Excel', ['path' => __DIR__]],
	['\pieni\Sync\Mysql', ['database' => $database, 'db' => $db]],
	['Example', []],
]);
print_r($example->get());
