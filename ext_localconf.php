<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Sunzinet.' . $_EXTKEY,
	'Pi1',
	array(
		'Ebook' => 'list',

	),
	// non-cacheable actions
	array(
		'Ebook' => '',

	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
	\Sunzinet\SzEbook\Command\ConvertCommandController::class;