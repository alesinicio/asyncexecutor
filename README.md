# Async Processor

This a simple class that can help spawning CLI processes, either as "run-and-forget" or "run-and-keep-running" modes.

## Usage

### Run-and-forget
Useful when you want to spawn a CLI process and make it run in the background. You will NOT get any return from the process itself.

Basic usage:

```
$async = new AsyncExecutor('/usr/bin/php');
$async->runProcess('path_to_script', ['param01', 'param02']);
```


### Run-and-keep-running
Useful when you want to spawn CLI processes and keep them running no matter what.

The class monitors the PID of the processes to check if they are still running, and restarts automatically if needed.

Basic usage:

```
$async = new AsyncExecutor('/usr/bin/php');
$multiAsync = new AsyncMultiProcess($async);
$multiAsync->addProcess(new AsyncProcess('instance_01', 'path_to_script_01', ['param01']));
$multiAsync->addProcess(new AsyncProcess('instance_02', 'path_to_script_02', ['param02', 'param03']));
$multiAsync->keepRunningProcesses();
```

If a process fails to execute due to a non-existing script, you can set the time the class will wait to retry the execution (default, 5 seconds).

You can also configure the AsyncMultiProcess to abort execution if non-existing script is detected, effectively canceling everything (running processes will not be closed, though).