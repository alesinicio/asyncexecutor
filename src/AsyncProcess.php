<?php
namespace alesinicio\AsyncExecutor;

class AsyncProcess {
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
	public function __construct(
		public readonly string $name,
		public readonly string $path,
		public readonly array $params=[],
	) {}
}