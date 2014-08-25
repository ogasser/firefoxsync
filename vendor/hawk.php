<?php

namespace OCA\FirefoxSync\Vendor;

/**
 * A class to generate and parse Hawk authentication headers
 *
 * @author		Alex Bilbie | www.alexbilbie.com | hello@alexbilbie.com
 * @copyright	Copyright (c) 2012, Alex Bilbie.
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @link		http://alexbilbie.com
 */
class Hawk {

	/**
	 * Generate the MAC
	 * @param  string $secret The shared secret
	 * @param  array  $params The MAC data parameters
	 * @return string         The base64 encode MAC
	 */
	public static function generateMac($secret = '', $params = array())
	{
		$default = array(
			'timestamp'	=>	time(),
			'method'	=>	'GET',
			'path'	=>	'',
			'host'	=>	'',
			'port'	=>	80,
			'ext'	=>	null
		);

		// Only include the necessary parameters
		foreach (array_keys($default) as $key)
		{
			if (isset($params[$key]))
			{
				$default[$key] = $params[$key];
			}
		}

		// Nuke the ext key if it isn't being used
		if ($default['ext'] === null)
		{
			unset($default['ext']);
		}

		// Ensure the method parameter is uppercase
		$default['method'] = strtoupper($default['method']);

		// Generate the data string
		$data = implode("\n", $default);

		// Generate the hash
		$hash = hash_hmac('sha256', $data, $secret);

		// Return base64 value
		return base64_encode($hash);
	}

	/**
	 * Generate the full Hawk header string
	 * @param  string $key    The identifier key
	 * @param  string $secret The shared secret
	 * @param  array  $params The MAC data parameters
	 * @return string         The Hawk header string
	 */
	public static function generateHeader($key = '', $secret = '', $method = 'GET', $url = array(), $appData = array())
	{
		$url = parse_url($url);

		if ( ! isset($url['port']))
		{
			$params['port'] = ($url['scheme'] === 'https') ? 443 : 80;
		} else {
			$params['port'] = $url['port'];
		}

		$params['host'] = $url['host'];
		$params['path'] = $url['path'] . (isset($url['query']) ? $url['query'] : '');
		$params['method'] = $method;
		$params['ext'] = (count($appData) > 0) ? http_build_query($appData) : null;
		$params['timestamp'] = (isset($params['timestamp'])) ? $params['timestamp'] : time();
		die(var_dump($params));

		// Generate the MAC address
		$mac = self::generateMac($secret, $params);

		// Make the header string
		$header = 'Hawk id="'.$key.'", ts="'.$params['timestamp'].'", ';
		$header .= (isset($params['ext'])) ? 'ext="'.$params['ext'].'", ' : '';
		$header .= 'mac="'.$mac.'"';

		return $header;
	}

	/**
	 * Parse the Hawk header string into an array of parts
	 * @param  string $hawk The Hawk header
	 * @return array        The induvidual parts of the Hark header
	 */
	public static function parseHeader($hawk = '')
	{
		$segments = explode(', ', substr(trim($hawk), 5, -1));

		$parts['id'] = substr($segments[0], 4, strlen($segments[0])-5);
		$parts['timestamp'] = substr($segments[1], 4, strlen($segments[1])-5);
		$parts['mac'] = (count($segments) === 4) ? substr($segments[3], 5, strlen($segments[3])) : substr($segments[2], 5, strlen($segments[2]));
		$parts['ext'] = (count($segments) === 4) ? substr($segments[2], 5, strlen($segments[2])-6) : null;

		if ($parts['ext'] === null)
		{
			unset($parts['ext']);
		}

		return $parts;
	}

	/**
	 * Verify the received Hawk header
	 * @param  string $hawk   The Hawk header string
	 * @param  array  $params The MAC data parameters
	 * @param  string $secret The shared secret
	 * @return bool           True if the header validates, otherwise false
	 */
	public static function verifyHeader($hawk = '', $params = array(), $secret = '')
	{
		// Parse the header
		$parts =  self::parseHeader($hawk);

		$params['timestamp'] = $parts['timestamp'];

		if (isset($parts['ext'])) {
			$params['ext'] = $parts['ext'];
		}

		// Generate the MAC
		$test = self::generateMac($secret, $params);

		// Test against the received MAC
		return ($test === $parts['mac']);
	}

}
