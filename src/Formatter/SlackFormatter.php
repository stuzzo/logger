<?php

namespace Stuzzo\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;

class SlackFormatter extends LineFormatter
{
	const NO_MARGIN       = 0;
	const MARGIN_2_SPACES = 2;
	const MARGIN_4_SPACES = 4;
	
	protected $env;
	
	public function format(array $record)
	{
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
		
		$message = $this->addEndOfLine('<!channel> Something gooooes very wrong!');
		$message .= $this->addEndOfLine('Error: ' . $currentException->getMessage());
		$message .= $this->addEndOfLine('PHP Exception: ' . get_class($currentException));
		
		if ($record['extra']) {
			$recordExtra = $record['extra'];
			$message .= $this->addSpacesToString('Method: ' . $record['extra']['http_method'], self::NO_MARGIN);
			$url = $recordExtra['protocol'] . '://' . $recordExtra['server'] . $recordExtra['url'];
			$message .= $this->addSpacesToString('URL: ' . $url, self::NO_MARGIN);
			$message .= $this->addSpacesToString('Env: ' . $this->env, self::NO_MARGIN);
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
	
	public function setEnvironment($env)
	{
		$this->env = $env;
	}
}