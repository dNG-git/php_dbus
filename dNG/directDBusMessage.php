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

if (!defined ("CLASS_directDBusMessage"))
{
/**
* This is an abstraction layer for a D-BUS message.
*
* @author    direct Netware Group
* @copyright (C) direct Netware Group - All rights reserved
* @package   ext_dbus
* @since     v0.1.00
* @license   http://www.direct-netware.de/redirect.php?licenses;mpl2
*            Mozilla Public License, v. 2.0
*/
class directDBusMessage
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
	* Constructor (PHP5) __construct (directDBusMessage)
	*
	* @param directDBusMessages $f_messages D-BUS message handler
	* @param boolean $f_debug Debug flag
	* @uses  directDBusMessages::getNle()
	* @since v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function __construct ($f_messages,$f_debug = false)
	{
		$this->debugging = $f_debug;
		if ($this->debugging) { $this->debug = array ("directDBusMessage/#echo(__FILEPATH__)# -dbus->__construct (directDBusMessage)- (#echo(__LINE__)#)"); }

		$this->dbus_header = NULL;
		$this->dbus_raw = NULL;
		$this->nle = $f_messages->getNle ();
	}
/*#ifdef(PHP4):
/**
	* Constructor (PHP4) directDBusMessage
	*
	* @param directDBusMessages $f_messages D-BUS message handler
	* @param boolean $f_debug Debug flag
	* @uses  directDBusMessage::__construct()
	* @since v0.1.01
*\/
	function directDBusMessage ($f_messages,$f_debug = false) { $this->__construct ($f_messages,$f_debug); }
:#\n*/
/**
	* Get a "complete type" from the signature as defined in the D-BUS
	* Specification 1.0.
	*
	* @param  string &$f_signature D-BUS signature
	* @param  integer $f_offset Signature offset
	* @param  integer $f_type_count Requested type number
	* @return string Complete type string definition
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function getCompleteType (&$f_signature,$f_offset,$f_type_count = 1)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->getCompleteType ($f_signature,$f_offset,$f_type_count)- (#echo(__LINE__)#)"; }
		$f_return = "";

		if (((is_string ($f_signature)))&&(strlen ($f_signature) > $f_offset)&&($f_type_count))
		{
			$f_arrays_count = 0;
			$f_dicts_count = 0;
			$f_types_single = array ("b","d","g","i","n","o","q","s","t","u","x","y");

			while (($f_type_count)&&(isset ($f_signature[$f_offset])))
			{
				if (in_array ($f_signature[$f_offset],$f_types_single))
				{
					$f_return .= $f_signature[$f_offset];
					if ((!$f_arrays_count)&&(!$f_dicts_count)) { $f_type_count--; }
				}
				elseif (($f_signature[$f_offset] == "a")||($f_signature[$f_offset] == "v")) { $f_return .= $f_signature[$f_offset]; }
				else
				{
					switch ($f_signature[$f_offset])
					{
					case "(":
					{
						$f_return .= $f_signature[$f_offset];
						$f_arrays_count++;

						break 1;
					}
					case ")":
					{
						$f_return .= $f_signature[$f_offset];
						$f_arrays_count--;

						if ((!$f_arrays_count)&&(!$f_dicts_count)) { $f_type_count--; }
						break 1;
					}
					case "{":
					{
						$f_return .= $f_signature[$f_offset];
						$f_dicts_count++;

						break 1;
					}
					case "}":
					{
						$f_return .= $f_signature[$f_offset];
						$f_dicts_count--;

						if ((!$f_arrays_count)&&(!$f_dicts_count)) { $f_type_count--; }
						break 1;
					}
					}
				}

				$f_offset++;
			}
		}

		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->getCompleteType ()- (#echo(__LINE__)#) found ".$f_return; }
		return $f_return;
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
	/*#ifndef(PHP4) */public /* #*/function getHeader ($f_field = "")
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->getHeader ()- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ($this->dbus_header != NULL)
		{
			if ($f_field)
			{
				if (is_numeric ($f_field))
				{
					foreach ($this->dbus_header[6] as $f_header_field)
					{
						if ((is_bool ($f_return))&&($f_header_field[0] === $f_field)) { $f_return = $f_header_field[1]; }
					}
				}
				else
				{
					switch ($f_field)
					{
					case "endian":
					{
						if ((isset ($this->dbus_header[0]))&&(($this->dbus_header[0] == 108)||($this->dbus_header[0] == 66))) { $f_return = chr ($this->dbus_header[0]); }
						break 1;
					}
					case "type":
					{
						if (isset ($this->dbus_header[1]))
						{
							switch ($this->dbus_header[1])
							{
							case 1:
							{
								$f_return = "method_call";
								break 1;
							}
							case 2:
							{
								$f_return = "method_return";
								break 1;
							}
							case 3:
							{
								$f_return = "error";
								break 1;
							}
							case 4:
							{
								$f_return = "signal";
								break 1;
							}
							default: { $f_return = "unknown"; }
							}
						}

						break 1;
					}
					case "flags":
					{
						if (isset ($this->dbus_header[2])) { $f_return = $this->dbus_header[2]; }
						break 1;
					}
					case "protocol":
					{
						if (isset ($this->dbus_header[3])) { $f_return = $this->dbus_header[3]; }
						break 1;
					}
					case "body_size":
					{
						if (isset ($this->dbus_header[4])) { $f_return = $this->dbus_header[4]; }
						break 1;
					}
					case "serial":
					{
						if (isset ($this->dbus_header[5])) { $f_return = $this->dbus_header[5]; }
						break 1;
					}
					}
				}
			}
			else { $f_return = $this->dbus_header; }
		}

		return $f_return;
	}

/**
	* Returns the marshaled content.
	*
	* @return mixed Byte string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function getRaw ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->getRaw ()- (#echo(__LINE__)#)"; }

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
	/*#ifndef(PHP4) */public /* #*/function getRawBody ()
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->getRawBody ()- (#echo(__LINE__)#)"; }
		$f_return = false;

		if (($this->dbus_header != NULL)&&($this->dbus_raw != NULL))
		{
			$f_body_start = 0;

			if (isset ($this->dbus_header[4]))
			{
				$f_body_start = (strlen ($this->dbus_raw)) - $this->dbus_header[4];

				if ($f_body_start >= 16)
				{
					if ($this->dbus_header[4]) { $f_return = substr ($this->dbus_raw,$f_body_start); }
					else { $f_return = ""; }
				}
			}
		}

		return $f_return;
	}

/**
	* Marshals a given array based on the signature corresponding to the D-BUS
	* 1.0 Specification. Please note that 64bit values will be used as they are.
	* You have to provide a string with a maximum of 8 bytes in little endian.
	*
	* @param  string $f_signature Data signature
	* @param  array $f_data Data array
	* @param  integer $f_position Position within the array - usually 0
	* @uses   directDBusMessage::getCompleteType()
	* @uses   directDBusMessage::marshalArray()
	* @uses   directDBusMessage::marshalSetBoundary()
	* @uses   directDBusMessage::marshalSetNle()
	* @uses   directDBusMessage::typeGetPositionPadding()
	* @return mixed Marshaled string on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function marshalArray ($f_signature,&$f_data,$f_position = 0)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->marshalArray ($f_signature,+f_data,$f_position)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ((is_string ($f_signature))&&(is_array ($f_data)))
		{
			$f_data_next = true;
			$f_data_position = 0;
			$f_return = "";
			$f_signature_length = strlen ($f_signature);
			$f_signature_position = 0;

			while (($f_signature_position < $f_signature_length)&&(is_string ($f_return)))
			{
				switch ($f_signature[$f_signature_position])
				{
				case "(":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,8);
					$f_sub_signature = $this->getCompleteType ($f_signature,$f_signature_position);

					if (($f_sub_signature)&&(is_array ($f_data[$f_data_position])))
					{
						$f_sub_signature = substr ($f_sub_signature,1,-1);
						$f_sub_raw = $this->marshalArray ($f_sub_signature,$f_data[$f_data_position],$f_position);

						if (is_bool ($f_sub_raw)) { $f_return = false; }
						else
						{
							$f_position += strlen ($f_sub_raw);
							$f_signature_position += 1 + strlen ($f_sub_signature);
							$f_return .= $f_sub_raw;
						}
					}
					else { $f_return = false; }

					break 1;
				}
				case ")":
				{
					$f_data_next = false;
					break 1;
				}
				case "{":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,8);
					$f_sub_signature = $this->getCompleteType ($f_signature,$f_signature_position);

					if (($f_sub_signature)&&(is_array ($f_data[$f_data_position])))
					{
						$f_array_element_raw = reset ($f_data[$f_data_position]);
						$f_array_element_raw = (is_array ($f_array_element_raw) ? array_merge (array (key ($f_data[$f_data_position])),(array_values ($f_data[$f_data_position]))) : array (key ($f_data[$f_data_position]),$f_array_element_raw));
						$f_sub_signature = substr ($f_sub_signature,1,-1);

						$f_sub_raw = $this->marshalArray ($f_sub_signature,$f_array_element_raw,$f_position);
					}
					else { $f_sub_raw = false; }

					if (is_bool ($f_sub_raw)) { $f_return = false; }
					else
					{
						$f_position += strlen ($f_sub_raw);
						$f_signature_position += 1 + strlen ($f_sub_signature);
						$f_return .= $f_sub_raw;
					}

					break 1;
				}
				case "}":
				{
					$f_data_next = false;
					break 1;
				}
				case "a":
				{
					$f_position += 4 + $this->marshalSetBoundary ($f_return,$f_position,4);
					$f_sub_signature = $this->getCompleteType ($f_signature,($f_signature_position + 1));

					if (($f_sub_signature)&&(is_array ($f_data[$f_data_position])))
					{
						reset ($f_data[$f_data_position]);

						$f_array_count = count ($f_data[$f_data_position]);
						$f_array_offset = $f_position;
						$f_array_position = 0;
						$f_sub_raw = "";

						while (($f_return)&&($f_array_position < $f_array_count))
						{
							$f_array_element_raw = array ($f_data[$f_data_position][$f_array_position]);
							$f_array_element_raw = $this->marshalArray ($f_sub_signature,$f_array_element_raw,$f_position);

							if ((is_string ($f_return))&&(!is_bool ($f_array_element_raw)))
							{
								$f_position += strlen ($f_array_element_raw);
								$f_sub_raw .= $f_array_element_raw;
							}
							else { $f_return = false; }

							$f_array_position++;
						}

						if (is_string ($f_return))
						{
							$f_signature_position += strlen ($f_sub_signature);
							$f_size = strlen ($f_sub_raw);

							$f_size -= ($this->typeGetPositionPadding ($f_sub_signature[0],$f_array_offset) - $f_array_offset);
							$f_return .= (pack ("V",$f_size)).$f_sub_raw;
						}
					}
					else { $f_return = false; }

					break 1;
				}
				case "b":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,4);

					if (!isset ($f_data[$f_data_position])) { $f_return = false; }
					elseif ($f_data[$f_data_position]) { $f_return .= "\x01\x00\x00\x00"; }
					else { $f_return .= "\x00\x00\x00\x00"; }

					$f_position += 4;
					break 1;
				}
				case "d":
				{
					$f_position += 8 + $this->marshalSetBoundary ($f_return,$f_position,8);

					if (strlen ("{$f_data[$f_data_position]}") < 9) { $f_return .= pack ("a8","{$f_data[$f_data_position]}"); }
					else { $f_return = false; }

					break 1;
				}
				case "g":
				{
					$f_size = strlen ($f_data[$f_data_position]);
				
					if ((isset ($f_data[$f_data_position]))&&(strlen ($f_data[$f_data_position]) < 256)) { $f_return .= pack ("Ca*x",$f_size,$f_data[$f_data_position]); }
					else { $f_return = false; }

					$f_position += 2 + $f_size;
					break 1;
				}
				case "i":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,4);

					if (isset ($f_data[$f_data_position])) { $f_return .= $this->marshalSetNle (pack ("L",$f_data[$f_data_position])); }
					else { $f_return = false; }

					$f_position += 4;
					break 1;
				}
				case "n":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,2);

					if (isset ($f_data[$f_data_position])) { $f_return .= $this->marshalSetNle (pack ("s",$f_data[$f_data_position])); }
					else { $f_return = false; }

					$f_position += 2;
					break 1;
				}
				case "o":
				case "s":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,4);

					if (isset ($f_data[$f_data_position]))
					{
						$f_size = strlen ($f_data[$f_data_position]);
						$f_return .= (pack ("V",$f_size)).$f_data[$f_data_position]."\x00";
					}
					else { $f_return = false; }

					$f_position += 5 + $f_size;
					break 1;
				}
				case "q":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,2);

					if (isset ($f_data[$f_data_position])) { $f_return .= pack ("v",$f_data[$f_data_position]); }
					else { $f_return = false; }

					$f_position += 2;
					break 1;
				}
				case "t":
				{
					$f_position += 8 + $this->marshalSetBoundary ($f_return,$f_position,8);

					if (strlen ("{$f_data[$f_data_position]}") < 9) { $f_return .= pack ("a8","{$f_data[$f_data_position]}"); }
					else { $f_return = false; }

					break 1;
				}
				case "u":
				{
					$f_position += $this->marshalSetBoundary ($f_return,$f_position,4);

					if (isset ($f_data[$f_data_position])) { $f_return .= pack ("V",$f_data[$f_data_position]); }
					else { $f_return = false; }

					$f_position += 4;
					break 1;
				}
				case "v":
				{
					if ((is_array ($f_data[$f_data_position]))&&(isset ($f_data[$f_data_position][0])))
					{
						$f_sub_signature = "g".$f_data[$f_data_position][0];
						$f_sub_raw = $this->marshalArray ($f_sub_signature,$f_data[$f_data_position],$f_position);

						if (is_bool ($f_sub_raw)) { $f_return = false; }
						else
						{
							$f_return .= $f_sub_raw;
							$f_position += strlen ($f_sub_raw);
						}
					}
					else { $f_return = false; }

					$f_data_next = false;
					break 1;
				}
				case "x":
				{
					$f_position += 8 + $this->marshalSetBoundary ($f_return,$f_position,8);

					if (strlen ("{$f_data[$f_data_position]}") < 9) { $f_return .= pack ("a8","{$f_data[$f_data_position]}"); }
					else { $f_return = false; }

					break 1;
				}
				case "y":
				{

					if ((is_string ($f_data[$f_data_position]))&&(strlen ($f_data[$f_data_position]) == 1)) { $f_return .= $f_data[$f_data_position]; }
					elseif ((is_numeric ($f_data[$f_data_position]))&&($f_data[$f_data_position] < 256)) { $f_return .= pack ("C",$f_data[$f_data_position]); }
					else { $f_return = false; }

					$f_position++;
					break 1;
				}
				default: { $f_return = false; }
				}

				$f_signature_position++;

				if ($f_data_next) { $f_data_position++; }
				else { $f_data_next = true; }
			}
		}
		else { $f_return = false; }

		return $f_return;
	}

/**
	* Fills the given data string with NUL bytes until the defined boundary has
	* been reached.
	*
	* @param  string &$f_data Data string
	* @param  integer $f_position Current position
	* @param  integer $f_boundary_spec Boundary to use
	* @return integer Number of bytes added
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function marshalSetBoundary (&$f_data,$f_position,$f_boundary_spec)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->marshalSetBoundary (+f_data,$f_position,$f_boundary_spec)- (#echo(__LINE__)#)"; }
		$f_return = 0;

		if (((is_string ($f_data)))&&($f_position > 1)&&($f_boundary_spec > 1))
		{
			$f_position = ($f_boundary_spec - ($f_position % $f_boundary_spec));

			if (($f_position)&&($f_position < $f_boundary_spec))
			{
				if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->marshalSetBoundary ()- (#echo(__LINE__)#) added $f_position NUL bytes to conform to the requested boundary"; }
				for ($f_i = 0;$f_i < $f_position;$f_i++) { $f_data .= "\x00"; }
				$f_return = $f_position;
			}
		}

		return $f_return;
	}

/**
	* Sends a message and waits for the response.
	*
	* @param  integer $f_data Byte value to byteswap (if we are on a native big
	*         endian system)
	* @return string Byte string
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function marshalSetNle ($f_data)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->marshalSetNle (+f_data)- (#echo(__LINE__)#)"; }

		if ((!$this->nle)&&(strlen ($f_data) > 1))
		{
			$f_bytes_inverted = array ();
			$f_position = 0;

			for ($f_i = (strlen ($f_data) - 1);$f_i > -1;$f_i--)
			{
				$f_bytes_inverted[$f_position] = $f_data[$f_i];
				$f_position++;
			}

			return implode ("",$f_bytes_inverted);
		}
		else { return $f_data; }
	}

/**
	* Sets the given header array and raw string for this message.
	*
	* @param  array $f_header Header array parsed with "unmarshal ()"
	* @param  string $f_raw Raw binary string
	* @param  boolean $f_overwrite True to ignore data already set
	* @return boolean True on success
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function set ($f_header,$f_raw,$f_overwrite = false)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->set (+f_header,$f_raw,+f_overwrite)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ((is_array ($f_header))&&(is_string ($f_raw))&&(($f_overwrite)||(($this->dbus_header == NULL)&&($this->dbus_raw == NULL))))
		{
			$f_return = true;
			$this->dbus_header = $f_header;
			$this->dbus_raw = $f_raw;
		}

		return $f_return;
	}

/**
	* Returns the defined boundary corresponding to the D-BUS 1.0 Specification.
	*
	* @param  string $f_type Type code (ASCII)
	* @return integer Defined boundary
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function typeGetPadding ($f_type)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->typeGetPadding ($f_type)- (#echo(__LINE__)#)"; }
		$f_return = 0;

		if (is_string ($f_type))
		{
			switch ($f_type)
			{
			case "(":
			{
				$f_return = 8;
				break 1;
			}
			case "{":
			{
				$f_return = 8;
				break 1;
			}
			case "a":
			{
				$f_return = 4;
				break 1;
			}
			case "b":
			{
				$f_return = 4;
				break 1;
			}
			case "d":
			{
				$f_return = 8;
				break 1;
			}
			case "i":
			{
				$f_return = 4;
				break 1;
			}
			case "n":
			{
				$f_return = 2;
				break 1;
			}
			case "o":
			{
				$f_return = 4;
				break 1;
			}
			case "q":
			{
				$f_return = 2;
				break 1;
			}
			case "s":
			{
				$f_return = 4;
				break 1;
			}
			case "t":
			{
				$f_return = 8;
				break 1;
			}
			case "u":
			{
				$f_return = 4;
				break 1;
			}
			case "x":
			{
				$f_return = 8;
				break 1;
			}
			}
		}

		return $f_return;
	}

/**
	* Calculates the new position to correspond to the given type boundary.
	*
	* @param  string $f_type Type code (ASCII)
	* @param  integer $f_position Current position in the byte string
	* @uses   directDBusMessage::typeGetPadding()
	* @return integer New position (Position of the needed boundary)
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function typeGetPositionPadding ($f_type,$f_position)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->typeGetPositionPadding ($f_type,$f_position)- (#echo(__LINE__)#)"; }

		$f_boundary_spec = $this->typeGetPadding ($f_type);
		$f_return = $f_position;

		if ($f_boundary_spec > 0)
		{
			$f_position = ($f_boundary_spec - ($f_position % $f_boundary_spec));
			if (($f_position)&&($f_position < $f_boundary_spec)) { $f_return += $f_position; }
		}

		return $f_return;
	}

/**
	* Unmarshals a given byte string based on the signature corresponding to the
	* D-BUS 1.0 Specification. Please note that 64bit values are returned as
	* byte strings.
	*
	* @param  mixed $f_le Position (integer) of the endian definition within the
	*         byte string or one of the defined endian ASCII codes (string)
	* @param  string $f_signature Data signature
	* @param  string &$f_data Byte data
	* @param  integer $f_position
	* @uses   directDBusMessage::getCompleteType()
	* @uses   directDBusMessage::typeGetPositionPadding()
	* @uses   directDBusMessage::unmarshal()
	* @uses   directDBusMessage::unmarshalGetBoundary()
	* @uses   directDBusMessage::unmarshalRead()
	* @uses   directDBusMessage::unmarshalSetLe()
	* @return mixed Data array on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */public /* #*/function unmarshal ($f_le,$f_signature,&$f_data,$f_position = -1)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->unmarshal (+f_le,$f_signature,+f_data,$f_position)- (#echo(__LINE__)#)"; }
		$f_return = false;

		if ((is_string ($f_signature))&&(is_string ($f_data)))
		{
			$f_position_element = false;
			$f_return = array ();
			$f_return_position = 0;
			$f_signature_length = strlen ($f_signature);
			$f_signature_position = 0;

			if ($f_position < 0) { $f_position = 0; }
			else { $f_position_element = true; }

			while (($f_signature_position < $f_signature_length)&&(is_array ($f_return)))
			{
				switch ($f_signature[$f_signature_position])
				{
				case "(":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,8);
					$f_sub_signature = $this->getCompleteType ($f_signature,$f_signature_position);

					if ($f_sub_signature)
					{
						$f_sub_signature = substr ($f_sub_signature,1,-1);
						$f_bytes_unpacked = $this->unmarshal ($f_le,$f_sub_signature,$f_data,$f_position);

						if ($f_bytes_unpacked)
						{
							if (isset ($f_bytes_unpacked['position']))
							{
								$f_position = $f_bytes_unpacked['position'];
								unset ($f_bytes_unpacked['position']);
							}

							$f_return[$f_return_position] = $f_bytes_unpacked;
						}
						else { $f_return = false; }
					}
					else { $f_return = false; }

					if ($f_return)
					{
						$f_return_position++;
						$f_signature_position += 1 + strlen ($f_sub_signature);
					}

					break 1;
				}
				case ")": break 1;
				case "{":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,8);
					$f_sub_signature = $this->getCompleteType ($f_signature,$f_signature_position);

					if (($f_sub_signature)&&(strlen ($this->getCompleteType ($f_sub_signature,1)) == 1)&&(strlen ($f_sub_signature) == (strlen ($this->getCompleteType ($f_sub_signature,2)) + 3)))
					{
						$f_sub_signature = substr ($f_sub_signature,1,-1);
						$f_bytes_unpacked = $this->unmarshal ($f_le,$f_sub_signature,$f_data,$f_position);

						if ($f_bytes_unpacked)
						{
							if (isset ($f_bytes_unpacked['position']))
							{
								$f_position = $f_bytes_unpacked['position'];
								unset ($f_bytes_unpacked['position']);
							}

							$f_return[$f_return_position] = array ($f_bytes_unpacked[0] => $f_bytes_unpacked[1]);
						}
						else { $f_return = false; }
					}
					else { $f_return = false; }

					if ($f_return)
					{
						$f_return_position++;
						$f_signature_position += 1 + strlen ($f_sub_signature);
					}

					break 1;
				}
				case "}": break 1;
				case "a":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,4);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,4);
 
					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_bytes_unpacked = (($f_le == "B") ? unpack ("N",$f_sub_read) : unpack ("V",$f_sub_read));
						$f_position += 4;
						$f_sub_signature = "";

						if ($f_bytes_unpacked) { $f_sub_signature = $this->getCompleteType ($f_signature,($f_signature_position + 1)); }

						if ($f_sub_signature)
						{
							$f_position = $this->typeGetPositionPadding ($f_sub_signature[0],$f_position);
							$f_return[$f_return_position] = array ();
							$f_sub_size = $f_bytes_unpacked[1];

							while (($f_return)&&($f_sub_size))
							{
								$f_bytes_unpacked = $this->unmarshal ($f_le,$f_sub_signature,$f_data,$f_position);

								if ($f_bytes_unpacked)
								{
									$f_sub_size -= ($f_bytes_unpacked['position'] - $f_position);
									$f_position = $f_bytes_unpacked['position'];
									unset ($f_bytes_unpacked['position']);

									$f_return[$f_return_position] = array_merge ($f_return[$f_return_position],$f_bytes_unpacked);
								}
								else { $f_return = false; }
							}

							if ($f_return)
							{
								$f_return_position++;
								$f_signature_position += strlen ($f_sub_signature);
							}
						}
						elseif ($f_bytes_unpacked) { $f_return = false; }
					}

					break 1;
				}
				case "b":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,4);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,4);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						if ($f_le == "B") { $f_return[$f_return_position] = (($f_sub_read[3] == "\x01") ? true : false); }
						else { $f_return[$f_return_position] = (($f_sub_read[0] == "\x01") ? true : false); }

						$f_position += 4;
						$f_return_position++;
					}

					break 1;
				}
				case "d":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,8);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,8);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return[$f_return_position] = $this->unmarshalSetLe ($f_le,"",$f_sub_read);
						$f_return_position++;
						$f_position += 8;
					}

					break 1;
				}
				case "g":
				{
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,1);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_bytes_unpacked = unpack ("C",$f_sub_read);
						$f_position++;

						if ($f_bytes_unpacked)
						{
							$f_return[$f_return_position] = $this->unmarshalRead ($f_data,$f_position,$f_bytes_unpacked[1]);
							$f_return_position++;
							$f_position += 1 + $f_bytes_unpacked[1];
						}
					}

					break 1;
				}
				case "i":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,4);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,4);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return[$f_return_position] = $this->unmarshalSetLe ($f_le,"L",$f_sub_read);
						$f_return_position++;
						$f_position += 4;
					}

					break 1;
				}
				case "n":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,2);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,2);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return[$f_return_position] = $this->unmarshalSetLe ($f_le,"s",$f_sub_read);
						$f_return_position++;
						$f_position += 2;
					}

					break 1;
				}
				case "o":
				case "s":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,4);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,4);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_bytes_unpacked = (($f_le == "B") ? unpack ("N",$f_sub_read) : unpack ("V",$f_sub_read));
						$f_position += 4;

						if ($f_bytes_unpacked)
						{
							$f_return[$f_return_position] = $this->unmarshalRead ($f_data,$f_position,$f_bytes_unpacked[1]);
							$f_return_position++;
							$f_position += 1 + $f_bytes_unpacked[1];
						}
					}

					break 1;
				}
				case "q":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,2);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,2);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return = (($f_le == "B") ? array_merge ($f_return,(unpack ("n",$f_sub_read))) : array_merge ($f_return,(unpack ("v",$f_sub_read))));
						$f_return_position++;
						$f_position += 4;
					}

					break 1;
				}
				case "t":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,8);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,8);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return[$f_return_position] = $this->unmarshalSetLe ($f_le,"",$f_sub_read);
						$f_return_position++;
						$f_position += 8;
					}

					break 1;
				}
				case "u":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,4);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,4);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return = (($f_le == "B") ? array_merge ($f_return,(unpack ("N",$f_sub_read))) : array_merge ($f_return,(unpack ("V",$f_sub_read))));
						$f_return_position++;
						$f_position += 4;
					}

					break 1;
				}
				case "v":
				{
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,1);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_bytes_unpacked = unpack ("C",$f_sub_read);
						$f_position++;

						if ($f_bytes_unpacked)
						{
							$f_sub_read = $this->unmarshalRead ($f_data,$f_position,$f_bytes_unpacked[1]);
							$f_position += 1 + $f_bytes_unpacked[1];
						}

						$f_return[$f_return_position] = array ($f_sub_read);
						$f_bytes_unpacked = $this->unmarshal ($f_le,$f_sub_read,$f_data,$f_position);

						if (is_bool ($f_bytes_unpacked)) { $f_return = false; }
						else
						{
							$f_position = $f_bytes_unpacked['position'];
							unset ($f_bytes_unpacked['position']);
							$f_return[$f_return_position] = array_merge ($f_return[$f_return_position],$f_bytes_unpacked);
						}

						$f_return_position++;
					}

					break 1;
				}
				case "x":
				{
					$f_position += $this->unmarshalGetBoundary ($f_position,8);
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,8);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						$f_return[$f_return_position] = $this->unmarshalSetLe ($f_le,"",$f_sub_read);
						$f_return_position++;
						$f_position += 8;
					}

					break 1;
				}
				case "y":
				{
					$f_sub_read = $this->unmarshalRead ($f_data,$f_position,1);

					if (is_bool ($f_sub_read)) { $f_return = false; }
					else
					{
						if ((is_int ($f_le))&&($f_le == $f_signature_position)) { $f_le = $f_sub_read; }
						$f_bytes_unpacked = unpack ("C",$f_sub_read);

						if ($f_bytes_unpacked)
						{
							$f_return[$f_return_position] = $f_bytes_unpacked[1];
							$f_return_position++;
							$f_position++;
						}
						else { $f_return = false; }
					}

					break 1;
				}
				default: { $f_return = false; }
				}

				$f_signature_position++;
			}

			if ((is_array ($f_return))&&($f_position_element)) { $f_return['position'] = $f_position; }
		}
		else { $f_return = false; }

		return $f_return;
	}

/**
	* Returns the number of bytes to seek to reach the next boundary.
	*
	* @param  integer $f_position Current position
	* @param  integer $f_boundary_spec Boundary to use
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalGetBoundary ($f_position,$f_boundary_spec)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->unmarshalGetBoundary ($f_position,$f_boundary_spec)- (#echo(__LINE__)#)"; }
		$f_return = 0;

		if ($f_boundary_spec > 0)
		{
			$f_position = ($f_boundary_spec - ($f_position % $f_boundary_spec));
			if (($f_position)&&($f_position < $f_boundary_spec)) { $f_return = $f_position; }
		}

		return $f_return;
	}

/**
	* Reads the given bytes from the data string.
	*
	* @param  string &$f_data Byte data
	* @param  integer $f_offset Offset
	* @param  integer $f_length Length
	* @return mixed Data read on success; false on error
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalRead (&$f_data,$f_offset,$f_length)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->unmarshalRead (+f_data,$f_offset,$f_length)- (#echo(__LINE__)#)"; }

		if ((is_string ($f_data))&&(is_numeric ($f_offset))&&(is_numeric ($f_length))&&(strlen ($f_data) >= $f_offset + $f_length)) { return substr ($f_data,$f_offset,$f_length); }
		else { return false; }
	}

/**
	* Sets the endian mode requested and byteswaps data when necessary.
	*
	* @param  string $f_le Endian ASCII code
	* @param  string $f_unpack_mode Mode to use for unpacking (or an empty
	*         string if it should only byteswap the data)
	* @param  string $f_data Byte data
	* @return Byte data (byteswapped if necessary)
	* @since  v0.1.00
*/
	/*#ifndef(PHP4) */protected /* #*/function unmarshalSetLe ($f_le,$f_unpack_mode,$f_data)
	{
		if ($this->debugging) { $this->debug[] = "directDBusMessage/#echo(__FILEPATH__)# -dbus->unmarshalSetLe ($f_le,$f_unpack_mode,+f_data)- (#echo(__LINE__)#)"; }
		$f_return = 0;

		if (is_string ($f_data))
		{
			if ((($f_le == "B")&&($this->nle))||(($f_le == "l")&&(!$this->nle)))
			{
				$f_bytes_inverted = array ();
				$f_position = 0;

				for ($f_i = (strlen ($f_data) - 1);$f_i > -1;$f_i--)
				{
					$f_bytes_inverted[$f_position] = $f_data[$f_i];
					$f_position++;
				}

				if ($f_unpack_mode)
				{
					$f_bytes_inverted = unpack ($f_unpack_mode,(implode ("",$f_bytes_inverted)));
					if ($f_bytes_inverted) { $f_return = $f_bytes_inverted[1]; }
				}
				else { $f_return = implode ("",$f_bytes_inverted); }
			}
			elseif ($f_unpack_mode)
			{
				$f_bytes_unpacked = unpack ($f_unpack_mode,$f_data);
				if ($f_bytes_unpacked) { $f_return = $f_bytes_unpacked[1]; }
			}
			else { $f_return = $f_data; }
		}

		return $f_return;
	}
}

/* -------------------------------------------------------------------------
Mark this class as the most up-to-date one
------------------------------------------------------------------------- */

define ("CLASS_directDBusMessage",true);
}

//j// EOF
?>