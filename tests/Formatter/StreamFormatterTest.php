<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stuzzo\Monolog\Formatter;

/**
 * @covers \Stuzzo\Monolog\Formatter\StreamFormatter
 */
class StreamFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testDefFormatWithString()
    {
        $formatter = new StreamFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'WARNING',
            'level' => 300,
            'channel' => 'log',
            'context' => array(),
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => array(),
        ));
        $this->assertEquals('['.date('Y-m-d').'] log.WARNING: foo [] []'."\n", $message);
    }
	
	public function testDefFormatWithStringError()
	{
		$formatter = new StreamFormatter(null, 'Y-m-d');
		$message = $formatter->format(array(
			                              'level_name' => 'ERROR',
			                              'level' => 400,
			                              'channel' => 'log',
			                              'context' => array(),
			                              'message' => 'foo',
			                              'datetime' => new \DateTime,
			                              'extra' => array(),
		                              ));
		$this->assertEquals('['.date('Y-m-d').']' . PHP_EOL . 'CHANNEL: log' . PHP_EOL . 'LEVEL: ERROR' . PHP_EOL . 'MESSAGE: foo'."\n", $message);
	}
}

class TestFoo
{
    public $foo = 'foo';
}

class TestBar
{
    public function __toString()
    {
        return 'bar';
    }
}
