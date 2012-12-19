<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
D-BUS PHP Binding
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?php;dbus

This Source Code Form is subject to the terms of the Mozilla Public License,
v. 2.0. If a copy of the MPL was not distributed with this file, You can
obtain one at http://mozilla.org/MPL/2.0/.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;mpl2
----------------------------------------------------------------------------
#echo(phpDBusVersion)#
#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* This file provides an independent implementation of the D-BUS 1.0
* specification.
*
* @internal  We are using ApiGen to automate the documentation process for
*            creating the Developer's Manual. All sections including these
*            special comments will be removed from the release source code.
*            Use the following line to ensure 76 character sizes:
* ----------------------------------------------------------------------------
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   DBus.php
* @since     v0.1.02
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
/*#ifdef(PHP5n) */

namespace dNG\DBus;
/* #*/
/*#use(direct_use) */
use dNG\data\directFile;
use dNG\data\directXmlParser;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

/**
* This is an abstraction layer for D-BUS communication.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   DBus.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directSession
{
/**
	* @var string $dbus_callbacks Registered D-BUS callbacks
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_callbacks;
/**
	* @var string $dbus_callback_listeners Search list for registered
	*      listeners
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_callback_listeners;
/**
	* @var string $dbus_guid D-BUS GUID
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_guid;
/**
	* @var directMessages $dbus_raw D-BUS message handler
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_messages;
/**
	* @var object $event_handler The EventHandler is called whenever debug messages
	*      should be logged or errors happened.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $event_handler;
/**
	* @var boolean $nle True if we are on a native little endian
	*      system
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $nle;
/**
	* @var boolean $PHP_socket True if fsockopen is available
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $PHP_socket;
/**
	* @var resource $socket_dbus Opened D-BUS socket
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $socket_dbus;
/**
	* @var string $dbus_cookie_owner Owner for the DBUS_COOKIE_SHA1 path.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_cookie_owner;
/**
	* @var string $dbus_cookie_path User given path to the DBUS_COOKIE_SHA1
	*      directory.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_cookie_path;
/**
	* @var string $socket_path UNIX socket path
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $socket_path;
/**
	* @var integer $socket_timeout Operation timeout
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $socket_timeout;
/**
	* @var integer $timeout_retries Retries before timing out
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $timeout_retries = 5;
/**
	* @var integer $xml_parser Allocated XML parser instance
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $xml_parser;

/* -------------------------------------------------------------------------
Construct the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directSession)
	*
	* @param string $path Path to the socket to connect to
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct($path, $event_handler = NULL)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -dbus->__construct(directSession)- (#echo(__LINE__)#)"); }

		$this->dbus_callbacks = array();
		$this->dbus_callback_listeners = "\n";
		$this->dbus_cookie_path = NULL;
		$this->dbus_cookie_owner = NULL;
		$this->dbus_guid = "";
		$this->dbus_messages = NULL;
		$this->event_handler = $event_handler;

		if (pack("S", 1) == "\x01\x00") { $this->nle = true; }
		else { $this->nle = false; }

		$this->PHP_socket = function_exists("fsockopen");
		$this->socket_dbus = NULL;

		if (class_exists(/*#ifdef(PHP5n) */'dNG\DBus\directMessage'/* #*//*#ifndef(PHP5n):"directMessage":#*/) && class_exists(/*#ifdef(PHP5n) */'dNG\DBus\directMessages'/* #*//*#ifndef(PHP5n):"directMessages":#*/)) { $this->socket_path = ((stripos($path, "unix:abstract://") === 0) ? preg_replace("#unix:abstract:\/\/#i", "unix://\x00", $path) : $path); }
		$this->socket_timeout = 15;
		$this->xml_parser = NULL;
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directSession
	*
	* @param string $path Path to the socket to connect to
	* @param object $event_handler EventHandler to use
	* @since v0.1.01
*\/
	function directSession($path, $event_handler = NULL) { $this->__construct($path, $event_handler); }
:#\n*/
/**
	* Destructor (PHP5) __destruct (directSession)
	*
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __destruct() { $this->disconnect(); }

/**
	* Converts data to an hex string in a binary safe way.
	*
	* @param  string $data Data to encode/decode to hexadecimal 
	* @param  boolean $decode False to encode
	* @return mixed Read line on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authHex($data, $decode = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authHex($data, +decode)- (#echo(__LINE__)#)"); }
		$return = false;

		if ($decode) { $return = @pack("H*", $data); }
		else
		{
			$return = @unpack("H*hexdata", $data);
			if (isset($return['hexdata'])) { $return = $return['hexdata']; }
		}

		return $return;
	}

/**
	* Reads data during an authentication process.
	*
	* @return mixed Read line on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authRead()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authRead()- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_resource($this->socket_dbus))
		{
			$data_read = "";
			$streams_read = array($this->socket_dbus);
			$streams_ignored = NULL;
			$timeout_time = time() + $this->socket_timeout;

			while ((!feof($this->socket_dbus) && strpos($data_read, "\r\n") < 1 && time() < $timeout_time))
			{
/*#ifndef(PHP4) */
				stream_select($streams_read, $streams_ignored, $streams_ignored, $this->socket_timeout);
/* #\n*/
				$data_read .= fread($this->socket_dbus, 4096);
			}

			if (strpos($data_read, "\r\n") > 0)
			{
				if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authRead()- (#echo(__LINE__)#) read ".$data_read); }
				$return = trim($data_read);
			}
		}

		return $return;
	}

/**
	* Reads and parses the response in the authentication process.
	* 
	* @param  boolean $return_response True to return the response
	* @return mixed Either an array ([0] command / result [1] data) or true on
	*         success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authReadParseResponse($return_response = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authReadParseResponse(+return_response)- (#echo(__LINE__)#)"); }

		$return = false;
		$data_read = $this->authRead();

		if($data_read)
		{
			$data = explode(" ", $data_read, 2);

			if (count($data) == 2)
			{
				if ($return_response) { $return = $data; }
				elseif ($data[0] == "DATA" || $data[0] == "OK") { $return = true; }
			}
		}

		return $return;
	}

/**
	* Writes data to the socket during an authentication process.
	*
	* @param  string $data Data for the authentication protocol to send.
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authWrite($data)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authWrite($data)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_resource($this->socket_dbus))
		{
			$data = str_replace(array("\r", "\n"), "", $data);
			$return = $this->write($data."\r\n");
		}

		return $return;
	}

/**
	* Writes data to the socket during an authentication process and parses the
	* response.
	*
	* @param  string $data Data for the authentication protocol to send. 
	* @param  boolean $return_response True to return the response
	* @return mixed Data array on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authWriteParseResponse($data, $return_response = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->authWriteParseResponse($data, +return_response)- (#echo(__LINE__)#)"); }

		$return = false;
		if ($this->authWrite($data)) { $return = $this->authReadParseResponse($return_response); }

		return $return;
	}

/**
	* Reads an incoming message and delegates it if requested.
	*
	* @param directMessage &$message directMessage object
	* @param array $body Body array if applicable
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callback(&$message, $body)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callback(+message, +body)- (#echo(__LINE__)#)"); }

		if (is_object($message) && is_array($body))
		{
			$header_array = $message->getHeader(5);
			$type = $message->getHeader("type");
			$is_valid = is_string($type);

			if ($is_valid && ($type == "method_return" || $type == "error") && $header_array && isset($this->dbus_callbacks[$header_array[1]]))
			{
				$this->callbackCaller($this->dbus_callbacks[$header_array[1]], $message, $body);
				unset($this->dbus_callbacks[$header_array[1]]);
			}

/* -------------------------------------------------------------------------
Listeners
------------------------------------------------------------------------- */

			if ($is_valid)
			{
				$listener = "(\*|".(preg_quote($type)).")";

				for ($i = 1;$i < 4;$i++)
				{
					$header_array = $message->getHeader($i);
					$listener .= ($header_array ? "\:(\*|".preg_quote($header_array[1]).")" : "\:\*");
				}

				$listener = "($listener)";
			}

			if ($is_valid && preg_match_all("#^$listener$#im", $this->dbus_callback_listeners, $listeners, PREG_SET_ORDER))
			{
				foreach ($listeners as $listener)
				{
					if (isset($this->dbus_callbacks[$listener[1]])) { $this->callbackCaller($this->dbus_callbacks[$listener[1]], $message, $body); }
				}
			}
		}
	}

/**
	* Calls found callback registrations.
	*
	* @param array $callbacks Found callbacks
	* @param directMessage &$message directMessage object
	* @param array $body Body array if applicable
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callbackCaller($callbacks, &$message, $body)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackCaller(+callbacks, +message, +body)- (#echo(__LINE__)#)"); }

		if (is_array($callbacks) && is_object($message) && is_array($body))
		{
			foreach ($callbacks as $callback)
			{
				if (is_string($callback) && function_exists($callback)) { $callback($message, $body); }
				elseif (is_array($callback) && count($callback) == 2 && isset($callback[0]) && isset($callback[1]) && method_exists($callback[0], $callback[1])) { $callback[0]->{$callback[1]}($message, $body); }
			}
		}
	}

/**
	* Checks if a callback is reachable from within this object.
	*
	* @param  mixed $callback Function name string or array with
	*         (&$object, "method") definition
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackCheck($callback)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackCheck(+callback)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_string($callback) && function_exists($callback)) { $return = true; }
		elseif (is_array($callback) && count($callback) == 2 && isset($callback[0]) && isset($callback[1])) { $return = method_exists($callback[0], $callback[1]); }

		return $return;
	}

/**
	* Waits until a tinmeout occured or the defined amount of messages are
	* parsed.
	*
	* @param  integer $timeout Timeout for incoming messages (in microseconds)
	* @param  integer $messages Number of messages to parse
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackListen($timeout = 0, $messages = 0)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackListen($timeout, $messages)- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { return $this->dbus_messages->callbackListen($timeout, $messages); }
		else { return false; }
	}

/**
	* Registers multiple but unique function or method callbacks for a signal.
	*
	* @param  string $type D-BUS listener type
	* @param  string $path D-BUS path the signal that emits a signal
	* @param  string $interface D-BUS interface
	* @param  string $member D-BUS member (method emitting the signal)
	* @return mixed Signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callbackListenerId($type, $path, $interface, $member)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackListenerId($type, $path, $interface, $member, +callback)- (#echo(__LINE__)#)"); }

		if (is_string($type) && is_string($path) && is_string($interface) && is_string($member))
		{
			if (strlen($type)) { $return = $type; }
			else { $return = "*"; }

			if (strlen($path)) { $return .= ":".$path; }
			else { $return .= ":*"; }

			if (strlen($interface)) { $return .= ":".$interface; }
			else { $return .= ":*"; }

			if (strlen($member)) { $return .= ":".$member; }
			else { $return .= ":*"; }
		}
		else { $return = false; }

		return $return;
	}

/**
	* Registers multiple but unique function or method callbacks for a signal.
	*
	* @param  string $type D-BUS listener type
	* @param  string $path D-BUS path for the signal that emits a signal
	* @param  string $interface D-BUS interface
	* @param  string $member D-BUS member (method emitting the signal)
	* @param  mixed $callback Function name string or array with
	*         (&$object, "method") definition
	* @return mixed Registered signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackRegisterListener($type, $path, $interface, $member, $callback)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackRegisterListener($type, $path, $interface, $member, +callback)- (#echo(__LINE__)#)"); }

		$return = $this->callbackCheck($callback);
		if ($return) { $return = $this->callbackListenerId($type, $path, $interface, $member); }

		if (is_string($return))
		{
			if (!isset($this->dbus_callbacks[$return])) { $this->dbus_callbacks[$return] = array(); }

			if (is_string($callback))
			{
				$this->dbus_callbacks[$return][$callback] = $callback;
				$this->dbus_callback_listeners = str_replace("\n$return\n", "\n", $this->dbus_callback_listeners).$return."\n";
			}
			elseif (is_array($callback))
			{
				$callback_class = get_class($callback[0]);
				$this->dbus_callbacks[$return][$callback_class.".".$callback[1]] = $callback;
				$this->dbus_callback_listeners = str_replace("\n$return\n", "\n", $this->dbus_callback_listeners).$return."\n";
			}
			else { $return = false; }
		}

		return $return;
	}

/**
	* Registers multiple but unique function or method callbacks for the given
	* serial.
	*
	* @param integer $serial Serial used for a method call
	* @param mixed $callback Function name string or array with
	*        (&$object, "method") definition
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackRegisterSerial($serial, $callback)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackRegisterSerial($serial, +callback)- (#echo(__LINE__)#)"); }

		if (!isset($this->dbus_callbacks[$serial])) { $this->dbus_callbacks[$serial] = array(); }

		if (is_string($callback)) { $this->dbus_callbacks[$serial][$callback] = $callback; }
		elseif (is_array($callback))
		{
			$callback_class = get_class($callback[0]);
			if (is_string($callback[1])) { $this->dbus_callbacks[$serial][$callback_class.".".$callback[1]] = $callback; }
		}
	}

/**
	* Removes the registration of the given function or method callbacks from a
	* signal.
	*
	* @param  string $type D-BUS listener type
	* @param  string $path D-BUS path for the signal that emits a signal
	* @param  string $interface D-BUS interface
	* @param  string $member D-BUS member (method emitting the signal)
	* @param  mixed $callback Function name string or array with
	*         (&$object, "method") definition
	* @return mixed Unregistered signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackUnregisterListener($type, $path, $interface, $member, $callback)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callbackUnregisterListener($type, $path, $interface, $member, +callback)- (#echo(__LINE__)#)"); }

		$return = $this->callbackCheck($callback);
		if ($return) { $return = $this->callbackListenerId($type, $path, $interface, $member); }

		if (is_string($return))
		{
			if (is_string($callback))
			{
				if (isset($this->dbus_callbacks[$return][$callback])) { unset($this->dbus_callbacks[$return][$callback]); }
			}
			elseif (is_array($callback))
			{
				$callback_class = get_class($callback[0]);
				if (isset($this->dbus_callbacks[$return][$callback_class.".".$callback[1]])) { unset($this->dbus_callbacks[$return][$callback_class.".".$callback[1]]); }
			}
			else { $return = false; }

			$this->dbus_callback_listeners = str_replace("\n$return\n", "\n", $this->dbus_callback_listeners);
			if (empty($this->dbus_callbacks[$return])) { unset($this->dbus_callbacks[$return]); }
		}

		return $return;
	}

/**
	* Connects to an active D-BUS socket.
	*
	* @param  integer $sync_timeout Timeout for synchronized requests
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function connect($sync_timeout = 3)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->connect($sync_timeout)- (#echo(__LINE__)#)"); }

		$return = false;

		if (is_resource($this->socket_dbus)) { $return = false; }
		elseif ($this->PHP_socket)
		{
			$error_code = 0;
			$error = "";

			if (stripos($this->socket_path, "unix://") === 0) { $this->socket_dbus = @fsockopen($this->socket_path, 0, $error_code, $error, $this->socket_timeout); }
			elseif (preg_match("#^(.+?)\:(\d+)$#", $this->socket_path, $port_array)) { $this->socket_dbus = @fsockopen($port_array[1], $port_array[2], $error_code, $error, $this->socket_timeout); }

			if ($error_code || $error || (!is_resource($this->socket_dbus)))
			{
				if ($this->event_handler !== NULL) { $this->event_handler->error("#echo(__FILEPATH__)# -dbus->connect()- (#echo(__LINE__)#) reports: $error_code:".$error); }
				$this->socket_dbus = NULL;
			}
			else
			{
				$return = true;
				if (!@stream_set_blocking($this->socket_dbus, 0)) { @stream_set_timeout($this->socket_dbus, $this->socket_timeout); }

				$auth_response = $this->authWriteParseResponse("\x00AUTH ", true);
				if (is_array($auth_response)) { $auth_response = explode(" ", $auth_response[1]); }
			}

			if ($return && in_array("EXTERNAL", $auth_response))
			{
				if (function_exists("posix_geteuid")) { $uid = posix_geteuid(); }
				else { $uid = getmyuid(); }

				if (is_numeric($uid))
				{
					$auth_login_response = $this->authWriteParseResponse("AUTH EXTERNAL ".$this->authHex($uid), true);
					if (is_array($auth_login_response) && $auth_login_response[0] == "OK") { $this->dbus_guid = $auth_login_response[1]; }
				}
			}

			if ($return && !$this->dbus_guid && in_array("DBUS_COOKIE_SHA1", $auth_response) && isset($this->dbus_cookie_path))
			{
				if (isset($this->dbus_cookie_owner)) { $username = $this->dbus_cookie_owner; }
				elseif (function_exists("posix_getpwuid"))
				{
					$userdata = posix_getpwuid(posix_geteuid());
					$username = $userdata['name'];
				}
				else { $username = get_current_user(); }

				$auth_response = $this->authWriteParseResponse("AUTH DBUS_COOKIE_SHA1 ".$this->authHex($username), true);
				$return = false;

				if (class_exists(/*#ifdef(PHP5n) */'dNG\data\directFile'/* #*//*#ifndef(PHP5n):"directFile":#*/) && is_array($auth_response) && $auth_response[0] == "DATA")
				{
					$auth_response = explode(" ", $this->authHex($auth_response[1], true), 3);

					if (file_exists($this->dbus_cookie_path."/".$auth_response[0]))
					{
						$file = new directFile();
						$return = $file->open($this->dbus_cookie_path."/".$auth_response[0], true, "r");
					}

					if ($return)
					{
						$file_content = $file->read();
						$file->close();
						if (is_bool($file_content)) { $return = false; }
					}

					if ($return && preg_match("/^".preg_quote($auth_response[1])." .+? (.+?)$/smi", $file_content, $result_array))
					{
						$challenge = $this->authHex(mt_rand());

						$auth_login_response = $this->authWriteParseResponse("DATA ".$this->authHex($challenge." ".sha1($auth_response[2].":".$challenge.":".$result_array[1])), true);
						if (is_array($auth_login_response) && $auth_login_response[0] == "OK") { $this->dbus_guid = $auth_login_response[1]; }
					}
					else { $return = false; }
				}

				if (!$return)
				{
					$auth_response = $this->authWriteParseResponse("CANCEL ", true);
					$return = true;
				}
			}

			if ($return && (!$this->dbus_guid) && in_array("EXTENSION_COOKIE_HMAC_SHA256", $auth_response) && isset($this->dbus_cookie_path) && defined("MHASH_SHA256"))
			{
				$auth_response = $this->authWriteParseResponse("AUTH EXTENSION_COOKIE_HMAC_SHA256", true);
				$return = false;

				if (class_exists(/*#ifdef(PHP5n) */'dNG\data\directFile'/* #*//*#ifndef(PHP5n):"directFile":#*/) && is_array($auth_response) && $auth_response[0] == "DATA")
				{
					$auth_response[1] = $this->authHex($auth_response[1], true);

					if (file_exists($this->dbus_cookie_path))
					{
						$file = new directFile();
						$return = $file->open($this->dbus_cookie_path, true, "rb");
					}

					if ($return)
					{
						$file_content = $file->read();
						$file->close();
						if (is_bool($file_content)) { $return = false; }
					}

					if ($return)
					{
						if (function_exists("hash_hmac") && in_array("sha256", hash_algos())) { $challenge = $this->authHex(hash_hmac("sha256", $file_content, $auth_response[1], true)); }
						else
						{
							$auth_response[1] = str_pad($auth_response[1], 64, "\x00");

							$challenge = mhash(MHASH_SHA256, ($auth_response[1] ^ str_repeat("\x36", 64)).$file_content);
							$challenge = mhash(MHASH_SHA256, ($auth_response[1] ^ str_repeat("\x5c", 64)).$challenge);
							$challenge = $this->authHex($challenge);
						}

						$auth_login_response = $this->authWriteParseResponse("DATA ".$challenge, true);
						if (is_array($auth_login_response) && $auth_login_response[0] == "OK") { $this->dbus_guid = $auth_login_response[1]; }
					}
					else { $return = false; }
				}

				if (!$return)
				{
					$auth_response = $this->authWriteParseResponse("CANCEL ", true);
					$return = true;
				}
			}

			if ($return && (!$this->dbus_guid) && in_array("ANONYMOUS", $auth_response))
			{
				$auth_response = $this->authWriteParseResponse("AUTH ANONYMOUS", true);
				if (is_array($auth_response) && $auth_response[0] == "OK") { $this->dbus_guid = $auth_response[1]; }
			}

			if ($this->dbus_guid)
			{
				$return = $this->authWrite("BEGIN ");
				if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->connect()- (#echo(__LINE__)#) started binary protocol"); }
				$this->dbus_messages = new directMessages($this, $sync_timeout, $this->event_handler);
			}
			else { $return = false; }
		}

		return $return;
	}

/**
	* Disconnects from an active session.
	*
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function disconnect()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->disconnect()- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_resource($this->socket_dbus))
		{
			$return = fclose($this->socket_dbus);
			$this->dbus_callbacks = array();
			$this->dbus_callback_listeners = "\n";
			$this->dbus_guid = "";
			$this->dbus_messages = NULL;
			$this->socket_dbus = NULL;
			$this->socket_path = "";
		}

		return $return;
	}

/**
	* Returns the GUID of the server if authenticated successfully.
	*
	* @return mixed GUID string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getGuid()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getGuid()- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus)) { return $this->dbus_guid; }
		else { return false; }
	}

/**
	* Returns the socket file pointer if authenticated successfully.
	*
	* @return mixed File handle on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function &getHandle()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getHandle()- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { $return =& $this->socket_dbus; }
		else { $return = false; }

		return $return;
	}

/**
	* Receives the result of the endian check.
	*
	* @return boolean True if this system is a native little endian one
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getNle()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getNle()- (#echo(__LINE__)#)"); }
		return $this->nle;
	}

/**
	* Returns an proxy object for the given path.
	*
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface
	* @param  string $destination D-BUS destination address
	* @return mixed File handle on success; false on error
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function getProxy($path, $interface, $destination)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getProxy($path, $interface, $destination)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages) && class_exists(/*#ifdef(PHP5n) */'dNG\DBus\directProxy'/* #*//*#ifndef(PHP5n):"directProxy":#*/))
		{
			if ($this->xml_parser == NULL && class_exists(/*#ifdef(PHP5n) */'dNG\DBus\directXmlParser'/* #*//*#ifndef(PHP5n):"directXmlParser":#*/)) { $this->xml_parser = new directXmlParser("UTF-8", true, $this->timeout_retries, $this->event_handler); }
			$result = $this->sendMethodCallSyncResponse($path, "org.freedesktop.DBus.Introspectable", "Introspect", $destination);

			if ($this->xml_parser != NULL && (!is_bool($result)) && isset($result['body'][0]))
			{
				$result = $result['body'][0];
				$return = new directProxy($this, $path, $interface, $destination, $this->xml_parser->xml2array($result));
			}
		}

		return $return;
	}

/**
	* Reads data from the socket.
	*
	* @param  integer $length Length to read
	* @param  integer $timeout Timeout in seconds
	* @param  boolean $length_forced Data to send.
	* @return string Data read
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function read($length, $timeout, $length_forced = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->read($length, $timeout, +length_forced)- (#echo(__LINE__)#)"); }
		$return = "";

		if (is_resource($this->socket_dbus) && $length)
		{
			$length_read = 0;
			$length_last_read = 0;
			$streams_read = array($this->socket_dbus);
			$streams_ignored = NULL;
			$timeout_time = time() + $timeout;

			do
			{
/*#ifndef(PHP4) */
				stream_select($streams_read, $streams_ignored, $streams_ignored, $timeout);
/* #\n*/
				$data_read = fread($this->socket_dbus, $length);
				$length_last_read = strlen($data_read);

				$return .= $data_read;
				$length_read += $length_last_read;
			}
			while ((!feof($this->socket_dbus)) && $length_read < $length && time() < $timeout_time && ($length_last_read > 0 || $length_forced));
		}

		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->read()- (#echo(__LINE__)#) read ".(strlen($return))." bytes"); }
		return $return;
	}

/**
	* Returns true if the session is available.
	*
	* @return boolean True if session is active.
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function resourceCheck()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->resourceCheck()- (#echo(__LINE__)#)"); }
		return is_resource($this->socket_dbus);
	}

/**
	* Sends a message without waiting for an response.
	*
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $member D-BUS member (method to call)
	* @param  string $destination D-BUS destination address
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return mixed directMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCall($path, $interface, $member, $destination = "", $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendMethodCall($path, $interface, $member, $destination, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { return $this->dbus_messages->sendMethodCall($path, $interface, $member, $destination, $flags, $signature, $parameter); }
		else { return false; }
	}

/**
	* Sends a message and registers the given function or method for the
	* response.
	*
	* @param  mixed $callback Function name string or array with
	*         (&$object, "method") definition
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $member D-BUS member (method to call)
	* @param  string $destination D-BUS destination address
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallAsyncResponse($callback, $path, $interface, $member, $destination = "", $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendMethodCallAsyncResponse(+callback, $path, $interface, $member, $destination, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { return $this->dbus_messages->sendMethodCallAsyncResponse($callback, $path, $interface, $member, $destination, $flags, $signature, $parameter); }
		else { return false; }
	}

/**
	* Sends a message and waits for the response.
	*
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $member D-BUS member (method to call)
	* @param  string $destination D-BUS destination address
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return mixed directMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallSyncResponse($path, $interface, $member, $destination = "", $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendMethodCallSyncResponse($path, $interface, $member, $destination, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { return $this->dbus_messages->sendMethodCallSyncResponse($path, $interface, $member, $destination, $flags, $signature, $parameter); }
		else { return false; }
	}

/**
	* Emits a signal.
	*
	* @param  string $path D-BUS path the signal is emitted from
	* @param  string $interface D-BUS interface
	* @param  string $member D-BUS member (method emitting the signal)
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return mixed directMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendSignal($path, $interface, $member, $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendSignal($path, $interface, $member, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }

		if (is_resource($this->socket_dbus) && is_object($this->dbus_messages)) { return $this->dbus_messages->sendSignal($path, $interface, $member, $flags, $signature, $parameter); }
		else { return false; }
	}

/**
	* Sets connect data needed for DBUS_COOKIE_SHA1.
	*
	* @param  string $path Path to the DBUS_COOKIE_SHA1 directory.
	* @param  string $owner Owner username of this directory
	* @param  boolean True on success (if sha1 is supported)
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function setAuthCookie($path = "", $owner = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->setAuthCookie($path, +owner)- (#echo(__LINE__)#)"); }

		if (function_exists("sha1"))
		{
			$this->dbus_cookie_owner = preg_replace("#\s#", "", $owner);
			$this->dbus_cookie_path = $path;

			return true;
		}
		else { return false; }
	}

/**
	* Sets the EventHandler.
	*
	* @param object $event_handler EventHandler to use
	* @since v0.1.02
*/
	/*#ifndef(PHP4) */public /* #*/function setEventHandler($event_handler)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -dbus->setEventHandler(+event_handler)- (#echo(__LINE__)#)"); }
		$this->event_handler = $event_handler;
	}

/**
	* Sets a flag,  removes it or returns 0x00 if called without parameter.
	*
	* @param  string $flag Flag to set
	* @param  string $status True to switch it on; false to off; NULL for the
	*         opposite
	* @param  string $flags Old flag value
	* @return string New flag
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function setFlag($flag = "", $status = NULL, $flags = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->setFlag($flag, +status, +flags)- (#echo(__LINE__)#)"); }

		if (strlen($flags)) { $return = $flags; }
		else { $return = 0; }

		switch ($flag)
		{
		case "dbus_name_allow_replacement":
		{
			$flag = 1;
			break 1;
		}
		case "no_reply_expected":
		{
			$flag = 1;
			break 1;
		}
		case "dbus_name_replace_existing":
		{
			$flag = 2;
			break 1;
		}
		case "no_auto_start":
		{
			$flag = 2;
			break 1;
		}
		case "dbus_name_do_not_queue":
		{
			$flag = 4;
			break 1;
		}
		default: { $flag = NULL; }
		}

		if ($flag != NULL)
		{
			if (is_bool($status))
			{
				if ($status) { $return |= $flag; }
				else { $return &= ~$flag; }
			}
			else { $return ^= $flag; }
		}

		return $return;
	}

/**
	* Writes data to the socket.
	*
	* @param  string $data Data to send.
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function write($data)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->write(+data)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_resource($this->socket_dbus) && (!empty($data)))
		{
			if (fwrite($this->socket_dbus, $data)) { $return = true; }
		}

		return $return;
	}
}

//j// EOF