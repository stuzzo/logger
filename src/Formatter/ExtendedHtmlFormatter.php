<?php

namespace Stuzzo\Monolog\Formatter;

use Monolog\Formatter\HtmlFormatter;
use Stuzzo\Monolog\Service\ExecutionService;

/**
 * Formats incoming records into an HTML table
 * This is especially useful for html email logging
 *
 * @author Alfredo Aiello <stuzzo@gmail.com>
 */
class ExtendedHtmlFormatter extends HtmlFormatter
{
	/**
	 * Creates an HTML table row
	 *
	 * @param  string $th       Row header content
	 * @param  string $td       Row standard cell content
	 * @param  bool   $escapeTd false if td content must not be html escaped
	 *
	 * @return string
	 */
	protected function addRow($th, $td = ' ', $escapeTd = true)
	{
		$th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
		if ($escapeTd) {
			$td = '<pre>' . htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8') . '</pre>';
		}
		
		return "<tr style=\"padding: 4px;spacing: 0;text-align: left;\">\n<th style=\"background: #cccccc\" width=\"100px\">$th:</th>\n<td style=\"padding: 4px;spacing: 0;text-align: left;background: #eeeeee\">" . $td . "</td>\n</tr>";
	}
	
	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format
	 *
	 * @return mixed The formatted record
	 */
	public function format(array $record)
	{
		/*
		 * The formatter is intented to log if the level is at least 'ERROR'
		 */
		if ($record['level'] < 400) {
			return parent::format($record);
		}
		
		$output = $this->addTitle($record['level_name'], $record['level']);
		$output .= '<table cellspacing="1" width="100%" class="monolog-output">';
		
		/** @var \Exception $exceptionRecord */
		$exceptionRecord = ExecutionService::getExceptionFromRecord($record);
		
		$output .= $this->setMessage($record, $output, $exceptionRecord);
		$output .= $this->addRow('Time', $record['datetime']->format($this->dateFormat));
		$output .= $this->addRow('Channel', $record['channel']);
		
		if (false !== $exceptionRecord) {
			$output .= $this->addRow('Exception', get_class($exceptionRecord));
			$output .= $this->addExceptionStackTraceFormattedToMessage($exceptionRecord, $output);
		}
		
		$output .= $this->addExtraFieldToMessage($record, $output);
		$output .= $this->addRequestDataToMessage($record, $output);
		$output .= $this->addRequestFilesToMessage($record, $output);
		$output .= $this->addRequestHeadersToMessage($record, $output);
		
		return $output . '</table>';
	}
	
	private function setMessage($record, $output, \Exception $exceptionRecord)
	{
		if (false === $exceptionRecord) {
			$output .= $this->addRow('Message', (string) $exceptionRecord->getMessage());
		} else {
			$output .= $this->addRow('Message', $record['message']);
		}
		
		return $output;
	}
	
	private function addExceptionStackTraceFormattedToMessage(\Exception $currentException, $output)
	{
		$currentStackTrace = $currentException->getTrace();
		if (empty($currentStackTrace)) {
			$currentStackTrace = debug_backtrace();
		}
		
		$traceMessage = '';
		foreach ($currentStackTrace as $trace) {
			if (!empty($trace['file'])) {
				$traceMessage .= sprintf('at %s line %s', $trace['file'], $trace['line']);
			}
		}
		$output .= $this->addRow('Trace', $traceMessage);
		
		return $output;
	}
	
	private function addExtraFieldToMessage($record, $output)
	{
		$extraFields = !empty($record['extra']) ? $record['extra'] : [];
		if ($extraFields) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($extraFields as $key => $value) {
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output        .= $this->addRow('Request', $embeddedTable, false);
		}
		
		return $output;
	}
	
	private function addRequestDataToMessage($record, $output)
	{
		$data = !empty($record['data']) ? $record['data'] : [];
		if ($data) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($data as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output        .= $this->addRow('Data', $embeddedTable, false);
		}
		
		return $output;
	}
	
	private function addRequestFilesToMessage($record, $output)
	{
		$files = !empty($record['files']) ? $record['files'] : [];
		if ($files) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($files as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output        .= $this->addRow('Files', $embeddedTable, false);
		}
		
		return $output;
	}
	
	private function addRequestHeadersToMessage($record, $output)
	{
		$headers = !empty($record['headers']) ? $record['headers'] : [];
		if ($headers) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($headers as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output        .= $this->addRow('Headers', $embeddedTable, false);
		}
		
		return $output;
	}
	
	protected function formatKey($key)
	{
		if ('http_method' === $key) {
			$key = 'method';
		}
		
		if ('ip' === $key || 'url' === $key) {
			$key = strtoupper($key);
		} else {
			$key = ucfirst($key);
		}
		
		return $key;
	}
}