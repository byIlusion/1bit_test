<?php




/**
 * 	Класс темизации
 */
class Theming {

	private $path;
	private $suffix;
	private $dir;

	public $buffer;

	// Конструктор
	function __construct($conf) {
    $this->path = isset($conf->path) ? $conf->path : '';
    $this->suffix = isset($conf->suffix) ? $conf->suffix : 'php';
	}

	// темизация
	public function theme(string $theme, array $data = array()) {
		foreach ($data as $key => $value) {
			${$key} = $value;
		}

		ob_start('Theming::buffer_callback');
		include($this->path . '/' . $theme . '.' . $this->suffix);
		ob_end_flush();
		return $this->buffer;
	}

	private function buffer_callback($buffer) {
		$this->buffer = $buffer;
	}
}


?>