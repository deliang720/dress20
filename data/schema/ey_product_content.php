<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'aid' => 
  array (
    'name' => 'aid',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'content' => 
  array (
    'name' => 'content',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'update_time' => 
  array (
    'name' => 'update_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'fxrq' => 
  array (
    'name' => 'fxrq',
    'type' => 'enum(\'2019年\',\'2018年\',\'2017年\')',
    'notnull' => false,
    'default' => '2019年',
    'primary' => false,
    'autoinc' => false,
  ),
  'jiawei' => 
  array (
    'name' => 'jiawei',
    'type' => 'enum(\'0-1000\',\'1000-1699\',\'1700-2799\',\'2800-3500\',\'3500-10000\')',
    'notnull' => false,
    'default' => '0-1000',
    'primary' => false,
    'autoinc' => false,
  ),
  'yanse' => 
  array (
    'name' => 'yanse',
    'type' => 'enum(\'银色\',\'绿色\',\'黑色\',\'灰色\')',
    'notnull' => false,
    'default' => '银色',
    'primary' => false,
    'autoinc' => false,
  ),
  'xiangmuleixing' => 
  array (
    'name' => 'xiangmuleixing',
    'type' => 'enum(\'中餐类\',\'火锅类\',\'快餐类\',\'西餐厅\',\'精致小店\',\'面馆类\')',
    'notnull' => false,
    'default' => '中餐类',
    'primary' => false,
    'autoinc' => false,
  ),
  'mianji' => 
  array (
    'name' => 'mianji',
    'type' => 'enum(\'50㎡以下\',\'50㎡-100㎡\',\'100㎡-200㎡\',\'200㎡-300㎡\',\'300㎡-500㎡\',\'500㎡-1000㎡\',\'1000㎡以上\')',
    'notnull' => false,
    'default' => '50㎡以下',
    'primary' => false,
    'autoinc' => false,
  ),
  'fengge' => 
  array (
    'name' => 'fengge',
    'type' => 'enum(\'中式风格\',\'混搭风格\',\'日式风格\',\'工业风格\',\'简约风格\',\'现代风格\')',
    'notnull' => false,
    'default' => '中式风格',
    'primary' => false,
    'autoinc' => false,
  ),
);