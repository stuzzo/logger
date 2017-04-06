<?php

namespace Stuzzo\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;
use Stuzzo\Monolog\Service\ExecutionService;

class StreamFormatter extends LineFormatter
{
	const NO_MARGIN       = 0;
	const MARGIN_2_SPACES = 2;
	const MARGIN_4_SPACES = 4;
	const LOG_EXCEPTION   = 'EXCEPTION';
	const LOG_STACK_TRACE = 'STACK TRACE';
	const LOG_DOUBLE_DOTS = ': ';
	
	public function format(array $record)
	{
		return $this->formatMessage($record);
	}
	
	protected function formatMessage(array $record)
	{
		$output = $this->format;
		$this->allowInlineLineBreaks(true);
		
		$isGeneratedFromCommandLineInterface = $this->isGeneratedFromCommandLineInterface();
		if ($isGeneratedFromCommandLineInterface) {
			$record = $this->addCLIData($record);
		} else {
			$record = $this->addRequestData($record);
		}
		
		$vars = $this->normalize($record);
		foreach ($vars as $var => $val) {
			if (false !== strpos($output, '%' . $var . '%')) {
				$output = str_replace('%' . $var . '%', $this->stringify($val), $output);
			}
		}
		
		return $output;
	}
	
	private function addSpacesToString($string, $margin)
	{
		return str_repeat(' ', $margin) . $string . PHP_EOL;
	}
	
	private function isGeneratedFromCommandLineInterface()
	{
		return ExecutionService::isGeneratedFromCommandLineInterface();
	}
	
	private function addCLIData($record)
	{
		/** @var \Exception $exceptionRecord */
		$exceptionRecord = ExecutionService::getExceptionFromRecord($record);
		if (false === $exceptionRecord) {
			$record['message'] = $this->addSpacesToString($record['message'], self::NO_MARGIN);
		} else {
			$record['message']   = $this->addSpacesToString($exceptionRecord->getMessage(), self::NO_MARGIN);
			$record['exception'] = $this->formatException($exceptionRecord);
		}
		
		$record['extra'] = empty($record['extra']) ? '' : json_encode($record['extra']);
		return $record;
	}
	
	private function addRequestData($record)
	{
		/** @var \Exception $exceptionRecord */
		$exceptionRecord = ExecutionService::getExceptionFromRecord($record);
		if (false === $exceptionRecord) {
			$message = $this->addSpacesToString(self::LOG_MESSAGE . ': ' . $record['message'], self::NO_MARGIN);
		} else {
			$message = $this->addSpacesToString('PHP Exception: ', self::NO_MARGIN);
			$message .= $this->addSpacesToString(get_class($exceptionRecord), self::MARGIN_2_SPACES);
			$message .= $this->addSpacesToString('Message: ', self::NO_MARGIN);
			$message .= $this->addSpacesToString($exceptionRecord->getMessage(), self::MARGIN_2_SPACES);
		}
		
		if ($record['extra']) {
			$message .= $this->addSpacesToString('Request: ', self::NO_MARGIN);
			$message .= $this->addSpacesToString('Method: ' . $record['extra']['http_method'], self::MARGIN_2_SPACES);
			$message .= $this->addSpacesToString('URL: ' . $record['extra']['url'], self::MARGIN_2_SPACES);
			
			$message .= $this->addSpacesToString('Headers: ', self::MARGIN_2_SPACES);
			foreach ($record['headers'] as $key => $value) {
				$value   = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message .= $this->addSpacesToString('Data: ', self::MARGIN_2_SPACES);
			foreach ($record['data'] as $key => $value) {
				$value   = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message .= $this->addSpacesToString('Files: ', self::MARGIN_2_SPACES);
			foreach ($record['files'] as $key => $value) {
				$value   = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message = $this->addExceptionStackTraceFormattedToMessage($exceptionRecord, $message);
		}
		
		return $message;
	}
	
	private function formatException(\Exception $exceptionRecord)
	{
		if (null === $exceptionRecord) {
			return '';
		}
		
		$exceptionMessage           = $this->addSpacesToString(self::LOG_EXCEPTION . self::LOG_DOUBLE_DOTS . get_class($exceptionRecord),
		                                                       self::NO_MARGIN);
		$exceptionStackTraceMessage = $this->addExceptionStackTraceFormattedToMessage($exceptionRecord);
		
		return $exceptionMessage . $exceptionStackTraceMessage;
	}
	
	private function addExceptionStackTraceFormattedToMessage(\Exception $currentException)
	{
		$currentStackTrace = $currentException->getTrace();
		if (empty($currentStackTrace)) {
			$currentStackTrace = debug_backtrace();
		}
		
		$message = $this->addSpacesToString(self::LOG_STACK_TRACE . self::LOG_DOUBLE_DOTS, self::NO_MARGIN);
		foreach ($currentStackTrace as $trace) {
			if (!empty($trace['file'])) {
				$traceMessage = sprintf('at %s line %s', $trace['file'], $trace['line']);
				$message      .= $this->addSpacesToString($traceMessage, self::MARGIN_2_SPACES);
			}
		}
		
		return $message;
	}
	
}