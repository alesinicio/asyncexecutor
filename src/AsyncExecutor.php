<?php
namespace alesinicio\AsyncExecutor;

class AsyncExecutor {
	protected string $interpreter;

	public function __construct(
		private readonly string $interpreterPath = 'php',
		private readonly string $defaultOutputPath = '/dev/null',
	) {}

	/**
	 * Run process as background service.
	 *
	 * @param array<string> $params
	 * @throws FileNotFoundException
	 * @return int PID of process
	 */
	public function runProcess(string $scriptPath, array $params = [], ?string $outputPath = null) : int {
		if ($scriptPath && !file_exists($scriptPath)) throw new FileNotFoundException($scriptPath);

		$outputPath ??= $this->defaultOutputPath;
		$command = [$this->interpreterPath, $scriptPath, ...explode(';', str_repeat('%s;', count($params)))];
		$command = implode(' ', $command);
		$params[] = $outputPath;
		$command = sprintf($command . ' > %s 2>&1 & echo $!;', ...$params);
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