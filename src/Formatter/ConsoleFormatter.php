<?php

namespace Stuzzo\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;
use Stuzzo\Monolog\Service\ExecutionService;

class ConsoleFormatter extends LineFormatter
{
	const NO_MARGIN       = 0;
	const MARGIN_2_SPACES = 2;
	const MARGIN_4_SPACES = 4;
	const LOG_EXCEPTION   = 'EXCEPTION';
	const LOG_STACK_TRACE = 'STACK TRACE';
	const LOG_DOUBLE_DOTS = ': ';
	
	public function __construct($format = null, $dateFormat = null, $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = false)
	{
		parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
		$this->includeStacktraces();
	}
	
	public function format(array $record)
	{
		$record = $this->normalizeMessage($record);
		
		$output = parent::format($record);
		if ($this->allowInlineLineBreaks) {
			$output = $this->replaceNewlinesRemained($output);
		}
		
		return $output;
	}
	
	protected function replaceNewlinesRemained($str)
	{
		return str_replace(['\r', '\n'], ["\r", "\n"], $str);
	}
	
	protected function normalizeMessage($record)
	{
		/** @var \Exception $exceptionRecord */
		$exceptionRecord = ExecutionService::getExceptionFromRecord($record);
		if (false !== $exceptionRecord) {
			$record['message'] = $exceptionRecord->getMessage();
		}
		
		return $record;
	}
	
	protected function normalizeException($e)
	{
		// TODO 2.0 only check for Throwable
		if (!$e instanceof \Exception && !$e instanceof \Throwable) {
			throw new \InvalidArgumentException('Exception/Throwable expected, got ' . gettype($e) . ' / ' . get_class($e));
		}
		
		$previousText = 'PREVIOUS EXCEPTION(S): ';
		if ($previous = $e->getPrevious()) {
			do {
				$previousText .= get_class($previous) . '(code: ' . $previous->getCode() . ') at ' . $previous->getFile() . ' line ' . $previous->getLine() . "\n";
			} while ($previous = $previous->getPrevious());
			$previousText = $this->removeCarriageReturn($previousText);
		} else {
			$previousText = '';
		}
		
		$str = 'EXCEPTION: ' . get_class($e) . '(code: ' . $e->getCode() . ') at ' . $e->getFile() . ' line ' . $e->getLine();
		if ('' !== $previousText) {
			$str .= "\n" . $previousText;
		}
		if ($this->includeStacktraces) {
			$currentStackTrace = $e->getTrace();
			if (empty($currentStackTrace)) {
				$currentStackTrace = debug_backtrace();
			}
			
			$str .= "\n" . $this->addSpacesToString(self::LOG_STACK_TRACE . self::LOG_DOUBLE_DOTS, self::NO_MARGIN);
			foreach ($currentStackTrace as $trace) {
				if (!empty($trace['file'])) {
					$traceMessage = sprintf('at %s line %s', $trace['file'], $trace['line']);
					$str          .= $this->addSpacesToString($traceMessage, self::MARGIN_2_SPACES);
				}
			}
			$str = $this->removeCarriageReturn($str);
		}
		
		return $str;
	}
	
	private function addSpacesToString($string, $margin)
	{
		return str_repeat(' ', $margin) . $string . PHP_EOL;
	}
	
	private function removeCarriageReturn($string)
	{
		return rtrim($string);
	}
}