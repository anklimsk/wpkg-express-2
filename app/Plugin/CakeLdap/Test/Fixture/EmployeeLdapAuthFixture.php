<?php
/**
 * EmployeeLdapAuthFixture
 *
 */
class EmployeeLdapAuthFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'employee_ldap_auth';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'],
		CAKE_LDAP_LDAP_DISTINGUISHED_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_LDAP_LDAP_ATTRIBUTE_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
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
			CAKE_LDAP_LDAP_DISTINGUISHED_NAME => 'CN=Моисеева Л.Б.,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf',
			CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'lmoiseeva@fabrikam.com',
			CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF => 'CN=Web.PbAdmin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Моисеева Л.Б.',
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Моисеева Л.Б.',
		],
		[
			'id' => '2',
			CAKE_LDAP_LDAP_DISTINGUISHED_NAME => 'CN=Белова Н.М.,OU=20-02,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '81817f32-44a7-4b4a-8eff-b837ba387077',
			CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'nbelova@fabrikam.com',
			CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Белова Н.М.',
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Белова Н.М.',
		],
		[
			'id' => '3',
			CAKE_LDAP_LDAP_DISTINGUISHED_NAME => 'CN=Кириллов А.М.,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'b3ec524a-69d0-4fce-b9c2-3b59956cfa25',
			CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'akirillov@fabrikam.com',
			CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Кириллов А.М.',
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Кириллов А.М.',
		]
	];
}
