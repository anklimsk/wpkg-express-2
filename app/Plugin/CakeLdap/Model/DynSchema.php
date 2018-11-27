<?php
/**
 * This file is the model file of the application. Used for
 *  update schema of application, based on the` LdapFields` configuration
 *  from the plugin configuration file.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeSchema', 'Model');
App::uses('ConnectionManager', 'Model');

/**
 * The model is used for update schema of application.
 *
 * @package plugin.Model
 */
class DynSchema extends CakeLdapAppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Update schema after create or update (delete unused tables of fields)
 *
 * @param array $event Schema object properties.
 * @param string $connection Database connection name.
 *  See CakeSchema::after()
 * @return bool Return success.
 */
	public function updateSchema($event = null, $connection = null) {
		if (empty($event) || !is_array($event)) {
			return false;
		}

		if (empty($connection)) {
			$connection = 'default';
		}

		$errors = Hash::get($event, 'errors');
		if (!empty($errors)) {
			return false;
		}

		$table = Hash::get($event, 'create');
		if (empty($table)) {
			$table = Hash::get($event, 'update');
		}
		if (empty($table)) {
			return false;
		}

		$name = 'CakeLdap';
		$plugin = 'CakeLdap';
		$Schema = new CakeSchema(compact('connection', 'plugin', 'name'));
		$oldSchema = $Schema->load();
		if ($oldSchema === false) {
			return false;
		}

		$newSchema = $Schema->load();
		if (!isset($newSchema->tables[$table]) || empty($newSchema->tables[$table])) {
			return false;
		}

		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$fieldsDb = $modelConfigSync->getListFieldsDb();
		$fieldsLdap = $modelConfigSync->getListFieldsLdap();
		if (empty($fieldsDb)) {
			return false;
		}

		$fieldsInt = [
			'indexes',
			'tableParameters'
		];

		$newSchema->tables = array_intersect_key($newSchema->tables, [$table => null]);
		switch ($table) {
			case 'departments':
				if (in_array('department_id', $fieldsDb)) {
					$deleteDepartments = $modelConfigSync->getFlagDeleteDepartments();
					if (!$deleteDepartments) {
						return true;
					}

					unset($newSchema->tables[$table]['block']);
				} else {
					unset($newSchema->tables[$table]);
				}
				break;
			/*
			case 'memberofs':
				if (in_array(CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF, $fieldsLdap))
					return true;

				unset($newSchema->tables[$table]);
			break;
			*/
			case 'othertelephones':
				if (in_array(CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER, $fieldsLdap)) {
					return true;
				}

				unset($newSchema->tables[$table]);
				break;
			case 'othermobiles':
				if (in_array(CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER, $fieldsLdap)) {
					return true;
				}

				unset($newSchema->tables[$table]);
				break;
			case 'subordinates':
				if (in_array('manager_id', $fieldsDb)) {
					return true;
				}

				unset($newSchema->tables[$table]);
				break;
			case 'employees':
				$fieldsSchema = array_merge($fieldsDb, $fieldsInt);
				$newSchema->tables[$table] = array_intersect_key($newSchema->tables[$table], array_flip($fieldsSchema));
				break;
			default:
				return false;
		}

		$compareNew = $Schema->compare($oldSchema, $newSchema);
		$compareOld = $Schema->compare($newSchema, $oldSchema);
		$compareDataNew = Hash::get($compareNew, $table);
		$compareDataOld = Hash::get($compareOld, $table);

		$contents = null;
		$ds = ConnectionManager::getDataSource($Schema->connection);
		$ds->cacheSources = false;
		if (!empty($compareDataNew)) {
			$contents = [$table => $ds->alterSchema([$table => $compareDataNew], $table)];
		} elseif (!empty($compareDataOld)) {
			$contents = [$table => $ds->dropSchema($oldSchema, $table)];
		}
		if (empty($contents)) {
			return true;
		}

		$result = true;
		foreach ($contents as $table => $sql) {
			if (empty($sql)) {
				continue;
			}

			try {
				$ds->execute($sql);
			} catch (PDOException $e) {
				$result = false;
			}
		}

		return $result;
	}
}
