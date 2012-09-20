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
* This file provides an sWG independent implementation of the D-BUS 1.0
* specification.
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

if (!defined ("CLASS_directDBusSession"))
{
/**
* This is an abstraction layer for D-BUS communication.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   ext_dbus
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directDBusSession
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
	* @var directDBusMessages $dbus_raw D-BUS message handler
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_messages;
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
	* @var string $ext_dbus_path Path to the D-BUS PHP binding files.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $ext_dbus_path;
/**
	* @var boolean $nle True if we are on a native little endian
	*      system
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $nle;
/**
	* @var boolean $socket_available True if fsockopen is available
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $socket_available;
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
	* @var integer $xml_parser Allocated XML parser instance
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $xml_parser;

/* -------------------------------------------------------------------------
Construct the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directDBusSession)
	*
	* @param string $f_path Path to the socket to connect to
	* @param string $f_ext_dbus_path Path to the D-BUS PHP binding files.
	* @param boolean $f_debug Debug flag
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct ($f_path,$f_ext_dbus_path = "",$f_debug = false)
	{
		$this->debugging = $f_debug;
		if ($this->debugging) { $this->debug = array ("directDBusSession/#echo(__FILEPATH__)# -dbus->__construct (directDBusSession)- (#echo(__LINE__)#)"); }

		$this->dbus_callbacks = array ();
		$this->dbus_callback_listeners = "\n";
		$this->dbus_cookie_path = NULL;
		$this->dbus_cookie_owner = NULL;
		$this->dbus_guid = "";
		$this->dbus_messages = NULL;

		if (strlen ($f_ext_dbus_path)) { $f_ext_dbus_path .= "/"; }
		$this->ext_dbus_path = $f_ext_dbus_path;

		if (pack ("S",1) == "\x01\x00") { $this->nle = true; }
		else { $this->nle = false; }

		$this->socket_available = function_exists ("fsockopen");
		$this->socket_dbus = NULL;

		if (!defined ("CLASS_directDBusMessages")) { @include_once ($f_ext_dbus_path."directDBusMessages.php"); }
		if (!defined ("CLASS_directDBusMessage")) { @include_once ($f_ext_dbus_path."directDBusMessage.php"); }

		if ((defined ("CLASS_directDBusMessages"))&&(defined ("CLASS_directDBusMessage")))
		{
			if (stripos ($f_path,"unix:abstract://") === 0) { $this->socket_path = preg_replace ("#unix:abstract:\/\/#i","unix://\x00",$f_path); }
			else { $this->socket_path = $f_path; }
		}

		$this->socket_timeout = 15;
		$this->xml_parser = NULL;
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directDBusSession
	*
	* @param string $f_path Path to the socket to connect to
	* @param string $f_ext_dbus_path Path to the D-BUS PHP binding files.
	* @param boolean $f_debug Debug flag
	* @uses  directDBusSession::__construct()
	* @since v0.1.01
*\/
	function directDBusSession ($f_path,$f_ext_dbus_path = "",$f_debug = false) { $this->__construct ($f_path,$f_ext_dbus_path,$f_debug); }
:#\n*/
/**
	* Destructor (PHP5) __destruct (directDBusSession)
	*
	* @uses  directDBusSession::disconnect()
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __destruct () { $this->disconnect (); }

/**
	* Converts data to an hex string in a binary safe way.
	*
	* @param  string $f_data Data to encode/decode to hexadecimal 
	* @param  boolean $f_decode False to encode
	* @return mixed Read line on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authHex ($f_data,$f_decode = false)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authHex ($f_data,+f_decode)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ($f_decode) { $f_return = @pack("H*",$f_data); }
		else
		{
			$f_return = @unpack("H*hexdata",$f_data);
			if (isset ($f_return['hexdata'])) { $f_return = $f_return['hexdata']; }
		}

		return $f_return;
	}

/**
	* Reads data during an authentication process.
	*
	* @return mixed Read line on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authRead ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authRead ()- (#echo(__LINE__)#)"; }
		$f_return = false;

		if (is_resource ($this->socket_dbus))
		{
			$f_data_read = "";
			$f_stream_check = array ($this->socket_dbus);
			$f_stream_ignored = NULL;
			$f_timeout_time = time () + $this->socket_timeout;

			while ((!feof ($this->socket_dbus))&&(strpos ($f_data_read,"\r\n") < 1)&&($f_timeout_time > (time ())))
			{
/*#ifndef(PHP4) */
				stream_select ($f_stream_check,$f_stream_ignored,$f_stream_ignored,$this->socket_timeout);
/* #\n*/
				$f_data_read .= fread ($this->socket_dbus,4096);
			}

			if (strpos ($f_data_read,"\r\n") > 0)
			{
				if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authRead ()- (#echo(__LINE__)#) read ".$f_data_read; }
				$f_return = trim ($f_data_read);
			}
		}

		return $f_return;
	}

/**
	* Reads and parses the response in the authentication process.
	* 
	* @param  boolean $f_return_response True to return the response
	* @uses   directDBusSession::authRead()
	* @return mixed Either an array ([0] command / result [1] data) or true on
	*         success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authReadParseResponse ($f_return_response = false)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authReadParseResponse (+f_return_response)- (#echo(__LINE__)#)"; }

		$f_return = false;
		$f_data_read = $this->authRead ();

		if ($f_data_read)
		{
			$f_data = explode (" ",$f_data_read,2);

			if (count ($f_data) == 2)
			{
				if ($f_return_response) { $f_return = $f_data; }
				elseif (($f_data[0] == "DATA")||($f_data[0] == "OK")) { $f_return = true; }
			}
		}

		return $f_return;
	}

/**
	* Writes data to the socket during an authentication process.
	*
	* @param  string $f_data Data for the authentication protocol to send. 
	* @uses   directDBusSession::write()
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authWrite ($f_data)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authWrite ($f_data)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if (is_resource ($this->socket_dbus))
		{
			$f_data = str_replace ((array ("\r","\n")),"",$f_data);
			$f_return = $this->write ($f_data."\r\n");
		}

		return $f_return;
	}

/**
	* Writes data to the socket during an authentication process and parses the
	* response.
	*
	* @param  string $f_data Data for the authentication protocol to send. 
	* @param  boolean $f_return_response True to return the response 
	* @uses   directDBusSession::authReadParseResponse()
	* @uses   directDBusSession::authWrite()
	* @return mixed Data array on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function authWriteParseResponse ($f_data,$f_return_response = false)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->authWriteParseResponse ($f_data,+f_return_response)- (#echo(__LINE__)#)"; }

		$f_return = false;
		if ($this->authWrite ($f_data)) { $f_return = $this->authReadParseResponse ($f_return_response); }

		return $f_return;
	}

/**
	* Reads an incoming message and delegates it if requested.
	*
	* @param directDBusMessage &$f_message directDBusMessage object
	* @param array $f_body Body array if applicable
	* @uses  directDBusMessage::getHeader()
	* @uses  directDBusSession::callbackCaller()
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callback (&$f_message,$f_body)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callback (+f_message,+f_body)- (#echo(__LINE__)#)"; }

		if ((is_object ($f_message))&&(is_array ($f_body)))
		{
			$f_header_array = $f_message->getHeader (5);
			$f_type = $f_message->getHeader ("type");
			$f_continue_check = is_string ($f_type);

			if (($f_continue_check)&&(($f_type == "method_return")||($f_type == "error")))
			{
				if (($f_header_array)&&(isset ($this->dbus_callbacks[$f_header_array[1]])))
				{
					$this->callbackCaller ($this->dbus_callbacks[$f_header_array[1]],$f_message,$f_body);
					unset ($this->dbus_callbacks[$f_header_array[1]]);
				}
			}

/* -------------------------------------------------------------------------
Listeners
------------------------------------------------------------------------- */

			if ($f_continue_check)
			{
				$f_listener = "(\*|".(preg_quote ($f_type)).")";

				for ($f_i = 1;$f_i < 4;$f_i++)
				{
					$f_header_array = $f_message->getHeader ($f_i);
					$f_listener .= ($f_header_array ? "\:(\*|".(preg_quote ($f_header_array[1])).")" : "\:\*");
				}

				$f_listener = "($f_listener)";
			}

			if (($f_continue_check)&&(preg_match_all ("#^$f_listener$#im",$this->dbus_callback_listeners,$f_listeners,PREG_SET_ORDER)))
			{
				foreach ($f_listeners as $f_listener)
				{
					if (isset ($this->dbus_callbacks[$f_listener[1]])) { $this->callbackCaller ($this->dbus_callbacks[$f_listener[1]],$f_message,$f_body); }
				}
			}
		}
	}

/**
	* Calls found callback registrations.
	*
	* @param array $f_callbacks Found callbacks
	* @param directDBusMessage &$f_message directDBusMessage object
	* @param array $f_body Body array if applicable
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callbackCaller ($f_callbacks,&$f_message,$f_body)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackCaller (+f_callbacks,+f_message,+f_body)- (#echo(__LINE__)#)"; }

		if ((is_array ($f_callbacks))&&(is_object ($f_message))&&(is_array ($f_body)))
		{
			foreach ($f_callbacks as $f_callback)
			{
				if ((is_string ($f_callback))&&(function_exists ($f_callback))) { $f_callback ($f_message,$f_body); }
				elseif ((is_array ($f_callback))&&(count ($f_callback) == 2)&&(isset ($f_callback[0]))&&(isset ($f_callback[1]))&&(method_exists ($f_callback[0],$f_callback[1]))) { $f_callback[0]->{$f_callback[1]} ($f_message,$f_body); }
			}
		}
	}

/**
	* Checks if a callback is reachable from within this object.
	*
	* @param  mixed $f_callback Function name string or array with
	*         (&$object,"method") definition
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackCheck ($f_callback)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackCheck (+f_callback)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ((is_string ($f_callback))&&(function_exists ($f_callback))) { $f_return = true; }
		elseif ((is_array ($f_callback))&&(count ($f_callback) == 2)&&(isset ($f_callback[0]))&&(isset ($f_callback[1]))) { $f_return = method_exists ($f_callback[0],$f_callback[1]); }

		return $f_return;
	}

/**
	* Waits until a tinmeout occured or the defined amount of messages are
	* parsed.
	*
	* @param  integer $f_timeout Timeout for incoming messages (in microseconds)
	* @param  integer $f_messages Number of messages to parse
	* @uses   directDBusMessages::callbackListen()
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackListen ($f_timeout = 0,$f_messages = 0)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackListen ($f_timeout,$f_messages)- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { return $this->dbus_messages->callbackListen ($f_timeout,$f_messages); }
		else { return false; }
	}

/**
	* Registers multiple but unique function or method callbacks for a signal.
	*
	* @param  string $f_type D-BUS listener type
	* @param  string $f_path D-BUS path the signal that emits a signal
	* @param  string $f_interface D-BUS interface
	* @param  string $f_member D-BUS member (method emitting the signal)
	* @uses   directDBusSession::callbackCheck()
	* @return mixed Signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function callbackListenerId ($f_type,$f_path,$f_interface,$f_member)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackListenerId ($f_type,$f_path,$f_interface,$f_member,+f_callback)- (#echo(__LINE__)#)"; }

		if ((is_string ($f_type))&&(is_string ($f_path))&&(is_string ($f_interface))&&(is_string ($f_member)))
		{
			if (strlen ($f_type)) { $f_return = $f_type; }
			else { $f_return = "*"; }

			if (strlen ($f_path)) { $f_return .= ":".$f_path; }
			else { $f_return .= ":*"; }

			if (strlen ($f_interface)) { $f_return .= ":".$f_interface; }
			else { $f_return .= ":*"; }

			if (strlen ($f_member)) { $f_return .= ":".$f_member; }
			else { $f_return .= ":*"; }
		}
		else { $f_return = false; }

		return $f_return;
	}

/**
	* Registers multiple but unique function or method callbacks for a signal.
	*
	* @param  string $f_type D-BUS listener type
	* @param  string $f_path D-BUS path for the signal that emits a signal
	* @param  string $f_interface D-BUS interface
	* @param  string $f_member D-BUS member (method emitting the signal)
	* @param  mixed $f_callback Function name string or array with
	*         (&$object,"method") definition
	* @uses   directDBusSession::callbackCheck()
	* @uses   directDBusSession::callbackListenerId()
	* @return mixed Registered signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackRegisterListener ($f_type,$f_path,$f_interface,$f_member,$f_callback)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackRegisterListener ($f_type,$f_path,$f_interface,$f_member,+f_callback)- (#echo(__LINE__)#)"; }

		$f_return = $this->callbackCheck ($f_callback);
		if ($f_return) { $f_return = $this->callbackListenerId ($f_type,$f_path,$f_interface,$f_member); }

		if (is_string ($f_return))
		{
			if (!isset ($this->dbus_callbacks[$f_return])) { $this->dbus_callbacks[$f_return] = array (); }

			if (is_string ($f_callback))
			{
				$this->dbus_callbacks[$f_return][$f_callback] = $f_callback;
				$this->dbus_callback_listeners = (str_replace ("\n$f_return\n","\n",$this->dbus_callback_listeners)).$f_return."\n";
			}
			elseif (is_array ($f_callback))
			{
				$f_callback_class = get_class ($f_callback[0]);
				$this->dbus_callbacks[$f_return][$f_callback_class.".".$f_callback[1]] = $f_callback;
				$this->dbus_callback_listeners = (str_replace ("\n$f_return\n","\n",$this->dbus_callback_listeners)).$f_return."\n";
			}
			else { $f_return = false; }
		}

		return $f_return;
	}

/**
	* Registers multiple but unique function or method callbacks for the given
	* serial.
	*
	* @param integer $f_serial Serial used for a method call
	* @param mixed $f_callback Function name string or array with
	*        (&$object,"method") definition
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackRegisterSerial ($f_serial,$f_callback)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackRegisterSerial ($f_serial,+f_callback)- (#echo(__LINE__)#)"; }

		if (!isset ($this->dbus_callbacks[$f_serial])) { $this->dbus_callbacks[$f_serial] = array (); }

		if (is_string ($f_callback)) { $this->dbus_callbacks[$f_serial][$f_callback] = $f_callback; }
		elseif (is_array ($f_callback))
		{
			$f_callback_class = get_class ($f_callback[0]);
			if (is_string ($f_callback[1])) { $this->dbus_callbacks[$f_serial][$f_callback_class.".".$f_callback[1]] = $f_callback; }
		}
	}

/**
	* Removes the registration of the given function or method callbacks from a
	* signal.
	*
	* @param  string $f_type D-BUS listener type
	* @param  string $f_path D-BUS path for the signal that emits a signal
	* @param  string $f_interface D-BUS interface
	* @param  string $f_member D-BUS member (method emitting the signal)
	* @param  mixed $f_callback Function name string or array with
	*         (&$object,"method") definition
	* @uses   directDBusSession::callbackCheck()
	* @uses   directDBusSession::callbackListenerId()
	* @return mixed Unregistered signal ID on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function callbackUnregisterListener ($f_type,$f_path,$f_interface,$f_member,$f_callback)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->callbackUnregisterListener ($f_type,$f_path,$f_interface,$f_member,+f_callback)- (#echo(__LINE__)#)"; }

		$f_return = $this->callbackCheck ($f_callback);
		if ($f_return) { $f_return = $this->callbackListenerId ($f_type,$f_path,$f_interface,$f_member); }

		if (is_string ($f_return))
		{
			if (is_string ($f_callback))
			{
				if (isset ($this->dbus_callbacks[$f_return][$f_callback])) { unset ($this->dbus_callbacks[$f_return][$f_callback]); }
			}
			elseif (is_array ($f_callback))
			{
				$f_callback_class = get_class ($f_callback[0]);
				if (isset ($this->dbus_callbacks[$f_return][$f_callback_class.".".$f_callback[1]])) { unset ($this->dbus_callbacks[$f_return][$f_callback_class.".".$f_callback[1]]); }
			}
			else { $f_return = false; }

			$this->dbus_callback_listeners = str_replace ("\n$f_return\n","\n",$this->dbus_callback_listeners);
			if (empty ($this->dbus_callbacks[$f_return])) { unset ($this->dbus_callbacks[$f_return]); }
		}

		return $f_return;
	}

/**
	* Connects to an active D-BUS socket.
	*
	* @param  integer $f_sync_timeout Timeout for synchronized requests
	* @uses   directDBusSession::authHex()
	* @uses   directDBusSession::authWriteParseResponse()
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function connect ($f_sync_timeout = 3)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->connect ($f_sync_timeout)- (#echo(__LINE__)#)"; }

		$f_return = false;

		if (is_resource ($this->socket_dbus)) { $f_return = false; }
		elseif ($this->socket_available)
		{
			$f_error_code = 0;
			$f_error = "";

			if (stripos ($this->socket_path,"unix://") === 0) { $this->socket_dbus = @fsockopen ($this->socket_path,0,$f_error_code,$f_error,$this->socket_timeout); }
			elseif (preg_match ("#^(.+?)\:(\d+)$#",$this->socket_path,$f_port_array)) { $this->socket_dbus = @fsockopen ($f_port_array[1],$f_port_array[2],$f_error_code,$f_error,$this->socket_timeout); }

			if (($f_error_code)||($f_error)||(!is_resource ($this->socket_dbus)))
			{
				if ($this->debugging) { trigger_error ("directDBusSession/#echo(__FILEPATH__)# -dbus->connect ()- (#echo(__LINE__)#) reports: $f_error_code:".$f_error,E_USER_WARNING); }
				$this->socket_dbus = NULL;
			}
			else
			{
				$f_return = true;
				if (!@stream_set_blocking ($this->socket_dbus,0)) { @stream_set_timeout ($this->socket_dbus,$this->socket_timeout); }

				$f_auth_response = $this->authWriteParseResponse ("\x00AUTH ",true);
				if (is_array ($f_auth_response)) { $f_auth_response = explode (" ",$f_auth_response[1]); }
			}

			if (($f_return)&&(in_array ("EXTERNAL",$f_auth_response)))
			{
				if (function_exists ("posix_geteuid")) { $f_uid = posix_geteuid (); }
				else { $f_uid = getmyuid (); }

				if (is_numeric ($f_uid))
				{
					$f_auth_login_response = $this->authWriteParseResponse ("AUTH EXTERNAL ".($this->authHex ($f_uid)),true);
					if ((is_array ($f_auth_login_response))&&($f_auth_login_response[0] == "OK")) { $this->dbus_guid = $f_auth_login_response[1]; }
				}
			}

			if (($f_return)&&(!$this->dbus_guid)&&(in_array ("DBUS_COOKIE_SHA1",$f_auth_response))&&(isset ($this->dbus_cookie_path)))
			{
				if (!defined ("CLASS_directFile")) { @include_once ($this->ext_dbus_path."directFile.php"); }

				if (isset ($this->dbus_cookie_owner)) { $f_username = $this->dbus_cookie_owner; }
				elseif (function_exists ("posix_getpwuid"))
				{
					$f_userdata = posix_getpwuid (posix_geteuid ());
					$f_username = $f_userdata['name'];
				}
				else { $f_username = get_current_user (); }

				$f_auth_response = $this->authWriteParseResponse ("AUTH DBUS_COOKIE_SHA1 ".($this->authHex ($f_username)),true);
				$f_return = false;

				if ((defined ("CLASS_directFile"))&&(is_array ($f_auth_response))&&($f_auth_response[0] == "DATA"))
				{
					$f_auth_response = explode (" ",($this->authHex ($f_auth_response[1],true)),3);

					if (file_exists ($this->dbus_cookie_path."/".$f_auth_response[0]))
					{
						$f_file = new directFile ();
						$f_return = $f_file->open ($this->dbus_cookie_path."/".$f_auth_response[0],true,"r");
					}

					if ($f_return)
					{
						$f_file_content = $f_file->read ();
						$f_file->close ();
						if (is_bool ($f_file_content)) { $f_return = false; }
					}

					if (($f_return)&&(preg_match ("/^".(preg_quote ($f_auth_response[1]))." .+? (.+?)$/smi",$f_file_content,$f_result_array)))
					{
						$f_challenge = $this->authHex (mt_rand ());

						$f_auth_login_response = $this->authWriteParseResponse ("DATA ".($this->authHex ($f_challenge." ".(sha1 ($f_auth_response[2].":".$f_challenge.":".$f_result_array[1])))),true);
						if ((is_array ($f_auth_login_response))&&($f_auth_login_response[0] == "OK")) { $this->dbus_guid = $f_auth_login_response[1]; }
					}
					else { $f_return = false; }
				}

				if (!$f_return)
				{
					$f_auth_response = $this->authWriteParseResponse ("CANCEL ",true);
					$f_return = true;
				}
			}

			if (($f_return)&&(!$this->dbus_guid)&&(in_array ("EXTENSION_COOKIE_HMAC_SHA256",$f_auth_response))&&(isset ($this->dbus_cookie_path))&&(defined ("MHASH_SHA256")))
			{
				if (!defined ("CLASS_directFile")) { @include_once ($this->ext_dbus_path."directFile.php"); }

				$f_auth_response = $this->authWriteParseResponse ("AUTH EXTENSION_COOKIE_HMAC_SHA256",true);
				$f_return = false;

				if ((defined ("CLASS_directFile"))&&(is_array ($f_auth_response))&&($f_auth_response[0] == "DATA"))
				{
					$f_auth_response[1] = $this->authHex ($f_auth_response[1],true);

					if (file_exists ($this->dbus_cookie_path))
					{
						$f_file = new directFile ();
						$f_return = $f_file->open ($this->dbus_cookie_path,true,"rb");
					}

					if ($f_return)
					{
						$f_file_content = $f_file->read ();
						$f_file->close ();
						if (is_bool ($f_file_content)) { $f_return = false; }
					}

					if ($f_return)
					{
						if ((function_exists ("hash_hmac"))&&(in_array ("sha256",(hash_algos ())))) { $f_challenge = $this->authHex (hash_hmac ("sha256",$f_file_content,$f_auth_response[1],true)); }
						else
						{
							$f_auth_response[1] = str_pad ($f_auth_response[1],64,"\x00");

							$f_challenge = mhash (MHASH_SHA256,(($f_auth_response[1] ^ (str_repeat ("\x36",64))).$f_file_content));
							$f_challenge = mhash (MHASH_SHA256,(($f_auth_response[1] ^ (str_repeat ("\x5c",64))).$f_challenge));
							$f_challenge = $this->authHex ($f_challenge);
						}

						$f_auth_login_response = $this->authWriteParseResponse ("DATA ".$f_challenge,true);
						if ((is_array ($f_auth_login_response))&&($f_auth_login_response[0] == "OK")) { $this->dbus_guid = $f_auth_login_response[1]; }
					}
					else { $f_return = false; }
				}

				if (!$f_return)
				{
					$f_auth_response = $this->authWriteParseResponse ("CANCEL ",true);
					$f_return = true;
				}
			}

			if (($f_return)&&(!$this->dbus_guid)&&(in_array ("ANONYMOUS",$f_auth_response)))
			{
				$f_auth_response = $this->authWriteParseResponse ("AUTH ANONYMOUS",true);
				if ((is_array ($f_auth_response))&&($f_auth_response[0] == "OK")) { $this->dbus_guid = $f_auth_response[1]; }
			}

			if ($this->dbus_guid)
			{
				$f_return = $this->authWrite ("BEGIN ");
				if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->connect ()- (#echo(__LINE__)#) started binary protocol"; }
				$this->dbus_messages = new directDBusMessages ($this,$f_sync_timeout,$this->debugging);
			}
			else { $f_return = false; }
		}

		return $f_return;
	}

/**
	* Disconnects from an active session.
	*
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function disconnect ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->disconnect ()- (#echo(__LINE__)#)"; }
		$f_return = false;

		if (is_resource ($this->socket_dbus))
		{
			$f_return = fclose ($this->socket_dbus);
			$this->dbus_callbacks = array ();
			$this->dbus_callback_listeners = "\n";
			$this->dbus_guid = "";
			$this->dbus_messages = NULL;
			$this->socket_dbus = NULL;
			$this->socket_path = "";
		}

		return $f_return;
	}

/**
	* Returns the GUID of the server if authenticated successfully.
	*
	* @return mixed GUID string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getGuid ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->getGuid ()- (#echo(__LINE__)#)"; }

		if (is_resource ($this->socket_dbus)) { return $this->dbus_guid; }
		else { return false; }
	}

/**
	* Returns the socket file pointer if authenticated successfully.
	*
	* @return mixed File handle on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function &getHandle ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->getHandle ()- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { $f_return =& $this->socket_dbus; }
		else { $f_return = false; }

		return $f_return;
	}

/**
	* Returns an interface object for the given path.
	*
	* @param  string $f_path D-BUS path
	* @param  string $f_interface D-BUS interface
	* @param  string $f_destination D-BUS destination address
	* @return mixed File handle on success; false on error
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function getInterface ($f_path,$f_interface,$f_destination)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->getInterface ($f_path,$f_interface,$f_destination)- (#echo(__LINE__)#)"; }

		if (!defined ("CLASS_directXmlReader"))
		{
			if (@include_once ($this->ext_dbus_path."directXmlReader.php")) { $this->xml_parser = new directXmlReader ("UTF-8",true,(time ()),&$this->socket_timeout,$this->ext_dbus_path); }
		}

		if (!defined ("CLASS_directDBusInterface")) { @include_once ($f_ext_dbus_path."directDBusInterface.php"); }
		$f_return = false;

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))&&(defined ("CLASS_directDBusInterface"))&&($this->xml_parser != NULL))
		{
			$f_result = $this->sendMethodCallSyncResponse ($f_path,"org.freedesktop.DBus.Introspectable","Introspect",$f_destination);

			if ((!is_bool ($f_result))&&(isset ($f_result['body'][0])))
			{
				$f_result = $f_result['body'][0];
				$f_return = new directDBusInterface ($this,$f_path,$f_interface,$f_destination,($this->xml_parser->xml2array ($f_result)));
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
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->getNle ()- (#echo(__LINE__)#)"; }
		return $this->nle;
	}

/**
	* Reads data from the socket.
	*
	* @param  integer $f_length Length to read
	* @param  integer $f_timeout Timeout in seconds
	* @param  boolean $f_length_forced Data to send.
	* @return string Data read
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function read ($f_length,$f_timeout,$f_length_forced = false)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->read ($f_length,$f_timeout,+f_length_forced)- (#echo(__LINE__)#)"; }
		$f_return = "";

		if ((is_resource ($this->socket_dbus))&&($f_length))
		{
			$f_length_read = 0;
			$f_length_last_read = 0;
			$f_stream_check = array ($this->socket_dbus);
			$f_stream_ignored = NULL;
			$f_timeout_time = time () + $f_timeout;

			do
			{
/*#ifndef(PHP4) */
				stream_select ($f_stream_check,$f_stream_ignored,$f_stream_ignored,$f_timeout);
/* #\n*/
				$f_data_read = fread ($this->socket_dbus,$f_length);
				$f_length_last_read = strlen ($f_data_read);

				$f_return .= $f_data_read;
				$f_length_read += $f_length_last_read;
			}
			while ((!feof ($this->socket_dbus))&&($f_length_read < $f_length)&&($f_timeout_time > (time ()))&&(($f_length_last_read > 0)||($f_length_forced)));
		}

		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->read ()- (#echo(__LINE__)#) read ".(strlen ($f_return))." bytes"; }
		return $f_return;
	}

/**
	* Returns true if the session is available.
	*
	* @return boolean True if session is active.
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function resourceCheck ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->resourceCheck ()- (#echo(__LINE__)#)"; }
		return is_resource ($this->socket_dbus);
	}

/**
	* Sends a message without waiting for an response.
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
	* @uses   directDBusMessages::sendMethodCall()
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCall ($f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->sendMethodCall ($f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { return $this->dbus_messages->sendMethodCall ($f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter); }
		else { return false; }
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
	* @uses   directDBusMessages::sendMethodCallAsyncResponse()
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallAsyncResponse ($f_callback,$f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->sendMethodCallAsyncResponse (+f_callback,$f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { return $this->dbus_messages->sendMethodCallAsyncResponse ($f_callback,$f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter); }
		else { return false; }
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
	* @uses   directDBusMessages::sendMethodCallSyncResponse()
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendMethodCallSyncResponse ($f_path,$f_interface,$f_member,$f_destination = "",$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->sendMethodCallSyncResponse ($f_path,$f_interface,$f_member,$f_destination,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { return $this->dbus_messages->sendMethodCallSyncResponse ($f_path,$f_interface,$f_member,$f_destination,$f_flags,$f_signature,$f_parameter); }
		else { return false; }
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
	* @uses   directDBusMessages::sendSignal()
	* @return mixed directDBusMessage object on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function sendSignal ($f_path,$f_interface,$f_member,$f_flags = NULL,$f_signature = "",$f_parameter = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->sendSignal ($f_path,$f_interface,$f_member,+flags,$f_signature,+f_parameter)- (#echo(__LINE__)#)"; }

		if ((is_resource ($this->socket_dbus))&&(is_object ($this->dbus_messages))) { return $this->dbus_messages->sendSignal ($f_path,$f_interface,$f_member,$f_flags,$f_signature,$f_parameter); }
		else { return false; }
	}

/**
	* Sets connect data needed for DBUS_COOKIE_SHA1.
	*
	* @param  string $f_path Path to the DBUS_COOKIE_SHA1 directory.
	* @param  string $f_owner Owner username of this directory
	* @param  boolean True on success (if sha1 is supported)
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function setAuthCookie ($f_path = "",$f_owner = NULL)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->setAuthCookie ($f_path,+f_owner)- (#echo(__LINE__)#)"; }

		if (function_exists ("sha1"))
		{
			$this->dbus_cookie_owner = preg_replace ("#\s#","",$f_owner);
			$this->dbus_cookie_path = $f_path;

			return true;
		}
		else { return false; }
	}

/**
	* Sets a flag, removes it or returns 0x00 if called without parameter.
	*
	* @param  string $f_flag Flag to set
	* @param  string $f_status True to switch it on; false to off; NULL for the
	*         opposite
	* @param  string $f_flags Old flag value
	* @return string New flag
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function setFlag ($f_flag = "",$f_status = NULL,$f_flags = "")
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->setFlag ($f_flag,+f_status,+f_flags)- (#echo(__LINE__)#)"; }

		if (strlen ($f_flags)) { $f_return = $f_flags; }
		else { $f_return = 0; }

		switch ($f_flag)
		{
		case "dbus_name_allow_replacement":
		{
			$f_flag = 1;
			break 1;
		}
		case "no_reply_expected":
		{
			$f_flag = 1;
			break 1;
		}
		case "dbus_name_replace_existing":
		{
			$f_flag = 2;
			break 1;
		}
		case "no_auto_start":
		{
			$f_flag = 2;
			break 1;
		}
		case "dbus_name_do_not_queue":
		{
			$f_flag = 4;
			break 1;
		}
		default: { $f_flag = NULL; }
		}

		if ($f_flag != NULL)
		{
			if (is_bool ($f_status))
			{
				if ($f_status) { $f_return |= $f_flag; }
				else { $f_return &= ~$f_flag; }
			}
			else { $f_return ^= $f_flag; }
		}

		return $f_return;
	}

/**
	* Writes data to the socket.
	*
	* @param  string $f_data Data to send.
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function write ($f_data)
	{
		if ($this->debugging) { $this->debug[] = "directDBusSession/#echo(__FILEPATH__)# -dbus->write (+f_data)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ((is_resource ($this->socket_dbus))&&(!empty ($f_data)))
		{
			if (fwrite ($this->socket_dbus,$f_data)) { $f_return = true; }
		}

		return $f_return;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directDBusSession",true);
}

//j// EOF
?>