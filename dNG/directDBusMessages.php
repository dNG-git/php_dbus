<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
D-BUS PHP Binding
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?ext_dbus

This Source Code Form is subject to the terms of the Mozilla Public License,
v. 2.0. If a copy of the MPL was not distributed with this file, You can
obtain one at http://mozilla.org/MPL/2.0/.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;mpl2
----------------------------------------------------------------------------
#echo(extDBusVersion)#
ext_dbus/#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* This file provides an sWG independent binary message implementation of the
* D-BUS 1.0 specification.
* 
* @internal  We are using phpDocumentor to automate the documentation process
*            for creating the Developer's Manual. All sections including
*            these special comments will be removed from the release source
*            code.
*            Use the following line to ensure 76 character sizes:
* ----------------------------------------------------------------------------
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   ext_dbus
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
/*#ifdef(PHP5n) */

namespace dNG;
/* #\n*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Functions and classes

if (!defined ("CLASS_directDBusMessages"))
{
/**
* The "directDBusMessages" class provides methods for the D-BUS message
* flow.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   ext_dbus
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directDBusMessages
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
	* @var directDBusSession $socket_dbus D-BUS session oject
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_session;
/**
	* @var integer $dbus_sync_timeout D-BUS timeout for synchronized calls
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_sync_timeout;
/**
	* @var array $debug Debug message container 
*/
	/*#ifndef(PHP4) */public /* #*//*#ifdef(PHP4):var :#*/$debug;
/**
	* @var boolean $debugging True if we should fill the debug message
	*      container 
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $debugging;
/**
	* @var boolean $nle True if we are on a native little endian
	*      system
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $nle;

/* -------------------------------------------------------------------------
Construct the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directDBusMessages)
	*
	* @param directDBusSession $f_session D-BUS session object
	* @param integer $f_sync_timeout Timeout for synchronized requests
	* @param boolean $f_debug Debug flag
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct (&$f_session,$f_sync_timeout = 3,$f_debug = false)
	{
		$this->debugging = $f_debug;
		if ($this->debugging) { $this->debug = array ("directDBusMessages/#echo(__FILEPATH__)# -dbus->__construct (directDBusMessages)- (#echo(__LINE__)#)"); }

		$this->dbus_guid = $f_session->getGuid ();

		if ($this->dbus_guid)
		{
			$this->dbus_broken_data_header = array ();
			$this->dbus_broken_data_read = "";
			$this->dbus_broken_length = 0;
			$this->dbus_requests = 1;
			$this->dbus_session =& $f_session;
			$this->dbus_sync_timeout = $f_sync_timeout;
			$this->dbus_name = "";
			$this->dbus_name = $this->getName ();
			$this->nle = $f_session->getNle ();
		}
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directDBusMessages
	*
	* @param directDBusSession $f_session D-BUS session object
	* @param integer $f_sync_timeout Timeout for synchronized requests
	* @param boolean $f_debug Debug flag
	* @since v0.1.01
*\/
	function directDBusMessages (&$f_session,$f_sync_timeout = 3,$f_debug = false) { $this->__construct ($f_session,$f_sync_timeout,$f_debug); }
:#\n*/
/**
	* Reads an incoming message and delegates it if requested.
	* 
	* @param  string $f_le Endian mode used for this message
	* @param  directDBusMessage &$f_message directDBusMessage object
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callback ($f_le,&$f_message)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->callback ($f_le,+f_message)- (#echo(__LINE__)#)"; }
		$f_return = is_object ($this->dbus_session);

		if ($f_return)
		{
			$f_header_array = $f_message->getHeader (6);

			if (($this->dbus_name)&&($f_header_array))
			{
				if ($f_header_array[1] != $this->dbus_name) { $f_return = false; }
			}

			if ($f_return)
			{
				$f_header_array = $f_message->getHeader (8);

				if ($f_header_array)
				{
					$f_signature = $f_header_array[1];
					$f_body = $f_message->unmarshal ($f_le,$f_signature,($f_message->getRawBody ()));
					if (is_bool ($f_body)) { $f_return = false; }
				}
				elseif ($f_message->getHeader ("body_size")) { $f_return = false; }
				else { $f_body = array (); }
			}

			if ($f_return) { $this->dbus_session->callback ($f_message,$f_body); }
		}

		return $f_return;
	}

/**
	* Waits until a timeout occured or the defined amount of messages are
	* parsed.
	*
	* @param  integer $f_timeout Timeout for incoming messages (in microseconds)
	* @param  integer $f_messages Number of messages to parse
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackListen ($f_timeout = 0,$f_messages = 0)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->callbackListen ($f_timeout,$f_messages)- (#echo(__LINE__)#)"; }

		$f_return = is_object ($this->dbus_session);

		if ($f_return)
		{
/*#ifndef(PHP4) */
			if ($f_timeout == 0) { $f_timeout = $this->dbus_sync_timeout; }
			elseif ($f_timeout < 10000) { $f_timeout = 0.01; }
			else { $f_timeout /= 1000000; }

			$f_continue_check = true;
			$f_timeout_seconds = ceil ($f_timeout);
			$f_timeout_time = microtime (true) + $f_timeout;
/* #\n*//*#ifdef(PHP4):
			if ($f_timeout == 0) { $f_timeout = $this->dbus_sync_timeout; }
			elseif ($f_timeout < 1000000) { $f_timeout = 1; }
			else { $f_timeout = ceil ($f_timeout / 1000000); }

			$f_continue_check = true;
			$f_timeout_time = time () + $f_timeout;
			$f_timeout_seconds = $f_timeout;
:#\n*/
		}

		$this->sendMethodCall ("/org/freedesktop/DBus","org.freedesktop.DBus","AddMatch","org.freedesktop.DBus",NULL,"s",(array ("type='signal'")));

		while ((is_bool ($f_return))&&($f_return)&&($f_continue_check)&&($f_timeout_time > /*#ifndef(PHP4) */(microtime (true))/* #*//*#ifdef(PHP4):(time ()):#*/))
		{
			$f_continue_check = false;
			$f_message =& $this->read ($f_timeout_seconds - ($f_timeout_seconds - (/*#ifndef(PHP4) */ceil ($f_timeout_time - (microtime (true)))/* #*//*#ifdef(PHP4):$f_timeout_time - (time ()):#*/)));

			if (is_object ($f_message))
			{
				$f_le = $f_message->getHeader ("endian");
				if (is_string ($f_le)) { $f_continue_check = true; }
			}

			if ($f_continue_check) { $f_return = $this->callback ($f_le,$f_message); }

			if ($f_messages > -1)
			{
				$f_messages--;
				if ($f_messages == 0) { $f_continue_check = false; }
			}
		}

		$this->sendMethodCall ("/org/freedesktop/DBus","org.freedesktop.DBus","RemoveMatch","org.freedesktop.DBus",NULL,"s",(array ("type='signal'")));

		return $f_return;
	}

/**
	* Return the D-BUS name given by the bus.
	*
	* @return mixed Name string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getName ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->getName ()- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ($this->dbus_name) { $f_return = $this->dbus_name; }
		else
		{
			$f_response = $this->sendMethodCallSyncResponse ("/org/freedesktop/DBus","org.freedesktop.DBus","Hello","org.freedesktop.DBus");

			if ((is_array ($f_response))&&(isset ($f_response['body'][0])))
			{
				$this->dbus_name = $f_response['body'][0];
				$f_return = $f_response['body'][0];
			}
		}

		return $f_return;
	}

/**
	* Receives the result of the endian check.
	*
	* @return boolean True if this system is a native little endian one
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getNle ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->getNle ()- (#echo(__LINE__)#)"; }
		return $this->nle;
	}

/**
	* Reads a message and returns it. If a timeout occurs it save the current
	* state for later continuation.
	*
	* @param  integer $f_timeout Timeout limit in seconds
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function &read ($f_timeout)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->read ($f_timeout)- (#echo(__LINE__)#)"; }

		$f_continue_check = true;
		$f_response = new directDBusMessage ($this,$this->debugging);
		$f_return = is_object ($this->dbus_session);
		$f_timeout_time = time () + $f_timeout;

		while ((is_bool ($f_return))&&($f_return)&&(($f_continue_check)||($this->dbus_broken_length))&&($f_timeout_time > (time ())))
		{
			$f_continue_check = false;

			if ($this->dbus_broken_length)
			{
				$f_length_unread = ($this->dbus_broken_length - (strlen ($this->dbus_broken_data_read)));

				if ($f_length_unread) { $f_data_read = $this->dbus_session->read ($f_length_unread,$f_timeout); }
				else { $f_data_read = ""; }

				if (is_bool ($f_data_read)) { $f_return = false; }
				else
				{
					$this->dbus_broken_data_read .= $f_data_read;

					if ($f_length_unread == strlen ($f_data_read))
					{
						if (empty ($this->dbus_broken_header))
						{
							$f_continue_check = true;
							$f_data_read = $this->dbus_broken_data_read;
						}
						else
						{
							if ($f_response->set ($f_response->unmarshal (0,"yyyyuua(yv)",$this->dbus_broken_data_read),$this->dbus_broken_data_read)) { $f_return =& $f_response; }
							else { $f_return = false; }

							$this->dbus_broken_data_read = "";
							$this->dbus_broken_header = array ();
							$this->dbus_broken_length = 0;
						}
					}
				}
			}
			else
			{
				$f_data_read = $this->dbus_session->read (16,$f_timeout);

				if (is_bool ($f_data_read)) { $f_return = false; }
				else
				{
					if (strlen ($f_data_read) == 16) { $f_continue_check = true; }
					else
					{
						$this->dbus_broken_data_read = $f_data_read;
						$this->dbus_broken_length = 16;
					}
				}
			}

			if ($f_continue_check)
			{
				$f_continue_check = false;

				$this->dbus_broken_data_read = $f_data_read;
				$this->dbus_broken_length = 16;

				$this->dbus_broken_header = $f_response->unmarshal (0,"yyyyuuu",$f_data_read);

				if ($this->dbus_broken_header)
				{
					if ($this->dbus_broken_header[6])
					{
						$this->dbus_broken_length += $this->dbus_broken_header[6];

						$f_length_boundary = $this->dbus_broken_length % 8;
						if (($f_length_boundary)&&($f_length_boundary < 8)) { $this->dbus_broken_length += (8 - $f_length_boundary); }
					}

					if ($this->dbus_broken_header[4]) { $this->dbus_broken_length += $this->dbus_broken_header[4]; }
				}
				else { $f_return = false; }
			}
		}

		return $f_return;
	}

/**
	* Builds a D-BUS message based on the given data.
	* 
	* @param  integer $f_type Type as defined in the D-BUS Specification 1.0
	* @param  string $f_path D-BUS path
	* @param  string $f_interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $f_member D-BUS member (method to call)
	* @param  string $f_destination D-BUS destination address
	* @param  integer $f_flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directDBusSession.
	* @param  string $f_signature D-BUS body signature
	* @param  string $f_parameter D-BUS body content parameters
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function sendBuildMessage ($f_type,$f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendBuildMessage ($f_type,$f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }
		$f_return = is_object ($this->dbus_session);

		if (($f_return)&&(is_string ($f_path))&&(strlen ($f_path))&&(is_string ($f_interface))&&(is_string ($f_member))&&(strlen ($f_member))&&(is_string ($f_destination)))
		{
			$f_body_exists = false;
			$f_body_raw_length = 0;
			$f_continue_check = true;
			$f_return = new directDBusMessage ($this);

			if ((is_string ($f_signature))&&(strlen ($f_signature)))
			{
				if ($f_parameter == NULL) { $f_continue_check = false; }
				$f_body_exists = true;
			}

			if ($f_continue_check)
			{
$f_header_array = array (
array (1,(array ("o",$f_path))),
array (3,(array ("s",$f_member))),
);

				if (strlen ($f_interface)) { $f_header_array[] = array (2,(array ("s",$f_interface))); }
				if (strlen ($f_destination)) { $f_header_array[] = array (6,(array ("s",$f_destination))); }
				if ($this->dbus_name) { $f_header_array[] = array (7,(array ("s",$this->dbus_name))); }
			}

			if (($f_continue_check)&&($f_body_exists))
			{
				$f_body_raw = $f_return->marshalArray ($f_signature,$f_parameter);

				if (is_bool ($f_body_raw)) { $f_continue_check = false; }
				else
				{
					$f_header_array[] = array (8,(array ("g",$f_signature)));
					$f_body_raw_length = strlen ($f_body_raw);
				}
			}

			if ($f_continue_check)
			{
				if ($f_flags == NULL) { $f_flags = 0; }
				$f_header_array = array ("l",$f_type,$f_flags,1,$f_body_raw_length,$this->dbus_requests,$f_header_array);

				$f_header_raw = $f_return->marshalArray ("yyyyuua(yv)",$f_header_array);

				if (is_bool ($f_header_raw)) { $f_continue_check = false; }
				else
				{
					$f_return->marshalSetBoundary ($f_header_raw,(strlen ($f_header_raw)),8);
					if ($f_body_exists) { $f_header_raw .= $f_body_raw; }
				}
			}

			if ($f_continue_check) { $f_return->set ($f_header_array,$f_header_raw); }
			else { $f_return = false; }
		}

		return $f_return;
	}

/**
	* Sends a message and requests no response.
	*
	* @param  string $f_path D-BUS path
	* @param  string $f_interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $f_member D-BUS member (method to call)
	* @param  string $f_destination D-BUS destination address
	* @param  integer $f_flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directDBusSession.
	* @param  string $f_signature D-BUS body signature
	* @param  string $f_parameter D-BUS body content parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCall ($f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendMethodCall ($f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		if ($f_flags == NULL) { $f_flags = 1; }
		else { $f_flags |= 1; }

		$f_return = $this->sendBuildMessage (1,$f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter);

		if (is_object ($f_return))
		{
			$f_return = $f_return->getRaw ();
			$f_return = (((is_string ($f_return))&&(strlen ($f_return))) ? $this->dbus_session->write ($f_return) : false);
		}

		return $f_return;
	}

/**
	* Sends a message and registers the given function or method for the
	* response.
	*
	* @param  mixed $f_callback Function name string or array with
	*         (&$object,"method") definition
	* @param  string $f_path D-BUS path
	* @param  string $f_interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $f_member D-BUS member (method to call)
	* @param  string $f_destination D-BUS destination address
	* @param  integer $f_flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directDBusSession.
	* @param  string $f_signature D-BUS body signature
	* @param  string $f_parameter D-BUS body content parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallAsyncResponse ($f_callback,$f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendMethodCallAsyncResponse (+f_callback,$f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		$f_return = $this->dbus_session->callbackCheck ($f_callback);
		if ($f_return) { $f_return = $this->sendBuildMessage (1,$f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter); }

		if (is_object ($f_return))
		{
			$f_return = $f_return->getRaw ();

			if ((is_string ($f_return))&&(strlen ($f_return)))
			{
				$f_return = $this->dbus_session->write ($f_return);

				if ($f_return)
				{
					$this->dbus_session->callbackRegisterSerial ($this->dbus_requests,$f_callback);

					if ($this->dbus_requests < PHP_INT_MAX) { $this->dbus_requests++; }
					else { $this->dbus_requests = 1; }
				}
			}
			else { $f_return = false; }
		}

		return $f_return;
	}

/**
	* Sends a message and waits for the response.
	*
	* @param  string $f_path D-BUS path
	* @param  string $f_interface D-BUS interface (may stay empty (provide a
	*         empty string))
	* @param  string $f_member D-BUS member (method to call)
	* @param  string $f_destination D-BUS destination address
	* @param  integer $f_flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directDBusSession.
	* @param  string $f_signature D-BUS body signature
	* @param  string $f_parameter D-BUS body content parameters
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallSyncResponse ($f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendMethodCallSyncResponse ($f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }
		$f_return = false;

		$f_message = $this->sendBuildMessage (1,$f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter);
		if (is_object ($f_message)) { $f_return = $this->sendSyncResponse ($f_message); }

		return $f_return;
	}

/**
	* Emits a signal.
	*
	* @param  string $f_path D-BUS path the signal is emitted from
	* @param  string $f_interface D-BUS interface
	* @param  string $f_member D-BUS member (method emitting the signal)
	* @param  integer $f_flags Binary value as defined in the D-BUS
	*         Specification 1.0 or generated using directDBusSession.
	* @param  string $f_signature D-BUS body signature
	* @param  string $f_parameter D-BUS body content parameters
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendSignal ($f_path,$f_interface,$f_member,$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendSignal ($f_path,$f_interface,$f_member,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		$f_return = ((strlen ($f_interface)) ? true : false);

		if ($f_flags == NULL) { $f_flags = 1; }
		else { $f_flags |= 1; }

		if ($f_return) { $f_return = $this->sendBuildMessage (4,$f_path,$f_interface,$f_member,"",$f_flags,$f_signature,$f_parameter); }

		if (is_object ($f_return))
		{
			$f_return = $f_return->getRaw ();
			$f_return = (((is_string ($f_return))&&(strlen ($f_return))) ? $this->dbus_session->write ($f_return) : false);
		}

		return $f_return;
	}

/**
	* Sends a message and waits for the response.
	* 
	* @param  directDBusMessage &$f_message directDBusMessage object
	* @return mixed Array (message -> directDBusMessage object,body -> body
	*         array) on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function sendSyncResponse (&$f_message)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessages/#echo(__FILEPATH__)# -dbus->sendSyncResponse (+f_message)- (#echo(__LINE__)#)"; }

		$f_continue_check = true;
		$f_return = is_object ($this->dbus_session);

		if (is_object ($f_message)) { $f_message_raw = $f_message->getRaw (); }
		else { $f_return = false; }

		if (($f_return)&&(is_string ($f_message_raw))&&(strlen ($f_message_raw)))
		{
			$f_return = $this->dbus_session->write ($f_message_raw);
			unset ($f_message_raw);

			$f_timeout_time = (time () + $this->dbus_sync_timeout);
			$f_serial = $this->dbus_requests;

			if ($this->dbus_requests < PHP_INT_MAX) { $this->dbus_requests++; }
			else { $this->dbus_requests = 0; }
		}
		else { $f_return = false; }

		while ((is_bool ($f_return))&&($f_return)&&($f_continue_check)&&($f_timeout_time > (time ())))
		{
			$f_continue_check = false;
			$f_response =& $this->read ($this->dbus_sync_timeout);

			if (is_object ($f_response))
			{
				$f_le = $f_response->getHeader ("endian");
				if (is_string ($f_le)) { $f_continue_check = true; }
			}

			if ($f_continue_check)
			{
				$f_header_array = $f_response->getHeader (5);
				if (($f_header_array)&&($f_header_array[1] == $f_serial)) { $f_continue_check = false; }

				if ($f_continue_check) { $this->callback ($f_le,$f_response); }
				else
				{
					$f_type = $f_response->getHeader ("type");
					if (is_bool ($f_type)) { $f_return = false; }

					if ($f_return)
					{
						$f_header_array = $f_response->getHeader (6);

						if (($this->dbus_name)&&($f_header_array))
						{
							if ($f_header_array[1] == $this->dbus_name) { $f_continue_check = true; }
						}
						else { $f_continue_check = true; }
					}
					else { $f_continue_check = true; }

/* -------------------------------------------------------------------------
Ignore message and continue to parse incoming messages if the destination is
set and wrong.
------------------------------------------------------------------------- */

					if ($f_continue_check)
					{
						if (($f_return)&&(($f_type == "method_return")||($f_type == "error")))
						{
							$f_header_array = $f_response->getHeader (8);

							if ($f_header_array)
							{
								$f_signature = $f_header_array[1];
								$f_body = $f_response->unmarshal ($f_le,$f_signature,($f_response->getRawBody ()));
								if (is_bool ($f_body)) { $f_return = false; }
							}
							elseif ($f_response->getHeader ("body_size")) { $f_return = false; }
							else { $f_body = array (); }
						}
						else { $f_return = false; }

						if ($f_return) { $f_return = array ("message" => $f_response,"body" => $f_body); }
					}
					else { $f_continue_check = true; }
				}
			}
		}

		return $f_return;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directDBusMessages",true);
}

//j// EOF