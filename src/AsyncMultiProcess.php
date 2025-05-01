<?php
namespace Alesinicio\AsyncExecutor;

use Exception;
use Psr\Log\LoggerInterface;

class AsyncMultiProcess {
	private bool $stopOnException       = true;
	private int  $processRestartTimeout = 5;
	/**
	 * @var array<AsyncProcess>
	 */
	private array $processes = [];
	/**
	 * @var array<string>
	 */
	private array $processRestartPends = [];

	public function __construct(
		private readonly AsyncExecutor    $async,
		private readonly ?LoggerInterface $logger = null,
	) {}
	/**
	 * Adds a process to the list of process to be managed.
	 * Does NOT execute immediatelly.
	 *
	 * @param AsyncProcess $process
	 */
	public function addProcess(AsyncProcess $process) : void {
		$this->processes[$process->name] = $process;
	}
	/**
	 * If set to TRUE, any FileNotFoundException will be rethrown and should be captured/handled by the user.
	 * The whole control flow will stop, so user should restart the process.
	 *
	 * If set to FALSE, any FileNotFoundException will be ignored.
	 * The class will try to start the process again after the configured time (::setRestartTimeout)
	 *
	 * @param bool $stopOnException
	 */
	public function setStopOnException(bool $stopOnException) : void {
		$this->stopOnException = $stopOnException;
	}
	/**
	 * Sets the time the class will wait to try to restart a process whose execution triggered a FileNotFoundException.
	 *
	 * @param int $seconds
	 */
	public function setRestartTimeout(int $seconds) : void {
		$this->processRestartTimeout = $seconds;
	}
	/**
	 * Executes the processes that were added and keep them running forever.
	 * @throws Exception
	 */
	public function keepRunningProcesses() : void {
		while (true) {
			foreach ($this->processes as $process) {
				if ($process->pid && $this->async->isProcessRunning($process->pid)) continue;
				if ($this->isProcessUnderRestartTimeout($process)) continue;
				$this->startProcess($process);
			}
			usleep(10_000);
		}
	}
	/**
	 * @param AsyncProcess $process
	 * @return void
	 * @throws FileNotFoundException
	 * @throws Exception
	 */
	protected function startProcess(AsyncProcess $process) : void {
		unset($this->processRestartPends[$process->name]);
		$this->logger?->debug('Starting process ' . $process->name);
		try {
			$process->pid = $this->async->runProcess($process->path, $process->params);
		} catch (FileNotFoundException $e) {
			if ($this->stopOnException) throw $e;

			$timeout = strtotime('+' . $this->processRestartTimeout . ' seconds');
			if (!$timeout) throw new Exception('Unexpected return');

			$this->processRestartPends[$process->name] = $timeout;
		}
	}
	private function isProcessUnderRestartTimeout(AsyncProcess $process) : bool {
		$waitUntil = $this->processRestartPends[$process->name] ?? null;
		if (!$waitUntil) return false;

		return date('U') > $waitUntil;
	}
}