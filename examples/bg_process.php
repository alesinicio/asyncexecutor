<?php
//A SIMPLE BACKGROUND PROCESS THAT WILL RUN FOR 5 SECONDS AND EXIT
global $argv;
array_shift($argv);

for($i=0; $i<5; $i++) {
	$message = 'iteration '.$i.' -- we received params >> '.implode(' || ', $argv).PHP_EOL;
	file_put_contents(__DIR__.'/bg_process_log.txt', $message, FILE_APPEND);
	sleep(1);
}