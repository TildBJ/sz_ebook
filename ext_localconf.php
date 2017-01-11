<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Sunzinet.' . $_EXTKEY,
	'Pi1',
	array(
		'Ebook' => 'show',

	),
	// non-cacheable actions
	array(
		'Ebook' => '',

	)
);

?>