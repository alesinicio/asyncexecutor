<?php
namespace alesinicio\AsyncExecutor;

class AsyncProcess {
	/**
	 * @var string $path
	 * The path for the script to be executed.
	 */
	public string $path;
	/**
	 * @var string $name
	 * String identifier/name of the process. Should be unique among all processes.
	 */
	public string $name;
	/**
	 * @var array<string> $params
	 * Parameters to be passed to the script.
	 */
	public array $params;
	/**
	 * @var null|int $pid
	 * Current PID of the process. If NULL, the process is not being currently executed.
	 */
	public ?int $pid = null;
	
	/**
	 * @param string $name
	 * @param string $path
	 * @param array<string> $params
	 */
	public function __construct(string $name, string $path, array $params=[]) {
		$this->path = $path;
		$this->name = $name;
		$this->params = $params;
	}
}