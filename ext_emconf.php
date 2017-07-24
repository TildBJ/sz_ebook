<?php

//
// Extension Manager/Repository config file for ext: "sz_ebook"
//
// Auto generated by Extension Builder 2014-06-02
//
// Manual updates:
// Only the data in the array - anything else is removed by next write.
// "version" and "dependencies" must not be touched!
//

$EM_CONF[$_EXTKEY] = array(
    'title' => 'eBook',
    'description' =>
        'Shows eBooks in your browser, 
        using turn.js and pdf.js, 
        based on Extbase and Fluid. Works with Tablets and Smartphones.',
    'category' => 'plugin',
    'author' => 'Dennis Römmich',
    'author_email' => 'dennis@roemmich.eu',
    'author_company' => 'sunzinet AG',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '1',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '2.0.4',
    'constraints' => array(
        'depends' => array(
            'extbase' => '1.3',
            'fluid' => '1.3',
            'typo3' => '6.2.0-8.7.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    'autoload' => array(
        'psr-4' => array('TildBJ\\SzEbook\\' => 'Classes')
    ),
);
