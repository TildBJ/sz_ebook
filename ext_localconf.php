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

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['szEbookFileToFalMigration'] =
    'Tx_SzEbook_Migration_FileToFalMigration';
