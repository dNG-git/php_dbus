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
* @since     v0.1.01
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

if (!defined ("CLASS_directDBusInterface"))
{
/**
* The "directDBusInterface" class provides methods for the D-BUS message
* flow.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   ext_dbus
* @since     v0.1.01
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directDBusInterface
{
/**
	* @var string $dbus_guid D-BUS GUID
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_guid;
/**
	* @var direct_dbus_session $socket_dbus D-BUS session oject
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_session;
/**
	* @var string $destination Destination requested
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $destination;
/**
	* @var string $interface Interface requested
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $interface;
/**
	* @var array $debug Debug message container 
*/
	/*#ifndef(PHP4) */public /* #*//*#ifdef(PHP4):var :#*/$debug;
/**
	* @var string $flags Flags cache for the next request 
*/
	/*#ifndef(PHP4) */public /* #*//*#ifdef(PHP4):var :#*/$flags;
/**
	* @var boolean $debugging True if we should fill the debug message
	*      container 
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $debugging;
/**
	* @var array $methods Detected methods of the given interface
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $methods;
/**
	* @var string $path Path requested
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $path;
/**
	* @var array $signals Detected signals of the given interface
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $signals;

/* -------------------------------------------------------------------------
Construct the class using old and new behavior
------------------------------------------------------------------------- */

/**
	* Constructor (PHP5) __construct (directDBusInterface)
	*
	* @param directDBusSession $f_session D-BUS session object
	* @param string $f_path D-BUS path
	* @param string $f_interface D-BUS interface
	* @param string $f_destination D-BUS destination address
	* @param boolean $f_debug Debug flag
	* @uses  directDBusInterface::getName()
	* @uses  directDBusSession::getGuid()
	* @uses  directDBusSession::getNle()
	* @since v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function __construct (&$f_session,$f_path,$f_interface,$f_destination,$f_xml_array,$f_debug = false)
	{
		$this->debugging = $f_debug;
		if ($this->debugging) { $this->debug = array ("directDBusInterface/#echo(__FILEPATH__)# -dbus->__construct (directDBusInterface)- (#echo(__LINE__)#)"); }
		$this->dbus_guid = $f_session->getGuid ();

		if ($this->dbus_guid)
		{
			$this->dbus_session =& $f_session;
			$this->interface = "";
			$this->nle = $f_session->getNle ();

			$f_xml_interface_array = array ();

			if (isset ($f_xml_array['node']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_array['node']['interface']))
			{
				if (!isset ($f_xml_array['node']['interface']['xml.mtree'])) { $f_xml_array['node']['interface'] = array ($f_xml_array['node']['interface']); }

				foreach ($f_xml_array['node']['interface'] as &$f_xml_node_array)
				{
					if ((isset ($f_xml_node_array['xml.item']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_node_array['xml.item']['attributes']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_node_array['xml.item']['attributes']['name']))&&($f_xml_node_array['xml.item']['attributes']['name'] == $f_interface)) { $f_xml_interface_array = $f_xml_node_array; }
				}
			}

			if ($f_xml_interface_array)
			{
				$this->path = $f_path;
				$this->interface = $f_interface;
				$this->destination = $f_destination;
				$this->flags = "";
				$this->methods = array ();
				$this->signals = array ();

				if (isset ($f_xml_interface_array['method']))
				{
					if (!isset ($f_xml_interface_array['method']['xml.mtree'])) { $f_xml_interface_array['method'] = array ($f_xml_interface_array['method']); }

					foreach ($f_xml_interface_array['method'] as &$f_xml_node_array)
					{
						if (isset ($f_xml_node_array['xml.item']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_node_array['xml.item']['attributes']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_node_array['xml.item']['attributes']['name']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_node_array['arg']))
						{
							$this->methods[$f_xml_node_array['xml.item']['attributes']['name']] = array ("in" => array (),"out" => array ());

							if (!isset ($f_xml_node_array['arg']['xml.mtree'])) { $f_xml_node_array['arg'] = array ($f_xml_node_array['arg']); }

							foreach ($f_xml_node_array['arg'] as &$f_xml_sub_node_array)
							{
								if (isset ($f_xml_sub_node_array['attributes']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_sub_node_array['attributes']['name']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_sub_node_array['attributes']['direction']/*#ifndef(PHP4) */,/* #*//*#ifdef(PHP4):) && isset (:#*/$f_xml_sub_node_array['attributes']['type'])) { $this->methods[$f_xml_node_array['xml.item']['attributes']['name']][$f_xml_sub_node_array['attributes']['direction']][$f_xml_sub_node_array['attributes']['name']] = $f_xml_sub_node_array['attributes']['type']; }
							}
						}
					}
				}
			}
		}
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directDBusInterface
	*
	* @param directDBusSession $f_session D-BUS session object
	* @param string $f_path D-BUS path
	* @param string $f_interface D-BUS interface
	* @param string $f_destination D-BUS destination address
	* @param boolean $f_debug Debug flag
	* @uses  directDBusInterface::__construct()
	* @since v0.1.01
*\/
	function directDBusInterface (&$f_session,$f_path,$f_interface,$f_destination,$f_xml_array,$f_debug = false) { $this->__construct ($f_session,$f_path,$f_interface,$f_destination,$f_xml_array,$f_debug); }
:#\n*/
/**
	* This method is called for "overloaded" or "inaccessible" methods of an
	* object. We use it to dynamically provide an interface generated from XML
	* data given to the constructor. Use "isReady ()" to check if this is a valid
	* interface which has been parsed successfully.
	* 
	* @param  string $f_method The name of the method being called.
	* @param  array $f_arguments An enumerated array containing the parameters
	*         passed to "__call()".
	* @uses   directDBusSession::sendMethodCall()
	* @uses   directDBusSession::sendMethodCallAsyncResponse()
	* @uses   directDBusSession::sendMethodCallSyncResponse()
	* @uses   directDBusSession::setFlag()
	* @return mixed directDBusMessage object on success (true for calls); false
	*         on error
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function __call ($f_method,$f_arguments)
	{
		if ($this->debugging) { $this->debug[] = "directDBusInterface/#echo(__FILEPATH__)# -dbus->__call ($f_method,+f_arguments)- (#echo(__LINE__)#)"; }

		$f_return = false;

		if (strlen ($this->interface))
		{
			if (strpos ($f_method,"async_") === 0)
			{
				$f_method = substr ($f_method,6);

				if ((isset ($this->methods[$f_method]))&&(count ($f_arguments) > 0))
				{
					$f_callback = array_shift ($f_arguments);
					$f_flags = (strlen ($this->flags) ? $this->flags : NULL);

					if (empty ($this->methods[$f_method]['in'])) { $f_return = $this->dbus_session->sendMethodCallAsyncResponse ($f_callback,$this->path,$this->interface,$f_method,$this->destination,$f_flags); }
					else { $f_return = $this->dbus_session->sendMethodCallAsyncResponse ($f_callback,$this->path,$this->interface,$f_method,$this->destination,$f_flags,(implode ("",$this->methods[$f_method]['in'])),$f_arguments); }
				}
			}
			elseif (strpos ($f_method,"call_") === 0)
			{
				$f_method = substr ($f_method,5);

				if (isset ($this->methods[$f_method]))
				{
					$f_flags = (strlen ($this->flags) ? $this->dbus_session->setFlag ("no_reply_expected",true,$this->flags) : $this->dbus_session->setFlag ("no_reply_expected",true));

					if (empty ($this->methods[$f_method]['in'])) { $f_return = $this->dbus_session->sendMethodCall ($this->path,$this->interface,$f_method,$this->destination,$f_flags); }
					else { $f_return = $this->dbus_session->sendMethodCall ($this->path,$this->interface,$f_method,$this->destination,$f_flags,(implode ("",$this->methods[$f_method]['in'])),$f_arguments); }
				}
			}
			elseif (isset ($this->methods[$f_method]))
			{
				$f_flags = (strlen ($this->flags) ? $this->flags : NULL);

				if (empty ($this->methods[$f_method]['in'])) { $f_return = $this->dbus_session->sendMethodCallSyncResponse ($this->path,$this->interface,$f_method,$this->destination,$f_flags); }
				else { $f_return = $this->dbus_session->sendMethodCallSyncResponse ($this->path,$this->interface,$f_method,$this->destination,$f_flags,(implode ("",$this->methods[$f_method]['in'])),$f_arguments); }
			}

			$this->flags = "";
		}

		return $f_return;
	}

/**
	* Get an array with methods and their expected parameters ("in" and "out").
	*
	* @return mixed Array with methods on success; false if this is not a valid
	*         interface
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function getMethods ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusInterface/#echo(__FILEPATH__)# -dbus->getMethods ()- (#echo(__LINE__)#)"; }

		if (strlen ($this->interface)) { return $this->methods; }
		else { return false; }
	}

/**
	* Check if this is a valid interface which has been parsed successfully.
	*
	* @return boolean True if ready
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function isReady ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusInterface/#echo(__FILEPATH__)# -dbus->isReady ()- (#echo(__LINE__)#)"; }

		if (strlen ($this->interface)) { return true; }
		else { return false; }
	}

/**
	* Sets flags for the next request (will be deleted after each request).
	*
	* @param  string $f_flags Flags
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function setFlags ($f_flags = "")
	{
		if ($this->debugging) { $this->debug[] = "directDBusInterface/#echo(__FILEPATH__)# -dbus->setFlags (+f_flags)- (#echo(__LINE__)#)"; }
		if (strlen ($f_flags)) { $this->flags = $f_flags; }
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directDBusInterface",true);
}

//j// EOF
?>