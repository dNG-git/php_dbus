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
* This is a proxy for an D-BUS 1.0 service.
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
* The "directProxy" class provides methods for the D-BUS message
* flow.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   DBus.php
* @since     v0.1.01
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directProxy
{
/**
	* @var string $dbus_guid D-BUS GUID
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_guid;
/**
	* @var directSession $socket_dbus D-BUS session oject
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_session;
/**
	* @var string $destination Destination requested
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $destination;
/**
	* @var object $event_handler The EventHandler is called whenever debug messages
	*      should be logged or errors happened.
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $event_handler;
/**
	* @var string $flags Flags cache for the next request 
*/
	/*#ifndef(PHP4) */public /* #*//*#ifdef(PHP4):var :#*/$flags;
/**
	* @var string $interface Interface requested
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $interface;
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
	* Constructor (PHP5) __construct (directProxy)
	*
	* @param directSession $session D-BUS session object
	* @param string $path D-BUS path
	* @param string $interface D-BUS interface
	* @param string $destination D-BUS destination address
	* @param array $xml_array XML node array with interface description
	* @param object $event_handler EventHandler to use
	* @since v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function __construct(&$session, $path, $interface, $destination, $xml_array, $event_handler = NULL)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -dbus->__construct(directProxy)- (#echo(__LINE__)#)"); }
		$this->dbus_guid = $session->getGuid();

		if ($this->dbus_guid)
		{
			$this->dbus_session =& $session;
			$this->event_handler = $event_handler;
			$this->interface = "";
			$this->nle = $session->getNle();

			$xml_interface_array = array();

			if (isset($xml_array['node']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_array['node']['interface']))
			{
				if (!isset($xml_array['node']['interface']['xml.mtree'])) { $xml_array['node']['interface'] = array($xml_array['node']['interface']); }

				foreach ($xml_array['node']['interface'] as &$xml_node_array)
				{
					if (isset($xml_node_array['xml.item']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_node_array['xml.item']['attributes']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_node_array['xml.item']['attributes']['name']) && $xml_node_array['xml.item']['attributes']['name'] == $interface) { $xml_interface_array = $xml_node_array; }
				}
			}

			if ($xml_interface_array)
			{
				$this->path = $path;
				$this->interface = $interface;
				$this->destination = $destination;
				$this->flags = "";
				$this->methods = array();
				$this->signals = array();

				if (isset($xml_interface_array['method']))
				{
					if (!isset($xml_interface_array['method']['xml.mtree'])) { $xml_interface_array['method'] = array($xml_interface_array['method']); }

					foreach ($xml_interface_array['method'] as &$xml_node_array)
					{
						if (isset($xml_node_array['xml.item']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_node_array['xml.item']['attributes']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_node_array['xml.item']['attributes']['name']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_node_array['arg']))
						{
							$this->methods[$xml_node_array['xml.item']['attributes']['name']] = array("in" => array(), "out" => array());

							if (!isset($xml_node_array['arg']['xml.mtree'])) { $xml_node_array['arg'] = array($xml_node_array['arg']); }

							foreach ($xml_node_array['arg'] as &$xml_sub_node_array)
							{
								if (isset($xml_sub_node_array['attributes']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_sub_node_array['attributes']['name']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_sub_node_array['attributes']['direction']/*#ifndef(PHP4) */, /* #*//*#ifdef(PHP4):) && isset(:#*/$xml_sub_node_array['attributes']['type'])) { $this->methods[$xml_node_array['xml.item']['attributes']['name']][$xml_sub_node_array['attributes']['direction']][$xml_sub_node_array['attributes']['name']] = $xml_sub_node_array['attributes']['type']; }
							}
						}
					}
				}
			}
		}
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directProxy
	*
	* @param directSession $session D-BUS session object
	* @param string $path D-BUS path
	* @param string $interface D-BUS interface
	* @param string $destination D-BUS destination address
	* @param object $event_handler EventHandler to use
	* @since v0.1.01
*\/
	function directProxy(&$session, $path, $interface, $destination, $xml_array, $event_handler = NULL) { $this->__construct($session, $path, $interface, $destination, $xml_array, $event_handler); }
:#\n*/
/**
	* Destructor (PHP5) __destruct (directMessage)
	*
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __destruct() { $this->dbus_session = NULL; }

/**
	* This method is called for "overloaded" or "inaccessible" methods of an
	* object. We use it to dynamically provide an interface generated from XML
	* data given to the constructor. Use "isReady()" to check if this is a valid
	* interface which has been parsed successfully.
	* 
	* @param  string $method The name of the method being called.
	* @param  array $arguments An enumerated array containing the parameters
	*         passed to "__call()".
	* @return mixed directMessage object on success (true for calls); false on
	*         error
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function __call($method, $arguments)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->__call($method, +arguments)- (#echo(__LINE__)#)"); }
		$return = false;

		if (strlen($this->interface))
		{
			if (strpos($method, "async_") === 0)
			{
				$method = substr($method, 6);

				if (isset($this->methods[$method]) && count($arguments) > 0)
				{
					$callback = array_shift($arguments);
					$flags = (strlen($this->flags) ? $this->flags : NULL);

					if (empty($this->methods[$method]['in'])) { $return = $this->dbus_session->sendMethodCallAsyncResponse($callback, $this->path, $this->interface, $method, $this->destination, $flags); }
					else { $return = $this->dbus_session->sendMethodCallAsyncResponse($callback, $this->path, $this->interface, $method, $this->destination, $flags, implode("", $this->methods[$method]['in']), $arguments); }
				}
			}
			elseif (strpos($method, "call_") === 0)
			{
				$method = substr($method, 5);

				if (isset($this->methods[$method]))
				{
					$flags = (strlen($this->flags) ? $this->dbus_session->setFlag("no_reply_expected", true, $this->flags) : $this->dbus_session->setFlag("no_reply_expected", true));

					if (empty($this->methods[$method]['in'])) { $return = $this->dbus_session->sendMethodCall($this->path, $this->interface, $method, $this->destination, $flags); }
					else { $return = $this->dbus_session->sendMethodCall($this->path, $this->interface, $method, $this->destination, $flags, implode("", $this->methods[$method]['in']), $arguments); }
				}
			}
			elseif (isset($this->methods[$method]))
			{
				$flags = (strlen($this->flags) ? $this->flags : NULL);

				if (empty($this->methods[$method]['in'])) { $return = $this->dbus_session->sendMethodCallSyncResponse($this->path, $this->interface, $method, $this->destination, $flags); }
				else { $return = $this->dbus_session->sendMethodCallSyncResponse($this->path, $this->interface, $method, $this->destination, $flags, implode("", $this->methods[$method]['in']), $arguments); }
			}

			$this->flags = "";
		}

		return $return;
	}

/**
	* Get an array with methods and their expected parameters ("in" and "out").
	*
	* @return mixed Array with methods on success; false if this is not a valid
	*         interface
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function getMethods()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getMethods()- (#echo(__LINE__)#)"); }

		if (strlen($this->interface)) { return $this->methods; }
		else { return false; }
	}

/**
	* Check if this is a valid interface which has been parsed successfully.
	*
	* @return boolean True if ready
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function isReady()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->isReady()- (#echo(__LINE__)#)"); }

		if (strlen($this->interface)) { return true; }
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
	* Sets flags for the next request (will be deleted after each request).
	*
	* @param  string $flags Flags
	* @since  v0.1.01
*/
	/*#ifndef(PHP4) */public /* #*/function setFlags($flags = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->setFlags(+flags)- (#echo(__LINE__)#)"); }
		if (strlen($flags)) { $this->flags = $flags; }
	}
}

//j// EOF