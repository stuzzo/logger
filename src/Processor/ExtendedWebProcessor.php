<?php

namespace Stuzzo\Monolog\Processor;

use Monolog\Processor\WebProcessor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Injects url/method and remote IP of the current web request in all records
 *
 * @author Alfredo Aiello <stuzzo@gmail.com>
 */
class ExtendedWebProcessor extends WebProcessor
{
	
	public function __construct($serverData = null, $extraFields = null)
	{
		parent::__construct($serverData, $extraFields);
		$this->extraFields = array_merge($this->extraFields, [
			'protocol' => 'REQUEST_SCHEME',
		]);
	}
	
	public function __invoke(array $record)
	{
		$record = parent::__invoke($record);
		
		if (!empty($GLOBALS['request']) && $GLOBALS['request'] instanceof Request) {
			
			/** @var Request $request */
			$request           = $GLOBALS['request'];
			$record['headers'] = $request->headers->all();
			$record['files']   = $request->files->all();
			if ($request->getMethod() === 'POST') {
				$record['data'] = $request->request->all();
			} else {
				$record['data'] = $request->query->all();
			}
			
		}
		
		return $record;
	}
}
