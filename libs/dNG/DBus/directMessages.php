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
* This file provides an independent binary message implementation of the D-BUS
* 1.0 specification.
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
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

/**
* The "directMessages" class provides methods for the D-BUS message
* flow.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   DBus.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directMessages
{
/**
	* @var string $dbus_guid D-BUS GUID
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_guid;
/**
	* @var string $dbus_name Given D-BUS name by the bus
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_name;
/**
	* @var array $dbus_broken_data_header Incomplete data header
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_broken_data_header;
/**
	* @var string $dbus_broken_data_read Incomplete data read
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_broken_data_read;
/**
	* @var integer $dbus_broken_length Incomplete data length expected
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_broken_length;
/**
	* @var integer $dbus_requests D-BUS request counter
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_requests;
/**
	* @var directSession $socket_dbus D-BUS session oject
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_session;
/**
	* @var integer $dbus_sync_timeout D-BUS timeout for synchronized calls
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_sync_timeout;
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

/* -------------------------------------------------------------------------
Construct the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directMessages)
	*
	* @param directSession $session D-BUS session object
	* @param integer $sync_timeout Timeout for synchronized requests
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct(&$session, $sync_timeout = 3, $event_handler = NULL)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -dbus->__construct(directMessages)- (#echo(__LINE__)#)"); }

		$this->dbus_guid = $session->getGuid();

		if ($this->dbus_guid)
		{
			$this->dbus_broken_data_header = array();
			$this->dbus_broken_data_read = "";
			$this->dbus_broken_length = 0;
			$this->dbus_requests = 1;
			$this->dbus_session =& $session;
			$this->dbus_sync_timeout = $sync_timeout;
			$this->dbus_name = "";
			$this->dbus_name = $this->getName();
			$this->event_handler = $event_handler;
			$this->nle = $session->getNle();
		}
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directMessages
	*
	* @param directSession $session D-BUS session object
	* @param integer $sync_timeout Timeout for synchronized requests
	* @param object $event_handler EventHandler to use
	* @since v0.1.01
*\/
	function directMessages(&$session, $sync_timeout = 3, $event_handler = NULL) { $this->__construct($session, $sync_timeout, $event_handler); }
:#\n*/
/**
	* Reads an incoming message and delegates it if requested.
	* 
	* @param  string $le Endian mode used for this message
	* @param  directMessage &$message directMessage object
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callback($le, &$message)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->callback($le, +message)- (#echo(__LINE__)#)"); }
		$return = is_object($this->dbus_session);

		if ($return)
		{
			$header_array = $message->getHeader(6);

			if ($this->dbus_name && $header_array)
			{
				if ($header_array[1] != $this->dbus_name) { $return = false; }
			}

			if ($return)
			{
				$header_array = $message->getHeader(8);

				if ($header_array)
				{
					$signature = $header_array[1];
					$body = $message->unmarshal($le, $signature, $message->getRawBody());
					if (is_bool($body)) { $return = false; }
				}
				elseif ($message->getHeader("body_size")) { $return = false; }
				else { $body = array(); }
			}

			if ($return) { $this->dbus_session->callback($message, $body); }
		}

		return $return;
	}

/**
	* Waits until a timeout occured or the defined amount of messages are
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
		$return = is_object($this->dbus_session);

		if ($return)
		{
			$is_valid = true;

/*#ifndef(PHP4) */
			if ($timeout == 0) { $timeout = $this->dbus_sync_timeout; }
			elseif ($timeout < 10000) { $timeout = 0.01; }
			else { $timeout /= 1000000; }

			$timeout_seconds = ceil($timeout);
			$timeout_time = microtime(true) + $timeout;
/* #\n*//*#ifdef(PHP4):
			if ($timeout == 0) { $timeout = $this->dbus_sync_timeout; }
			elseif ($timeout < 1000000) { $timeout = 1; }
			else { $timeout = ceil($timeout / 1000000); }

			$timeout_time = time() + $timeout;
			$timeout_seconds = $timeout;
:#\n*/
		}

		$this->sendMethodCall("/org/freedesktop/DBus", "org.freedesktop.DBus", "AddMatch", "org.freedesktop.DBus", NULL, "s", array("type='signal'"));

		while (is_bool($return) && $return && $is_valid && /*#ifndef(PHP4) */microtime(true)/* #*//*#ifdef(PHP4):time():#*/ < $timeout_time)
		{
			$is_valid = false;
			$message =& $this->read($timeout_seconds - ($timeout_seconds - (/*#ifndef(PHP4) */ceil($timeout_time - microtime(true))/* #*//*#ifdef(PHP4):$timeout_time - time():#*/)));

			if (is_object($message))
			{
				$le = $message->getHeader("endian");
				if (is_string($le)) { $is_valid = true; }
			}

			if ($is_valid) { $return = $this->callback($le, $message); }

			if ($messages > -1)
			{
				$messages--;
				if ($messages == 0) { $is_valid = false; }
			}
		}

		$this->sendMethodCall("/org/freedesktop/DBus", "org.freedesktop.DBus", "RemoveMatch", "org.freedesktop.DBus", NULL, "s", array("type='signal'"));
		return $return;
	}

/**
	* Return the D-BUS name given by the bus.
	*
	* @return mixed Name string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getName()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getName()- (#echo(__LINE__)#)"); }
		$return = false;

		if ($this->dbus_name) { $return = $this->dbus_name; }
		else
		{
			$response = $this->sendMethodCallSyncResponse("/org/freedesktop/DBus", "org.freedesktop.DBus", "Hello", "org.freedesktop.DBus");

			if (is_array($response) && isset($response['body'][0]))
			{
				$this->dbus_name = $response['body'][0];
				$return = $response['body'][0];
			}
		}

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
	* Reads a message and returns it. If a timeout occurs it save the current
	* state for later continuation.
	*
	* @param  integer $timeout Timeout limit in seconds
	* @return mixed directMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function &read($timeout)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->read($timeout)- (#echo(__LINE__)#)"); }

		$is_valid = true;
		$response = new directMessage($this, $this->event_handler);
		$return = is_object($this->dbus_session);
		$timeout_time = time() + $timeout;

		while (is_bool($return) && $return && ($is_valid || $this->dbus_broken_length) && time() < $timeout_time)
		{
			$is_valid = false;

			if ($this->dbus_broken_length)
			{
				$length_unread = $this->dbus_broken_length - strlen($this->dbus_broken_data_read);

				if ($length_unread) { $data_read = $this->dbus_session->read($length_unread, $timeout); }
				else { $data_read = ""; }

				if (is_bool($data_read)) { $return = false; }
				else
				{
					$this->dbus_broken_data_read .= $data_read;

					if ($length_unread == strlen($data_read))
					{
						if (empty($this->dbus_broken_header))
						{
							$is_valid = true;
							$data_read = $this->dbus_broken_data_read;
						}
						else
						{
							if ($response->set($response->unmarshal(0, "yyyyuua(yv)", $this->dbus_broken_data_read), $this->dbus_broken_data_read)) { $return =& $response; }
							else { $return = false; }

							$this->dbus_broken_data_read = "";
							$this->dbus_broken_header = array();
							$this->dbus_broken_length = 0;
						}
					}
				}
			}
			else
			{
				$data_read = $this->dbus_session->read(16, $timeout);

				if (is_bool($data_read)) { $return = false; }
				else
				{
					if (strlen($data_read) == 16) { $is_valid = true; }
					else
					{
						$this->dbus_broken_data_read = $data_read;
						$this->dbus_broken_length = 16;
					}
				}
			}

			if ($is_valid)
			{
				$is_valid = false;

				$this->dbus_broken_data_read = $data_read;
				$this->dbus_broken_length = 16;

				$this->dbus_broken_header = $response->unmarshal(0, "yyyyuuu", $data_read);

				if ($this->dbus_broken_header)
				{
					if ($this->dbus_broken_header[6])
					{
						$this->dbus_broken_length += $this->dbus_broken_header[6];

						$length_boundary = $this->dbus_broken_length % 8;
						if ($length_boundary && $length_boundary < 8) { $this->dbus_broken_length += 8 - $length_boundary; }
					}

					if ($this->dbus_broken_header[4]) { $this->dbus_broken_length += $this->dbus_broken_header[4]; }
				}
				else { $return = false; }
			}
		}

		return $return;
	}

/**
	* Builds a D-BUS message based on the given data.
	* 
	* @param  integer $type Type as defined in the D-BUS Specification 1.0
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a empty
	*         string))
	* @param  string $member D-BUS member (method to call)
	* @param  string $destination D-BUS destination address
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return mixed directMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function sendBuildMessage($type, $path, $interface, $member, $destination = "", $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendBuildMessage($type, $path, $interface, $member, $destination, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }
		$return = is_object($this->dbus_session);

		if ($return && is_string($path) && strlen($path) && is_string($interface) && is_string($member) && strlen($member) && is_string($destination))
		{
			$body_exists = false;
			$body_raw_length = 0;
			$is_valid = true;
			$return = new directMessage($this);

			if (is_string($signature) && strlen($signature))
			{
				if ($parameter == NULL) { $is_valid = false; }
				$body_exists = true;
			}

			if ($is_valid)
			{
$header_array = array(
array(1, array("o", $path)),
array(3, array("s", $member))
);

				if (strlen($interface)) { $header_array[] = array(2, array("s", $interface)); }
				if (strlen($destination)) { $header_array[] = array(6, array("s", $destination)); }
				if ($this->dbus_name) { $header_array[] = array(7, array("s", $this->dbus_name)); }
			}

			if ($is_valid && $body_exists)
			{
				$body_raw = $return->marshalArray($signature, $parameter);

				if (is_bool($body_raw)) { $is_valid = false; }
				else
				{
					$header_array[] = array(8, array("g", $signature));
					$body_raw_length = strlen($body_raw);
				}
			}

			if ($is_valid)
			{
				if ($flags == NULL) { $flags = 0; }
				$header_array = array("l", $type, $flags, 1, $body_raw_length, $this->dbus_requests, $header_array);

				$header_raw = $return->marshalArray("yyyyuua(yv)", $header_array);

				if (is_bool($header_raw)) { $is_valid = false; }
				else
				{
					$return->marshalSetBoundary($header_raw, strlen($header_raw), 8);
					if ($body_exists) { $header_raw .= $body_raw; }
				}
			}

			if ($is_valid) { $return->set($header_array, $header_raw); }
			else { $return = false; }
		}

		return $return;
	}

/**
	* Sends a message and requests no response.
	*
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a empty
	*         string))
	* @param  string $member D-BUS member (method to call)
	* @param  string $destination D-BUS destination address
	* @param  integer $flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directSession.
	* @param  string $signature D-BUS body signature
	* @param  string $parameter D-BUS body content parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCall($path, $interface, $member, $destination = "", $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendMethodCall($path, $interface, $member, $destination, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }

		if ($flags == NULL) { $flags = 1; }
		else { $flags |= 1; }

		$return = $this->sendBuildMessage(1, $path, $interface, $member, $destination, $flags, $signature, $parameter);

		if (is_object($return))
		{
			$return = $return->getRaw();
			$return = ((is_string($return) && strlen($return)) ? $this->dbus_session->write($return) : false);
		}

		return $return;
	}

/**
	* Sends a message and registers the given function or method for the
	* response.
	*
	* @param  mixed $callback Function name string or array with
	*         (&$object, "method") definition
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a empty
	*         string))
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

		$return = $this->dbus_session->callbackCheck($callback);
		if ($return) { $return = $this->sendBuildMessage(1, $path, $interface, $member, $destination, $flags, $signature, $parameter); }

		if (is_object($return))
		{
			$return = $return->getRaw();

			if (is_string($return) && strlen($return))
			{
				$return = $this->dbus_session->write($return);

				if ($return)
				{
					$this->dbus_session->callbackRegisterSerial($this->dbus_requests, $callback);

					if ($this->dbus_requests < PHP_INT_MAX) { $this->dbus_requests++; }
					else { $this->dbus_requests = 1; }
				}
			}
			else { $return = false; }
		}

		return $return;
	}

/**
	* Sends a message and waits for the response.
	*
	* @param  string $path D-BUS path
	* @param  string $interface D-BUS interface (may stay empty (provide a empty
	*         string))
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
		$return = false;

		$message = $this->sendBuildMessage(1, $path, $interface, $member, $destination, $flags, $signature, $parameter);
		if (is_object($message)) { $return = $this->sendSyncResponse($message); }

		return $return;
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
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendSignal($path, $interface, $member, $flags = NULL, $signature = "", $parameter = NULL)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendSignal($path, $interface, $member, +flags, $signature, +parameter)- (#echo(__LINE__)#)"); }
		$return = (strlen($interface) ? true : false);

		if ($flags == NULL) { $flags = 1; }
		else { $flags |= 1; }

		if ($return) { $return = $this->sendBuildMessage(4, $path, $interface, $member, "", $flags, $signature, $parameter); }

		if (is_object($return))
		{
			$return = $return->getRaw();
			$return = ((is_string($return) && strlen($return)) ? $this->dbus_session->write($return) : false);
		}

		return $return;
	}

/**
	* Sends a message and waits for the response.
	* 
	* @param  directMessage &$message directMessage object
	* @return mixed Array (message -> directMessage object, body -> body array)
	*         on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function sendSyncResponse(&$message)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->sendSyncResponse(+message)- (#echo(__LINE__)#)"); }

		$is_valid = true;
		$return = is_object ($this->dbus_session);

		if (is_object($message)) { $message_raw = $message->getRaw(); }
		else { $return = false; }

		if ($return && is_string($message_raw) && strlen($message_raw))
		{
			$return = $this->dbus_session->write($message_raw);
			unset($message_raw);

			$timeout_time =time() + $this->dbus_sync_timeout;
			$serial = $this->dbus_requests;

			if ($this->dbus_requests < PHP_INT_MAX) { $this->dbus_requests++; }
			else { $this->dbus_requests = 0; }
		}
		else { $return = false; }

		while (is_bool($return) && $return && $is_valid && time () < $timeout_time)
		{
			$is_valid = false;
			$response =& $this->read($this->dbus_sync_timeout);

			if (is_object($response))
			{
				$le = $response->getHeader("endian");
				if (is_string($le)) { $is_valid = true; }
			}

			if ($is_valid)
			{
				$header_array = $response->getHeader(5);
				if ($header_array && $header_array[1] == $serial) { $is_valid = false; }

				if ($is_valid) { $this->callback($le, $response); }
				else
				{
					$type = $response->getHeader("type");
					if (is_bool($type)) { $return = false; }

					if ($return)
					{
						$header_array = $response->getHeader(6);

						if ($this->dbus_name && $header_array)
						{
							if ($header_array[1] == $this->dbus_name) { $is_valid = true; }
						}
						else { $is_valid = true; }
					}
					else { $is_valid = true; }

/* -------------------------------------------------------------------------
Ignore message and continue to parse incoming messages if the destination is
set and wrong.
------------------------------------------------------------------------- */

					if ($is_valid)
					{
						if ($return && ($type == "method_return" || $type == "error"))
						{
							$header_array = $response->getHeader(8);

							if ($header_array)
							{
								$signature = $header_array[1];
								$body = $response->unmarshal($le, $signature, $response->getRawBody());
								if (is_bool($body)) { $return = false; }
							}
							elseif ($response->getHeader("body_size")) { $return = false; }
							else { $body = array(); }
						}
						else { $return = false; }

						if ($return) { $return = array("message" => $response, "body" => $body); }
					}
					else { $is_valid = true; }
				}
			}
		}

		return $return;
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
}

//j// EOF