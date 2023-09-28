<?php
namespace alesinicio\AsyncExecutor;

class AsyncExecutor {
	public function __construct(
		private readonly string $interpreterPath = 'php',
		private readonly string $defaultOutputPath = '/dev/null',
	) {}
	/**
	 * Run process as background service.
	 *
	 * @param string      $scriptPath
	 * @param array       $params
	 * @param string|null $outputPath
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