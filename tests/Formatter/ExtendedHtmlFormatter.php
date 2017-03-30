<?php

namespace Logger\Formatter;

use Monolog\Formatter\HtmlFormatter;

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
		 * Se nel record non mi arriva l'eccezione, allora restituisco la versione base
		 * del Formatter
		 */
		if (empty($record['context'])) {
			return parent::format($record);
		}
		
		$output = $this->addTitle($record['level_name'], $record['level']);
		$output .= '<table cellspacing="1" width="100%" class="monolog-output">';
		
		/** @var \Exception $currentException */
		$currentException = reset($record['context']);
		$extraFields      = !empty($record['extra']) ? $record['extra'] : [];
		$headers          = !empty($record['headers']) ? $record['headers'] : [];
		$files            = !empty($record['files']) ? $record['files'] : [];
		$data             = !empty($record['data']) ? $record['data'] : [];
		
		$output .= $this->addRow('Message', (string) $currentException->getMessage());
		$output .= $this->addRow('Time', $record['datetime']->format($this->dateFormat));
		$output .= $this->addRow('Channel', $record['channel']);
		
		$output .= $this->addRow('Exception', get_class($currentException));
		$traceMessage = '';
		foreach ($currentException->getTrace() as $trace) {
			if (!empty($trace['file'])) {
				$traceMessage .= sprintf('at %s line %s' . PHP_EOL, $trace['file'], $trace['line']);
			}
		}
		$output .= $this->addRow('Trace', $traceMessage);
		
		if ($extraFields) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($extraFields as $key => $value) {
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output .= $this->addRow('Request', $embeddedTable, false);
		}
		
		if ($data) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($data as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output .= $this->addRow('Data', $embeddedTable, false);
		}
		
		if ($files) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($files as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output .= $this->addRow('Files', $embeddedTable, false);
		}
		
		if ($headers) {
			$embeddedTable = '<table cellspacing="1" width="100%">';
			foreach ($headers as $key => $value) {
				if (is_array($value) && 1 === count($value)) {
					$value = reset($value);
				}
				$embeddedTable .= $this->addRow($this->formatKey($key), $this->convertToString($value));
			}
			$embeddedTable .= '</table>';
			$output .= $this->addRow('Headers', $embeddedTable, false);
		}
		
		return $output . '</table>';
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