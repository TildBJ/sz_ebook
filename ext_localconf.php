<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Ebook' => 'show',

	),
	// non-cacheable actions
	array(
		'Ebook' => '',

	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Tx_SzEbook_Command_ConvertCommandController';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_szebook'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'Convert Task for sz_ebook',
	'description'      => 'Konvertiert PDF\'s zu turnJs'
);

?>