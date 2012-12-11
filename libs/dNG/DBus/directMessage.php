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
* This is an abstraction layer for a D-BUS message.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   DBus.php
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directMessage
{
/**
	* @var array $dbus_header D-BUS message header
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_header;
/**
	* @var string $dbus_raw D-BUS raw data stream
*/
	/*#ifndef(PHP4) */protected/* #*//*#ifdef(PHP4):var:#*/ $dbus_raw;
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
	* Constructor (PHP5) __construct (directMessage)
	*
	* @param directMessages $messages D-BUS message handler
	* @param object $event_handler EventHandler to use
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct($messages, $event_handler = NULL)
	{
		if ($event_handler !== NULL) { $event_handler->debug("#echo(__FILEPATH__)# -dbus->__construct(directMessage)- (#echo(__LINE__)#)"); }

		$this->dbus_header = NULL;
		$this->dbus_raw = NULL;
		$this->event_handler = $event_handler;
		$this->nle = $messages->getNle();
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directMessage
	*
	* @param directMessages $messages D-BUS message handler
	* @param object $event_handler EventHandler to use
	* @since v0.1.01
*\/
	function directMessage($messages, $event_handler = NULL) { $this->__construct($messages, $event_handler); }
:#\n*/
/**
	* Get a "complete type" from the signature as defined in the D-BUS
	* Specification 1.0.
	*
	* @param  string &$signature D-BUS signature
	* @param  integer $offset Signature offset
	* @param  integer $type_count Requested type number
	* @return string Complete type string definition
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function getCompleteType(&$signature, $offset, $type_count = 1)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getCompleteType($signature, $offset, $type_count)- (#echo(__LINE__)#)"); }
		$return = "";

		if (is_string($signature) && strlen($signature) > $offset && $type_count)
		{
			$arrays_count = 0;
			$dicts_count = 0;
			$types_single = array("b", "d", "g", "i", "n", "o", "q", "s", "t", "u", "x", "y");

			while ($type_count && isset($signature[$offset]))
			{
				if (in_array($signature[$offset], $types_single))
				{
					$return .= $signature[$offset];
					if (!$arrays_count && !$dicts_count) { $type_count--; }
				}
				elseif ($signature[$offset] == "a" || $signature[$offset] == "v") { $return .= $signature[$offset]; }
				else
				{
					switch ($signature[$offset])
					{
					case "(":
					{
						$return .= $signature[$offset];
						$arrays_count++;

						break 1;
					}
					case ")":
					{
						$return .= $signature[$offset];
						$arrays_count--;

						if ((!$arrays_count) && (!$dicts_count)) { $type_count--; }
						break 1;
					}
					case "{":
					{
						$return .= $signature[$offset];
						$dicts_count++;

						break 1;
					}
					case "}":
					{
						$return .= $signature[$offset];
						$dicts_count--;

						if ((!$arrays_count) && (!$dicts_count)) { $type_count--; }
						break 1;
					}
					}
				}

				$offset++;
			}
		}

		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getCompleteType()- (#echo(__LINE__)#) found ".$return); }
		return $return;
	}

/**
	* Read and return header fields.
	*
	* @param  mixed String with one of the following: endian, type, flags,
	*         protocol, body_size, serial or an integer representing the field
	*         byte value for a header field
	* @return mixed Found field content on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getHeader($field = "")
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getHeader()- (#echo(__LINE__)#)"); }
		$return = false;

		if ($this->dbus_header != NULL)
		{
			if ($field)
			{
				if (is_numeric($field))
				{
					foreach ($this->dbus_header[6] as $header_field)
					{
						if (is_bool($return) && $header_field[0] === $field) { $return = $header_field[1]; }
					}
				}
				else
				{
					switch ($field)
					{
					case "endian":
					{
						if (isset($this->dbus_header[0]) && ($this->dbus_header[0] == 108 || $this->dbus_header[0] == 66)) { $return = chr($this->dbus_header[0]); }
						break 1;
					}
					case "type":
					{
						if (isset($this->dbus_header[1]))
						{
							switch ($this->dbus_header[1])
							{
							case 1:
							{
								$return = "method_call";
								break 1;
							}
							case 2:
							{
								$return = "method_return";
								break 1;
							}
							case 3:
							{
								$return = "error";
								break 1;
							}
							case 4:
							{
								$return = "signal";
								break 1;
							}
							default: { $return = "unknown"; }
							}
						}

						break 1;
					}
					case "flags":
					{
						if (isset($this->dbus_header[2])) { $return = $this->dbus_header[2]; }
						break 1;
					}
					case "protocol":
					{
						if (isset($this->dbus_header[3])) { $return = $this->dbus_header[3]; }
						break 1;
					}
					case "body_size":
					{
						if (isset($this->dbus_header[4])) { $return = $this->dbus_header[4]; }
						break 1;
					}
					case "serial":
					{
						if (isset($this->dbus_header[5])) { $return = $this->dbus_header[5]; }
						break 1;
					}
					}
				}
			}
			else { $return = $this->dbus_header; }
		}

		return $return;
	}

/**
	* Returns the marshaled content.
	*
	* @return mixed Byte string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getRaw ()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getRaw()- (#echo(__LINE__)#)"); }

		if ($this->dbus_raw != NULL) { return $this->dbus_raw; }
		else { return false; }
	}

/**
	* Returns the marshaled body content. An empty string will be returned if it
	* is empty.
	*
	* @return mixed Byte string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getRawBody()
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->getRawBody()- (#echo(__LINE__)#)"); }
		$return = false;

		if ($this->dbus_header != NULL && $this->dbus_raw != NULL)
		{
			$body_start = 0;

			if (isset($this->dbus_header[4]))
			{
				$body_start = strlen($this->dbus_raw) - $this->dbus_header[4];

				if ($body_start >= 16)
				{
					if ($this->dbus_header[4]) { $return = substr($this->dbus_raw, $body_start); }
					else { $return = ""; }
				}
			}
		}

		return $return;
	}

/**
	* Marshals a given array based on the signature corresponding to the D-BUS
	* 1.0 Specification. Please note that 64bit values will be used as they are.
	* You have to provide a string with a maximum of 8 bytes in little endian.
	*
	* @param  string $signature Data signature
	* @param  array $data Data array
	* @param  integer $position Position within the array - usually 0
	* @return mixed Marshaled string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function marshalArray($signature, &$data, $position = 0)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->marshalArray($signature, +data, $position)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_string($signature) && is_array($data))
		{
			$data_next = true;
			$data_position = 0;
			$return = "";
			$signature_length = strlen($signature);
			$signature_position = 0;

			while ($signature_position < $signature_length && is_string($return))
			{
				switch ($signature[$signature_position])
				{
				case "(":
				{
					$position += $this->marshalSetBoundary($return, $position, 8);
					$sub_signature = $this->getCompleteType($signature, $signature_position);

					if ($sub_signature && is_array($data[$data_position]))
					{
						$sub_signature = substr($sub_signature, 1, -1);
						$sub_raw = $this->marshalArray($sub_signature, $data[$data_position], $position);

						if (is_bool($sub_raw)) { $return = false; }
						else
						{
							$position += strlen($sub_raw);
							$signature_position += 1 + strlen($sub_signature);
							$return .= $sub_raw;
						}
					}
					else { $return = false; }

					break 1;
				}
				case ")":
				{
					$data_next = false;
					break 1;
				}
				case "{":
				{
					$position += $this->marshalSetBoundary($return, $position, 8);
					$sub_signature = $this->getCompleteType($signature, $signature_position);

					if ($sub_signature && is_array($data[$data_position]))
					{
						$array_element_raw = reset($data[$data_position]);
						$array_element_raw = (is_array($array_element_raw) ? array_merge(array(key($data[$data_position])), (array_values($data[$data_position]))) : array(key($data[$data_position]), $array_element_raw));
						$sub_signature = substr($sub_signature, 1, -1);

						$sub_raw = $this->marshalArray($sub_signature, $array_element_raw, $position);
					}
					else { $sub_raw = false; }

					if (is_bool($sub_raw)) { $return = false; }
					else
					{
						$position += strlen($sub_raw);
						$signature_position += 1 + strlen($sub_signature);
						$return .= $sub_raw;
					}

					break 1;
				}
				case "}":
				{
					$data_next = false;
					break 1;
				}
				case "a":
				{
					$position += 4 + $this->marshalSetBoundary($return, $position, 4);
					$sub_signature = $this->getCompleteType($signature, $signature_position + 1);

					if ($sub_signature && is_array($data[$data_position]))
					{
						reset($data[$data_position]);

						$array_count = count($data[$data_position]);
						$array_offset = $position;
						$array_position = 0;
						$sub_raw = "";

						while ($return && $array_position < $array_count)
						{
							$array_element_raw = array($data[$data_position][$array_position]);
							$array_element_raw = $this->marshalArray($sub_signature, $array_element_raw, $position);

							if (is_string($return) && (!is_bool($array_element_raw)))
							{
								$position += strlen($array_element_raw);
								$sub_raw .= $array_element_raw;
							}
							else { $return = false; }

							$array_position++;
						}

						if (is_string($return))
						{
							$signature_position += strlen($sub_signature);
							$size = strlen($sub_raw);

							$size -= $this->typeGetPositionPadding($sub_signature[0], $array_offset) - $array_offset;
							$return .= pack("V", $size).$sub_raw;
						}
					}
					else { $return = false; }

					break 1;
				}
				case "b":
				{
					$position += $this->marshalSetBoundary($return, $position, 4);

					if (!isset($data[$data_position])) { $return = false; }
					elseif ($data[$data_position]) { $return .= "\x01\x00\x00\x00"; }
					else { $return .= "\x00\x00\x00\x00"; }

					$position += 4;
					break 1;
				}
				case "d":
				{
					$position += 8 + $this->marshalSetBoundary($return, $position, 8);

					if (strlen("{$data[$data_position]}") < 9) { $return .= pack("a8", "{$data[$data_position]}"); }
					else { $return = false; }

					break 1;
				}
				case "g":
				{
					$size = strlen($data[$data_position]);
				
					if (isset($data[$data_position]) && strlen($data[$data_position]) < 256) { $return .= pack("Ca*x", $size, $data[$data_position]); }
					else { $return = false; }

					$position += 2 + $size;
					break 1;
				}
				case "i":
				{
					$position += $this->marshalSetBoundary($return, $position, 4);

					if (isset($data[$data_position])) { $return .= $this->marshalSetNle(pack("L", $data[$data_position])); }
					else { $return = false; }

					$position += 4;
					break 1;
				}
				case "n":
				{
					$position += $this->marshalSetBoundary($return, $position, 2);

					if (isset($data[$data_position])) { $return .= $this->marshalSetNle(pack("s", $data[$data_position])); }
					else { $return = false; }

					$position += 2;
					break 1;
				}
				case "o":
				case "s":
				{
					$position += $this->marshalSetBoundary($return, $position, 4);

					if (isset($data[$data_position]))
					{
						$size = strlen($data[$data_position]);
						$return .= pack("V", $size).$data[$data_position]."\x00";
					}
					else { $return = false; }

					$position += 5 + $size;
					break 1;
				}
				case "q":
				{
					$position += $this->marshalSetBoundary($return, $position, 2);

					if (isset($data[$data_position])) { $return .= pack("v", $data[$data_position]); }
					else { $return = false; }

					$position += 2;
					break 1;
				}
				case "t":
				{
					$position += 8 + $this->marshalSetBoundary($return, $position, 8);

					if (strlen("{$data[$data_position]}") < 9) { $return .= pack("a8", "{$data[$data_position]}"); }
					else { $return = false; }

					break 1;
				}
				case "u":
				{
					$position += $this->marshalSetBoundary($return, $position, 4);

					if (isset($data[$data_position])) { $return .= pack("V", $data[$data_position]); }
					else { $return = false; }

					$position += 4;
					break 1;
				}
				case "v":
				{
					if (is_array($data[$data_position]) && isset($data[$data_position][0]))
					{
						$sub_signature = "g".$data[$data_position][0];
						$sub_raw = $this->marshalArray($sub_signature, $data[$data_position], $position);

						if (is_bool($sub_raw)) { $return = false; }
						else
						{
							$return .= $sub_raw;
							$position += strlen($sub_raw);
						}
					}
					else { $return = false; }

					$data_next = false;
					break 1;
				}
				case "x":
				{
					$position += 8 + $this->marshalSetBoundary($return, $position, 8);

					if (strlen("{$data[$data_position]}") < 9) { $return .= pack("a8", "{$data[$data_position]}"); }
					else { $return = false; }

					break 1;
				}
				case "y":
				{

					if (is_string($data[$data_position]) && strlen($data[$data_position]) == 1) { $return .= $data[$data_position]; }
					elseif (is_numeric($data[$data_position]) && $data[$data_position] < 256) { $return .= pack("C", $data[$data_position]); }
					else { $return = false; }

					$position++;
					break 1;
				}
				default: { $return = false; }
				}

				$signature_position++;

				if ($data_next) { $data_position++; }
				else { $data_next = true; }
			}
		}
		else { $return = false; }

		return $return;
	}

/**
	* Fills the given data string with NUL bytes until the defined boundary has
	* been reached.
	*
	* @param  string &$data Data string
	* @param  integer $position Current position
	* @param  integer $boundary_spec Boundary to use
	* @return integer Number of bytes added
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function marshalSetBoundary(&$data, $position, $boundary_spec)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->marshalSetBoundary(+data, $position, $boundary_spec)- (#echo(__LINE__)#)"); }
		$return = 0;

		if (is_string($data) && $position > 1 && $boundary_spec > 1)
		{
			$position = $boundary_spec - ($position % $boundary_spec);

			if ($position && $position < $boundary_spec)
			{
				if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->marshalSetBoundary()- (#echo(__LINE__)#) added $position NUL bytes to conform to the requested boundary"); }
				for ($i = 0;$i < $position;$i++) { $data .= "\x00"; }
				$return = $position;
			}
		}

		return $return;
	}

/**
	* Sends a message and waits for the response.
	*
	* @param  integer $data Byte value to byteswap (if we are on a native big
	*         endian system)
	* @return string Byte string
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function marshalSetNle($data)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->marshalSetNle(+data)- (#echo(__LINE__)#)"); }

		if ((!$this->nle) && strlen($data) > 1)
		{
			$bytes_inverted = array();
			$position = 0;

			for ($i = (strlen($data) - 1);$i > -1;$i--)
			{
				$bytes_inverted[$position] = $data[$i];
				$position++;
			}

			return implode("", $bytes_inverted);
		}
		else { return $data; }
	}

/**
	* Sets the given header array and raw string for this message.
	*
	* @param  array $header Header array parsed with "unmarshal()"
	* @param  string $raw Raw binary string
	* @param  boolean $overwrite True to ignore data already set
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function set($header, $raw, $overwrite = false)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->set(+header, $raw, +overwrite)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_array($header) && is_string($raw) && ($overwrite || ($this->dbus_header == NULL && $this->dbus_raw == NULL)))
		{
			$return = true;
			$this->dbus_header = $header;
			$this->dbus_raw = $raw;
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

/**
	* Returns the defined boundary corresponding to the D-BUS 1.0 Specification.
	*
	* @param  string $type Type code (ASCII)
	* @return integer Defined boundary
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function typeGetPadding($type)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->typeGetPadding($type)- (#echo(__LINE__)#)"); }
		$return = 0;

		if (is_string($type))
		{
			switch ($type)
			{
			case "(":
			{
				$return = 8;
				break 1;
			}
			case "{":
			{
				$return = 8;
				break 1;
			}
			case "a":
			{
				$return = 4;
				break 1;
			}
			case "b":
			{
				$return = 4;
				break 1;
			}
			case "d":
			{
				$return = 8;
				break 1;
			}
			case "i":
			{
				$return = 4;
				break 1;
			}
			case "n":
			{
				$return = 2;
				break 1;
			}
			case "o":
			{
				$return = 4;
				break 1;
			}
			case "q":
			{
				$return = 2;
				break 1;
			}
			case "s":
			{
				$return = 4;
				break 1;
			}
			case "t":
			{
				$return = 8;
				break 1;
			}
			case "u":
			{
				$return = 4;
				break 1;
			}
			case "x":
			{
				$return = 8;
				break 1;
			}
			}
		}

		return $return;
	}

/**
	* Calculates the new position to correspond to the given type boundary.
	*
	* @param  string $type Type code (ASCII)
	* @param  integer $position Current position in the byte string
	* @return integer New position (Position of the needed boundary)
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function typeGetPositionPadding($type, $position)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->typeGetPositionPadding($type, $position)- (#echo(__LINE__)#)"); }

		$boundary_spec = $this->typeGetPadding($type);
		$return = $position;

		if ($boundary_spec > 0)
		{
			$position = $boundary_spec - ($position % $boundary_spec);
			if ($position && $position < $boundary_spec) { $return += $position; }
		}

		return $return;
	}

/**
	* Unmarshals a given byte string based on the signature corresponding to the
	* D-BUS 1.0 Specification. Please note that 64bit values are returned as
	* byte strings.
	*
	* @param  mixed $le Position (integer) of the endian definition within the
	*         byte string or one of the defined endian ASCII codes (string)
	* @param  string $signature Data signature
	* @param  string &$data Byte data
	* @param  integer $position
	* @return mixed Data array on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function unmarshal($le, $signature, &$data, $position = -1)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->unmarshal(+le, $signature, +data, $position)- (#echo(__LINE__)#)"); }
		$return = false;

		if (is_string($signature) && is_string($data))
		{
			$position_element = false;
			$return = array();
			$return_position = 0;
			$signature_length = strlen($signature);
			$signature_position = 0;

			if ($position < 0) { $position = 0; }
			else { $position_element = true; }

			while ($signature_position < $signature_length && is_array($return))
			{
				switch ($signature[$signature_position])
				{
				case "(":
				{
					$position += $this->unmarshalGetBoundary($position, 8);
					$sub_signature = $this->getCompleteType($signature, $signature_position);

					if ($sub_signature)
					{
						$sub_signature = substr($sub_signature, 1, -1);
						$bytes_unpacked = $this->unmarshal($le, $sub_signature, $data, $position);

						if ($bytes_unpacked)
						{
							if (isset($bytes_unpacked['position']))
							{
								$position = $bytes_unpacked['position'];
								unset($bytes_unpacked['position']);
							}

							$return[$return_position] = $bytes_unpacked;
						}
						else { $return = false; }
					}
					else { $return = false; }

					if ($return)
					{
						$return_position++;
						$signature_position += 1 + strlen($sub_signature);
					}

					break 1;
				}
				case ")": break 1;
				case "{":
				{
					$position += $this->unmarshalGetBoundary($position, 8);
					$sub_signature = $this->getCompleteType($signature, $signature_position);

					if ($sub_signature && strlen($this->getCompleteType($sub_signature, 1)) == 1 && strlen($sub_signature) == strlen($this->getCompleteType($sub_signature, 2)) + 3)
					{
						$sub_signature = substr($sub_signature, 1, -1);
						$bytes_unpacked = $this->unmarshal($le, $sub_signature, $data, $position);

						if ($bytes_unpacked)
						{
							if (isset($bytes_unpacked['position']))
							{
								$position = $bytes_unpacked['position'];
								unset($bytes_unpacked['position']);
							}

							$return[$return_position] = array($bytes_unpacked[0] => $bytes_unpacked[1]);
						}
						else { $return = false; }
					}
					else { $return = false; }

					if ($return)
					{
						$return_position++;
						$signature_position += 1 + strlen($sub_signature);
					}

					break 1;
				}
				case "}": break 1;
				case "a":
				{
					$position += $this->unmarshalGetBoundary($position, 4);
					$sub_read = $this->unmarshalRead($data, $position, 4);
 
					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$bytes_unpacked = (($le == "B") ? unpack("N", $sub_read) : unpack("V", $sub_read));
						$position += 4;
						$sub_signature = "";

						if ($bytes_unpacked) { $sub_signature = $this->getCompleteType($signature, $signature_position + 1); }

						if ($sub_signature)
						{
							$position = $this->typeGetPositionPadding($sub_signature[0], $position);
							$return[$return_position] = array();
							$sub_size = $bytes_unpacked[1];

							while ($return && $sub_size)
							{
								$bytes_unpacked = $this->unmarshal($le, $sub_signature, $data, $position);

								if ($bytes_unpacked)
								{
									$sub_size -= ($bytes_unpacked['position'] - $position);
									$position = $bytes_unpacked['position'];
									unset($bytes_unpacked['position']);

									$return[$return_position] = array_merge($return[$return_position], $bytes_unpacked);
								}
								else { $return = false; }
							}

							if ($return)
							{
								$return_position++;
								$signature_position += strlen($sub_signature);
							}
						}
						elseif ($bytes_unpacked) { $return = false; }
					}

					break 1;
				}
				case "b":
				{
					$position += $this->unmarshalGetBoundary($position, 4);
					$sub_read = $this->unmarshalRead($data, $position, 4);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						if ($le == "B") { $return[$return_position] = (($sub_read[3] == "\x01") ? true : false); }
						else { $return[$return_position] = (($sub_read[0] == "\x01") ? true : false); }

						$position += 4;
						$return_position++;
					}

					break 1;
				}
				case "d":
				{
					$position += $this->unmarshalGetBoundary($position, 8);
					$sub_read = $this->unmarshalRead($data, $position, 8);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return[$return_position] = $this->unmarshalSetLe($le, "", $sub_read);
						$return_position++;
						$position += 8;
					}

					break 1;
				}
				case "g":
				{
					$sub_read = $this->unmarshalRead($data, $position, 1);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$bytes_unpacked = unpack("C", $sub_read);
						$position++;

						if ($bytes_unpacked)
						{
							$return[$return_position] = $this->unmarshalRead($data, $position, $bytes_unpacked[1]);
							$return_position++;
							$position += 1 + $bytes_unpacked[1];
						}
					}

					break 1;
				}
				case "i":
				{
					$position += $this->unmarshalGetBoundary($position, 4);
					$sub_read = $this->unmarshalRead($data, $position, 4);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return[$return_position] = $this->unmarshalSetLe($le, "L", $sub_read);
						$return_position++;
						$position += 4;
					}

					break 1;
				}
				case "n":
				{
					$position += $this->unmarshalGetBoundary($position, 2);
					$sub_read = $this->unmarshalRead($data, $position, 2);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return[$return_position] = $this->unmarshalSetLe($le, "s", $sub_read);
						$return_position++;
						$position += 2;
					}

					break 1;
				}
				case "o":
				case "s":
				{
					$position += $this->unmarshalGetBoundary($position, 4);
					$sub_read = $this->unmarshalRead($data, $position, 4);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$bytes_unpacked = (($le == "B") ? unpack("N", $sub_read) : unpack("V", $sub_read));
						$position += 4;

						if ($bytes_unpacked)
						{
							$return[$return_position] = $this->unmarshalRead($data, $position, $bytes_unpacked[1]);
							$return_position++;
							$position += 1 + $bytes_unpacked[1];
						}
					}

					break 1;
				}
				case "q":
				{
					$position += $this->unmarshalGetBoundary($position, 2);
					$sub_read = $this->unmarshalRead($data, $position, 2);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return = (($le == "B") ? array_merge($return, (unpack("n", $sub_read))) : array_merge($return, (unpack("v", $sub_read))));
						$return_position++;
						$position += 4;
					}

					break 1;
				}
				case "t":
				{
					$position += $this->unmarshalGetBoundary($position, 8);
					$sub_read = $this->unmarshalRead($data, $position, 8);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return[$return_position] = $this->unmarshalSetLe($le, "", $sub_read);
						$return_position++;
						$position += 8;
					}

					break 1;
				}
				case "u":
				{
					$position += $this->unmarshalGetBoundary($position, 4);
					$sub_read = $this->unmarshalRead($data, $position, 4);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return = (($le == "B") ? array_merge($return, (unpack("N", $sub_read))) : array_merge($return, (unpack("V", $sub_read))));
						$return_position++;
						$position += 4;
					}

					break 1;
				}
				case "v":
				{
					$sub_read = $this->unmarshalRead($data, $position, 1);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$bytes_unpacked = unpack("C", $sub_read);
						$position++;

						if ($bytes_unpacked)
						{
							$sub_read = $this->unmarshalRead($data, $position, $bytes_unpacked[1]);
							$position += 1 + $bytes_unpacked[1];
						}

						$return[$return_position] = array($sub_read);
						$bytes_unpacked = $this->unmarshal($le, $sub_read, $data, $position);

						if (is_bool($bytes_unpacked)) { $return = false; }
						else
						{
							$position = $bytes_unpacked['position'];
							unset($bytes_unpacked['position']);
							$return[$return_position] = array_merge($return[$return_position], $bytes_unpacked);
						}

						$return_position++;
					}

					break 1;
				}
				case "x":
				{
					$position += $this->unmarshalGetBoundary($position, 8);
					$sub_read = $this->unmarshalRead($data, $position, 8);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						$return[$return_position] = $this->unmarshalSetLe($le, "", $sub_read);
						$return_position++;
						$position += 8;
					}

					break 1;
				}
				case "y":
				{
					$sub_read = $this->unmarshalRead($data, $position, 1);

					if (is_bool($sub_read)) { $return = false; }
					else
					{
						if (is_int($le) && $le == $signature_position) { $le = $sub_read; }
						$bytes_unpacked = unpack("C", $sub_read);

						if ($bytes_unpacked)
						{
							$return[$return_position] = $bytes_unpacked[1];
							$return_position++;
							$position++;
						}
						else { $return = false; }
					}

					break 1;
				}
				default: { $return = false; }
				}

				$signature_position++;
			}

			if (is_array($return) && $position_element) { $return['position'] = $position; }
		}
		else { $return = false; }

		return $return;
	}

/**
	* Returns the number of bytes to seek to reach the next boundary.
	*
	* @param  integer $position Current position
	* @param  integer $boundary_spec Boundary to use
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalGetBoundary($position, $boundary_spec)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->unmarshalGetBoundary($position, $boundary_spec)- (#echo(__LINE__)#)"); }
		$return = 0;

		if ($boundary_spec > 0)
		{
			$position = $boundary_spec - ($position % $boundary_spec);
			if ($position && $position < $boundary_spec) { $return = $position; }
		}

		return $return;
	}

/**
	* Reads the given bytes from the data string.
	*
	* @param  string &$data Byte data
	* @param  integer $offset Offset
	* @param  integer $length Length
	* @return mixed Data read on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalRead(&$data, $offset, $length)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->unmarshalRead(+data, $offset, $length)- (#echo(__LINE__)#)"); }

		if (is_string($data) && is_numeric($offset) && is_numeric($length) && strlen($data) >= $offset + $length) { return substr($data, $offset, $length); }
		else { return false; }
	}

/**
	* Sets the endian mode requested and byteswaps data when necessary.
	*
	* @param  string $le Endian ASCII code
	* @param  string $unpack_mode Mode to use for unpacking (or an empty string if
	*         it should only byteswap the data)
	* @param  string $data Byte data
	* @return Byte data (byteswapped if necessary)
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalSetLe($le, $unpack_mode, $data)
	{
		if ($this->event_handler !== NULL) { $this->event_handler->debug("#echo(__FILEPATH__)# -dbus->unmarshalSetLe($le, $unpack_mode, +data)- (#echo(__LINE__)#)"); }
		$return = 0;

		if (is_string($data))
		{
			if (($le == "B" && $this->nle) || ($le == "l" && !$this->nle))
			{
				$bytes_inverted = array();
				$position = 0;

				for ($i = strlen($data) - 1;$i > -1;$i--)
				{
					$bytes_inverted[$position] = $data[$i];
					$position++;
				}

				if ($unpack_mode)
				{
					$bytes_inverted = unpack($unpack_mode, implode("", $bytes_inverted));
					if ($bytes_inverted) { $return = $bytes_inverted[1]; }
				}
				else { $return = implode("", $bytes_inverted); }
			}
			elseif ($unpack_mode)
			{
				$bytes_unpacked = unpack($unpack_mode, $data);
				if ($bytes_unpacked) { $return = $bytes_unpacked[1]; }
			}
			else { $return = $data; }
		}

		return $return;
	}
}

//j// EOF