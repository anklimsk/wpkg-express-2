<?php
/**
 * Employees Fixture
 */
class EmployeesFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'department_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'upn' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'last_name' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'first_name' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'middle_name' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'full_name' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'position' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'mail' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'manager' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'block' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'photo' => ['type' => 'binary', 'null' => true, 'default' => null],
		'birthday' => ['type' => 'date', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			'id_UNIQUE' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Records
 *
 * @var array
 */
	public $records = [
		[
			'id' => '1',
			'department_id' => '1',
			'upn' => 'a.sazonov@fabrikam.com',
			'last_name' => 'Сазонов',
			'first_name' => 'Александр',
			'middle_name' => 'Павлович',
			'full_name' => 'Сазонов А.П.',
			'position' => 'Водитель',
			'mail' => '',
			'manager' => 1,
			'block' => 0,
			'birthday' => '1965-05-08'
		],
		[
			'id' => '2',
			'department_id' => '1',
			'upn' => 'd.kostin@fabrikam.com',
			'last_name' => 'Костин',
			'first_name' => 'Дмитрий',
			'middle_name' => 'Иванович',
			'full_name' => 'Костин Д.И.',
			'position' => 'Ведущий инженер',
			'mail' => '',
			'manager' => 0,
			'block' => 0,
			'birthday' => '1977-04-24'
		],
		[
			'id' => '3',
			'department_id' => '2',
			'upn' => 'n.gerasimova@fabrikam.com',
			'last_name' => 'Герасимова',
			'first_name' => 'Наталия',
			'middle_name' => 'Михайловна',
			'full_name' => 'Герасимова Н.М.',
			'position' => 'Заведующий сектором',
			'mail' => 'nn.gerasimova@fabrikam.com',
			'manager' => 1,
			'block' => 1,
			'birthday' => '1969-09-15'
		],
		[
			'id' => '4',
			'department_id' => '3',
			'upn' => 'alexeev@fabrikam.com',
			'last_name' => 'Алексеев',
			'first_name' => 'Алексей',
			'middle_name' => 'Викторович',
			'full_name' => 'Алексеев А. В.',
			'position' => 'Инженер-электроник 1 категории',
			'mail' => 'a.alexeev@fabrikam.com',
			'manager' => 0,
			'block' => 0,
			'birthday' => '1983-05-20'
		],
		[
			'id' => '5',
			'department_id' => '4',
			'upn' => 'e.efimov@fabrikam.com',
			'last_name' => 'Ефимов',
			'first_name' => 'Евгений',
			'middle_name' => 'Юрьевич',
			'full_name' => 'Ефимов У.Ю.',
			'position' => 'Инженер 1 категории',
			'mail' => 'e.efimov@fabrikam.com',
			'manager' => 1,
			'block' => 0,
			'birthday' => '1986-12-07'
		],
		[
			'id' => '6',
			'department_id' => '4',
			'upn' => 't.egorov@fabrikam.com',
			'last_name' => 'Егоров',
			'first_name' => 'Тимофей',
			'middle_name' => 'Геннадьевич',
			'full_name' => '<b>Егоров Т.Г.</b>',
			'position' => 'Ведущий инженер',
			'mail' => 't.egorov@fabrikam.com',
			'manager' => 1,
			'block' => 0,
			'birthday' => '1996-07-27'
		],
	];
}
