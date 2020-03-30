<?php
  
function schema() {
  
  $schema['folders'] = array(
    'description' => 'Table for folders',
    'fields' => array(
      'fid' => array(
//        'description' => 'Уникальный ID раздела',
        'type' => 'int',
        'length' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE,
        'not null' => TRUE,
      ),
      'title' => array(
        'description' => 'Name of folder',
        'type' => 'varchar',
				'length' => 36,
				'not null' => TRUE,
      ),
			'created' => array(
				'description' => 'Creation date',
				'type' => 'int',
        'length' => 11,
				'not null' => TRUE,
			),
			'modifed' => array(
				'description' => 'Modification date',
				'type' => 'int',
        'length' => 11,
				'default' => 0,
			),
			'description' => array(
				'description' => 'Description of folder',
				'type' => 'varchar',
        'length' => 256,
				'default' => '',
			),
			'parent' => array(
				'description' => 'Parent folder for current folder',
				'type' => 'int',
        'length' => 11,
				'not null' => TRUE,
				'default' => 0,
			),
    ),
		'primary key' => array('fid'),
		'indexes' => array(
			'fid' => array('fid'),
			'parent' => array('parent'),
		),
  );
  
  
  $schema['folders_items'] = array(
    'description' => 'Table for items of folder',
    'fields' => array(
      'iid' => array(
        'description' => 'Уникальный ID элемента',
        'type' => 'int',
        'length' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE,
        'not null' => TRUE,
      ),
			'fid' => array(
				'description' => 'Parent folder for current item',
				'type' => 'int',
        'length' => 11,
				'not null' => TRUE,
			),
      'title' => array(
        'description' => 'Name of item',
        'type' => 'varchar',
				'length' => 36,
				'not null' => TRUE,
      ),
			'created' => array(
				'description' => 'Creation date',
				'type' => 'int',
        'length' => 11,
				'not null' => TRUE,
			),
			'modifed' => array(
				'description' => 'Modification date',
				'type' => 'int',
        'length' => 11,
				'default' => 0,
			),
			'type' => array(
				'description' => 'Type of item (news, article, feedback, comment)',
				'type' => 'varchar',
        'length' => 16,
				'default' => '',
			),
			'data' => array(
				'description' => 'More a data of the item',
				'type' => 'BLOB',
			),
    ),
		'primary key' => array('iid'),
		'indexes' => array(
			'iid' => array('iid'),
			'fid' => array('fid'),
		),
  );
  
  
  return $schema;
}
  
?>