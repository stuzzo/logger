<?php

namespace Stuzzo\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;

class StreamFormatter extends LineFormatter
{
	const NO_MARGIN       = 0;
	const MARGIN_2_SPACES = 2;
	const MARGIN_4_SPACES = 4;
	
	public function format(array $record)
	{
		$this->format = "[%datetime%] %channel%.%level_name%: %message%\n";
		
		if ($record['level'] < 400) {
			return parent::format($record);
		}
		
		return $this->formatMessage($record);
	}
	
	protected function formatMessage(array $record)
	{
		/** @var \Exception $currentException */
		$currentException = reset($record['context']);
		$output           = $this->format;
		
		$message = $this->addEndOfLine('Something gooooes very wrong');
		$message .= $this->addSpacesToString('PHP Exception ->', self::NO_MARGIN);
		$message .= $this->addSpacesToString(get_class($currentException), self::MARGIN_2_SPACES);
		$message .= $this->addSpacesToString('Message ->', self::NO_MARGIN);
		$message .= $this->addSpacesToString($currentException->getMessage(), self::MARGIN_2_SPACES);
		
		if ($record['extra']) {
			$message .= $this->addSpacesToString('Request ->', self::NO_MARGIN);
			$message .= $this->addSpacesToString('Method: ' . $record['extra']['http_method'], self::MARGIN_2_SPACES);
			$message .= $this->addSpacesToString('URL: ' . $record['extra']['url'], self::MARGIN_2_SPACES);
			
			$message .= $this->addSpacesToString('Headers ->', self::MARGIN_2_SPACES);
			foreach ($record['headers'] as $key => $value) {
				$value = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message .= $this->addSpacesToString('Data ->', self::MARGIN_2_SPACES);
			foreach ($record['data'] as $key => $value) {
				$value = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message .= $this->addSpacesToString('Files ->', self::MARGIN_2_SPACES);
			foreach ($record['files'] as $key => $value) {
				$value = is_array($value) ? json_encode($value) : $value;
				$message .= $this->addSpacesToString("$key: $value", self::MARGIN_4_SPACES);
			}
			
			$message .= $this->addSpacesToString('Stack Trace ->', self::NO_MARGIN);
			foreach ($currentException->getTrace() as $trace) {
				if (!empty($trace['file'])) {
					$traceMessage = sprintf('at %s line %s', $trace['file'], $trace['line']);
					$message .= $this->addSpacesToString($traceMessage, self::MARGIN_2_SPACES);
				}
			}
		}
		$record['message'] = $message;
		
		$vars = $this->normalize($record);
		
		foreach ($vars as $var => $val) {
			if (false !== strpos($output, '%' . $var . '%')) {
				$output = str_replace('%' . $var . '%', $this->stringify($val), $output);
			}
		}
		
		return $output;
	}
	
	private function addEndOfLine($string)
	{
		return $string . PHP_EOL;
	}
	
	private function addSpacesToString($string, $margin)
	{
		return str_repeat(' ', $margin) . $string . PHP_EOL;
	}
	
}