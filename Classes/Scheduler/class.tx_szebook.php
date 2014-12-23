<?php

/**
 * Description of the phpfile 'class.tx_szebook.php'
 *
 * @author Dennis Römmich <dennis@roemmich.eu>
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */


class tx_szebook extends tx_scheduler_Task {

	public function execute() {
		$configuration = array(
			'extensionName' => 'szEbook',
			'pluginName' => 'Pi1',
			'controller' => 'EbookTask',
			'action' => 'convert',
			'switchableControllerActions' => array(
				'EbookTask' => array(
					'1' => 'convert',
				),
			),
			'settings' => ' =< plugin.tx_szebook.settings',
			'persistence' => ' =< plugin.tx_szebook.persistence'
		);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['tx_szebook_cli']['controller'] = 'szEbook';
		$_GET['tx_szebook_cli']['action'] = 'convert';
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['szEbook']['modules']['Pi1']['controllers'] = array(
			'EbookTask' => array(
				'actions' => array(
					'convert'
				)
			)
		);

		$bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
		$output = $bootstrap->run('', $configuration);

		return true;
	}

}

?>