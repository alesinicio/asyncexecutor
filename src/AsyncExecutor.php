<?php
namespace alesinicio\AsyncExecutor;

class AsyncExecutor {
	/**
	 * @throws FileNotFoundException
	 */
	public function __construct(
		private readonly string $interpreterPath = PHP_BINARY,
		private readonly string $defaultOutputPath = '/dev/null',
	) {
		if (!file_exists($this->interpreterPath)) throw new FileNotFoundException('Interpreter not found');
	}
	/**
	 * Run process as background service.
	 *
	 * @return int PID of process
	 * @throws FileNotFoundException
	 */
	public function runProcess(string $scriptPath, array $params = [], ?string $outputPath = null) : int {
		if ($scriptPath && !file_exists($scriptPath)) throw new FileNotFoundException($scriptPath);

		$command    = [$this->interpreterPath, $scriptPath, ...explode(';', str_repeat('%s;', count($params)))];
		$command    = implode(' ', $command);
		$outputPath ??= $this->defaultOutputPath;
		$params[]   = $outputPath;
		$command    = sprintf($command . ' > %s 2>&1 & echo $!;', ...(array_map(fn(string $arg) => escapeshellarg($arg), $params)));
		return intval(exec($command));
	}
	/**
	 * Checks if a process is running by its PID.
	 *
	 * @param int $pid
	 * @return bool
	 */
	public function isProcessRunning(int $pid) : bool {
		return (false !== posix_getpgid($pid));
	}
}