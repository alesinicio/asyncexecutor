<?php
namespace alesinicio\AsyncExecutor;

class AsyncExecutor {
	protected string $interpreter;
	
	public function __construct(string $interpreterPath='php') {
		$this->interpreter = $interpreterPath;
	}
	/**
	 * Run process as background service.
	 * 
	 * @param string $scriptPath
	 * @param array<string> $params
	 * @return int PID of process
	 */
	public function runProcess(string $scriptPath, array $params=[]) : int {
		if ($scriptPath && !file_exists($scriptPath)) throw new FileNotFoundException($scriptPath);
		
		$command = [$this->interpreter, $scriptPath, ...explode(';', str_repeat('%s;', count($params)))];
		$command = implode(' ', $command);
		$command = sprintf($command.' > /dev/null 2>&1 & echo $!;', ...$params);
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