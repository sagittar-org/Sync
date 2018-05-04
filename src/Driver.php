<?php
namespace pieni\Sync;

interface Driver
{
	public function __construct($params = []);
	public function mtime($name = '');
	public function get($name = '');
	public function put($data, $mtime, $name = '');
}
