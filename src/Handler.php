<?php
namespace pieni\Sync;

class Handler
{
	public function __construct($name, $drivers)
	{
		$this->name = $name;
		foreach ($drivers as $driver) {
			$driver[1]['handler'] = $this;
			$this->drivers[] = new $driver[0]($driver[1]);
		}
	}

	public function get($name = '')
	{
		$latest = ['mtime' => -2];
		foreach ($this->drivers as $index => $driver) {
			if (($mtimes[$index] = $driver->mtime($name)) > $latest['mtime']) {
				$latest = ['mtime' => $mtimes[$index], 'index' => $index];
			}
		}
		$data = $this->drivers[$latest['index']]->get($name);
		foreach ($this->drivers as $index => $driver) {
			if ($mtimes[$index] < $latest['mtime']) {
				$driver->put($data, $latest['mtime'], $name);
			}
		}
		return $data;
	}
}
