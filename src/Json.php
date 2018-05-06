<?php
namespace pieni\Sync;

class Json implements Driver
{
	public function __construct($params = [])
	{
		$this->handler = $params['handler'];
		$this->path = $params['path'];
	}

	public function mtime($name = '')
	{
		return file_exists($this->file($name)) ? filemtime($this->file($name)) : -1;
	}

	public function get($name = '')
	{
		return json_decode(file_get_contents($this->file($name)), true);
	}

	public function put($data, $mtime, $name = '')
	{
		@mkdir(dirname($this->file($name)), 0755, true);
		file_put_contents($this->file($name), json_encode($data, JSON_PRETTY_PRINT)."\n");
		touch($this->file($name), $mtime);
	}

	private function file($name)
	{
		return "{$this->path}/".trim("{$this->handler->name}.{$name}", '.').".json";
	}
}
