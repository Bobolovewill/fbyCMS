<?php

	error_reporting (E_ALL);
	ini_set('display_errors', 'On');
	/*****************************************************************************************************************************
	 * Warning: Cannot modify header information - headers already sent by ...
	 * header() must be called before any actual output is sent, either by normal HTML tags, blank lines in a file, or from PHP.
	 * This turns on PHP's output buffering feature. In PHP when you output something (do an echo or print) if has to send
	 * the HTTP headers at that time. If you turn on output buffering you can output in the script but PHP doesn't have to
	 * send the headers until the buffer is flushed. If you turn it on and don't turn it off PHP will automatically flush
	 * everything in the buffer after the script finishes running. There really is no harm in just turning it on in almost
	 * all cases and could give you a small performance increase under some configurations.
	 *****************************************************************************************************************************/
	ob_start();
	session_start();
	require_once 'helpers/directories.php';
	require_once 'helpers/Connection.php';
	require_once 'helpers/functions.php';
	require_once 'helpers/Hash.php';
	require_once 'helpers/language.php';
	require_once 'helpers/Cookie.php';
	require_once 'helpers/setLang.php';
	require_once 'helpers/dbError.php';
	require_once 'helpers/DateFormatter.php';
