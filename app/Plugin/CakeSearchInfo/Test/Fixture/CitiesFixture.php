<?php
/**
 * Cities Fixture
 */
class CitiesFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'cities';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'pcode_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'zip' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'population' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'length' => 10],
		'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
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
			'pcode_id' => '1',
			'name' => 'Минск',
			'zip' => '220000',
			'population' => 1964200,
			'description' => 'Минск (белор. Мінск) — столица Республики Беларусь, административный центр Минской области и Минского района, в состав которых не входит, поскольку является самостоятельной административно-территориальной единицей с особым (столичным) статусом.'
		],
		[
			'id' => '2',
			'pcode_id' => '2',
			'name' => 'Гродно',
			'zip' => '230000',
			'population' => 365610,
			'description' => 'Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.'
		],
		[
			'id' => '3',
			'pcode_id' => '3',
			'name' => 'Витебск',
			'zip' => '210000',
			'population' => 376226,
			'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.'
		],
		[
			'id' => '4',
			'pcode_id' => '4',
			'name' => 'Брест',
			'zip' => '224000',
			'population' => 340141,
			'description' => 'Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.'
		],
	];
}
