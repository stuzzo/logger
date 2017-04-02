<?php

namespace Stuzzo\Monolog\Service;

class ExecutionService
{
	public static function isGeneratedFromCommandLineInterface()
	{
		return PHP_SAPI === 'cli';
	}
	
	public static function getExceptionFromRecord($record)
	{
		$exceptionRecord = false;
		if (empty($record) || empty($record['context'])) {
			return $exceptionRecord;
		}
		
		$currentContext = $record['context'];
		foreach ($currentContext as $contextElement) {
			if ($contextElement instanceof \Exception) {
				$exceptionRecord = $contextElement;
				break;
			}
		}
		
		return $exceptionRecord;
	}
}