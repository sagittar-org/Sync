<?php
namespace pieni\Sync;

class Excel implements Driver
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
		$columns = end($this->handler->drivers)::$columns;
		$spreadsheet = (new \PhpOffice\PhpSpreadsheet\Reader\Xlsx())->load($this->file($name));
		foreach (array_keys($columns) as $table) {
			$data[$table] = [];
			$sheet = $spreadsheet->getSheetByName($table);
			for ($r = 2; ($row = $sheet->getCell('A'.$r)->getValue()) !== NULL; $r++) {
				$data[$table][$row] = [];
				foreach ($columns[$table] as $c => $column) {
					$data[$table][$row][$column] = (string) $sheet->getCellByColumnAndRow($c + 2, $r)->getValue();
				}
			}
		}
		return $data;
	}

	public function put($data, $mtime, $name = '')
	{
		$columns = end($this->handler->drivers)::$columns;
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->removeSheetByIndex(0);
		foreach (array_keys($columns) as $table) {
			$rows = $data[$table];
			$sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $table);
			$spreadsheet->addSheet($sheet);
			$sheet->setCellValueByColumnAndRow(1, 1, 'id');
			foreach ($columns[$table] as $c => $column) {
				$sheet->setCellValueByColumnAndRow($c + 2, 1, $column);
			}
			$r = 2;
			foreach ($rows as $key => $row) {
				$sheet->setCellValueByColumnAndRow(1, $r, $key);
				foreach ($columns[$table] as $c => $column) {
					$sheet->setCellValueExplicitByColumnAndRow($c + 2, $r, $row[$column], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				}
				$sheet->getRowDimension($r)->setRowHeight(-1);
				$r++;
			}
			for ($c = 1; $c <= count($columns[$table]) + 1; $c++) {
				$sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
			}
		}
		$spreadsheet->setActiveSheetIndex(0);
		@mkdir(dirname($this->file($name)), 0755, true);
		(new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($this->file($name));
		touch($this->file($name), $mtime);
	}

	private function file($name)
	{
		return "{$this->path}/".trim("{$this->handler->name}.{$name}", '.').".xlsx";
	}
}
