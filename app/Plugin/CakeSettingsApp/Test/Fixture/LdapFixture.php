<?php

/**
 * LdapFixture
 *
 */
class LdapFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'ldap';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'],
		CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'objectCategory' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
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
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'Web.Admin',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => '',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => '',
			'objectCategory' => 'group',
		],
		[
			'id' => '2',
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=Web.Manager,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Web.Manager,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'Web.Manager',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => '',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => '',
			'objectCategory' => 'group',
		],
		[
			'id' => '3',
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=Web.Extend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Web.Extend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'Web.Extend',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => '',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => '',
			'objectCategory' => 'group',
		],
		[
			'id' => '4',
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=John Doe,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=John Doe,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'John Doe',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => 'Web.Admin',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => 'j.doe@mail.com',
			'objectCategory' => 'user',
		],
		[
			'id' => '5',
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=John Smith,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=John Smith,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'John Smith',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => 'Web.Admin',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => 'j.smith@mail.org',
			'objectCategory' => 'user',
		],
		[
			'id' => '6',
			CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME => 'CN=Some user,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Some user,OU=Adm,OU=Пользователи,DC=fabrikam,DC=com',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME => 'Some user',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF => 'Web.Admin',
			CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL => '',
			'objectCategory' => 'user',
		],
	];
}
