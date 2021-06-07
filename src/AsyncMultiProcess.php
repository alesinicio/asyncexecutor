<?php
namespace alesinicio\AsyncExecutor;

use Psr\Log\LoggerInterface;

class AsyncMultiProcess {
	protected bool $stopOnException = true;
	protected AsyncExecutor $async;
	protected ?LoggerInterface $logger;
	protected int $processRestartTimeout = 5;
	/**
	 * @var array<AsyncProcess>
	 */
	protected array $processes = [];
	/**
	 * @var array<string>
	 */
	protected array $processRestartPends = [];
	
	public function __construct(AsyncExecutor $async, LoggerInterface $logger=null) {
		$this->logger = $logger;
		$this->async = $async;
	}
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
	 */
	public function keepRunningProcesses() : void {
		while(true) {
			foreach($this->processes as $process) {
				if ($process->pid && $this->async->isProcessRunning($process->pid)) continue;

				$waitUntil = $this->processRestartPends[$process->name] ?? null;
				if ($waitUntil && (date('U') > $waitUntil)) continue;
				unset($this->processRestartPends[$process->name]);
				
				if ($this->logger) $this->logger->debug('Starting process '.$process->name);
				try {
					$process->pid = $this->async->runProcess($process->path, $process->params);
				} catch (FileNotFoundException $e) {
					if ($this->stopOnException) throw new FileNotFoundException($e->getMessage());
					
					$timeout = strtotime('+'.$this->processRestartTimeout.' seconds');
					if (!$timeout) throw new \Exception('Unexpected return');
					
					$this->processRestartPends[$process->name] = date('U', $timeout);
				}
			}
		}
	}
}