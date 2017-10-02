<?php

namespace Stuzzo\Monolog\Processor;

/**
 * Add Request data to the record generated by logger
 *
 * @author Alfredo Aiello <stuzzo@gmail.com>
 */
class SlackProcessor
{
	public function __invoke(array $record)
	{
		foreach ($record['context'] as $key => $val) {
			if (!($val instanceof \Exception)) {
				continue;
			}
			
			$record['message'] = sprintf(
				'Uncaught PHP Exception %s %s at %s line %s :scream:',
				get_class($val),
				$val->getMessage(),
				$val->getFile(),
				$val->getLine() . PHP_EOL
			);
			
//			$record['extra'] = ['Stack Trace' => sprintf(
//				'%s',
//				$val->getTraceAsString()
//			)];
			
		}
		
		return $record;
	}
	
}