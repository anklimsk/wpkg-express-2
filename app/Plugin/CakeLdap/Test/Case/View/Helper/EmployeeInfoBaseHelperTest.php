<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('EmployeeInfoBaseHelper', 'CakeLdap.View/Helper');

/**
 * EmployeeInfoBaseHelper Test Case
 */
class EmployeeInfoBaseHelperTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->_targetObject = new EmployeeInfoBaseHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testGetEmptyText method
 *
 * @return void
 */
	public function testGetEmptyText() {
		$result = $this->_targetObject->getEmptyText();
		$expected = __d('view_extension', '&lt;None&gt;');
		$this->assertData($expected, $result);
	}

/**
 * testGetInfo method
 *
 * @return void
 */
	public function testGetInfo() {
		$this->storeLocale();
		$this->skipIf(!$this->setEngLocale(), "The English locale isn't available.");

		$employee = [];
		$fieldsLabel = [];
		$fieldsConfig = [];
		$linkOpt = [];
		$returnTableRow = true;
		$result = $this->_targetObject->getInfo($employee, $fieldsLabel, $fieldsConfig, $linkOpt, $returnTableRow);
		$expected = [];
		$this->assertData($expected, $result);

		$employee = [
			'Employee' => [
				'id' => '8',
				'department_id' => '5',
				'manager_id' => 7,
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Голубев Е.В.',
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Е.В.',
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Голубев',
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Егор',
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заместитель начальника отдела, водитель',
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000005',
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => 'Гараж',
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.golubev@fabrikam.com',
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => hex2bin('ffd8ffe000104a46494600010200006400640000ffec00114475636b79000100040000003c0000ffee000e41646f62650064c000000001ffdb0084000604040405040605050609060506090b080606080b0c0a0a0b0a0a0c100c0c0c0c0c0c100c0e0f100f0e0c1313141413131c1b1b1b1c1f1f1f1f1f1f1f1f1f1f010707070d0c0d181010181a1511151a1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1fffc000110800c800c803011100021101031101ffc4006d0000020301010100000000000000000000020300010405060801010101010100000000000000000000000001020304100002020202020104020301000000000000010203110421053112224151130632426152141611010101010100000000000000000000000001021112ffda000c03010002110311003f00faa4019f80316d78651c1dd5cb03956ae4217ea05a8804a2012401201899417b202d4d00c534116e4500c8a068017102bd482b0032bf2074b53e8457675bc2036c40b020152f0061d95c328e26e2f2072ec8f254028804a0017a014e2004a49102a7b518fd4044fb182fa940aed21f701b5f6507f508d35ee465f503446c520a34b204f4029c0801c40282e40e8ea2e5115dad6f080d9102c0805480c7b2b86071b6e3e4d2399643900144035100bd4055ad4501cbdcdc8c13e40e06f7731867e441c4d8fd870dfc80cbff00a3e7f901a75ff63cb5f203b5a5dea935f22a3d06976319a5c8575e8b54901a52c811c0007002421c90743563ca0aec6bf8441ae2058100a90196f5c147276a3e4a39b6439080f4009440a97080e66feca845f2078fee3b4f5f6e483c5767dc4b2f922bcfec76936df24195f653cf901d4f6b34fc8e8ed75ddd4935f22f51ecba7ee73ebf228f69d66fa9a5c9477a8b149201e9640a7002e35f206dd68f822ba942e0835440b0201520335eb828e66cc7c9460b21c9501e804f4033eccbd62c83caf75b7eaa5c928f01dcedca4e58666d6b8f2bb6ac9b64eaf1cfb35ac7f4274e112d59afa0e9c54689a63a71b35559168bd38f4bd4ecce2d723a9c7bbe937dfc72cd4a9c7b4ebf63da2b92a3af5728a1bea01461c81ae88915d0a57041a22058100a9008b97051cebd14639c792a03d00a947080e5f612c4590788ef2d6fd8cd6a478edba6564d98b5b90a874f29fd0c75d264c7faf49afe23a7922cfd7a5fea5ea79677d0493fe23abe450e924bfa93abe5ae8eba55b5c0952e5e83abf68491b95cac7b6ea2d6d23a462bd36b3cc5151ad200a280d34a22b755e081e80b02014c04dbe0a305c8a32ca3c9503e800d91e00e276bc4592abc2f6eb32662b723954ea29cfc1cad76cc77b43a98c92e0c75d6475abe8e0d7f11d381b3f5f8bfea5eb3c65b3f5f8ffa93ad484cba28afea4eb5c64bfa951fa0952e4aa75bd27e0e99ae1a8f4bd4f183ac71b1eab4ff008a36c3725c00480d14915baa207a0201008c045a518ae4519dc792a2288016c7e20703b75f1666b51e1fb35f3673d3a6611a705ee8e55e8cc7a9eb54708c3a3bfaf18348b12b43a20d782b245bad0fb058c7751049f066b71c9dca63c90b1cb95494ce99ae3a8eb7591c347595e7d47a9d25f14748e75bd4782a2d20345488ad95903d01008046026c28c96a2a10d1444800b57c48af3fdb4731666b523c4f655bf7673d3b66326ba6a472aef98efe85cd60cb6ee6aecf08a71d085fc0ea70bb6e1d5e30dd7116473b625ed922f18bf1e646a56351d5ebeac3475cd79f51e974e3f14758e1637c63c1a604a003ab8915aab440e4040201180a99465b11421a2a22002c5c11638bd95598b335d32f23d8eb664f838e9e8cc73e1aed48e75da474756a92c11be3abae9a22f1b612782757893cb1d38cd656d9538cf3a1b22861afc963163a7a5461a3b65c371dcd586123ac79b4df08f06dcc6a210d8448a7c110310100804602ac28cd6142245452024a3c11639bbd5653315d32f35bfaff0027c1cb4f4e1861aebdbc1cabd11ba8d65f6237c6b8d58229914453630c84a2fc099a64b9ebafb100468592c674ddad563076cbcdbaead10c24758f3e9ae08d3998904322829b12030201008c054fc1465b5942195168026b822b1ed5794ccd6f2e06f53cb396a3d18ac0ab4a472b1e8cd6ca52c197586c9a41a07bf24e29f548335aa38c15954a2820635e59a91cf55b75ea3ae63cdbae8550c23ac70b4f8a2b23401c406c4808080402301567828c96b28437c94144062f0408be3944ab1c4de82e4e763ae6b933c291cabd38a65761cebd19a2959c07485fe4e435c3e9b033636d767018b07ed92c73a7551cb372386aba1456758f3eab6423c1b73a3c1595a01910a644808080402301361463b5960437c9414580d4c055cf820e2efbf262b79ae15f3c48e3a7a31410b4e75e9cd3bdf265db3512790e87d612b4c2780e5aa6c27c9a8e3aaddafcb475cbcdbaea6bae11d6385ad715c1595e0a2b01071414c44040402011809b1f0518ae668676f92a229051fe4204dd6f04a38fbd2ce4c558e16ce72ce55db359e32699cec77ce9a6a9e49c77ce9a61864e3a7b3e3c0e25d0931c72d68fab2d9a91c75a74b55783ac79f55d4a3c1d239d6b8b2a0b05130012401a20b020100a6026df05182e66a2334a5c9457b8152b0cf559aeb7825a39fb0fdb2628e75d4e4c56e565950d33363a4d2e10689c759b6aaf238dfb3e29b2712ece856d8e3174d9452cd48e574e8530c1a8e76b656f06e32d3099a0d4c0200900680804020152033dcf828e7deca324a5c97a8ac93aa1933368cf62667a334e19205ba72452e5ab9fa11650ff00c9fe071a9a321aafec4e35e8faf59fd8713d3555adfe0712d6cae9c158e9f1860a8345436122874665532322864580680b020100a6066bbc01cebca32b5c93a88913aa8e240b9d6409954414aa00d548025420a38ebafb03a7428443a742a4821b18805ea517ea05a45049941c6450e848a1d160101008053033dde00e7de883338f240518805e8414eb00255003f8882d5601a80071800c8c40351018a20128805e8053894560089143a050f8141a020100a9019ed0315ab93211ea012890128817ea04f402bf1a027e3405fe300940025000d400628804a205e00a68016808900c8228744a0ca20100a9008b7c1063b5102704049004901690169004a205fa944f502d44025100d4402480bc014c8058100891432250d89410100805301369063b1102882d0069017802d2009145e002480b48a09202f005904029802c0805a019128622820201008c04d8418ed010d9012640c401010024516802480248a2c09902016414c01604009007128622820201008c04d8418ae606572e480e1201d100f204c8048034012405945013204009014c01605004980c8b01912820201008fc008b5f0073f624418e53e481b54b20698be002c8113019100d004046515902640b401202002c0102260322c06c4a0c0807ffd9'),
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1950-12-14',
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '5524',
				'block' => false,
			],
			'Department' => [
				'id' => '5',
				'value' => 'АТО'
			],
			'Manager' => [
				'id' => 7,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
			],
			'Subordinate' => [
				[
					'SubordinateDb' => [
						'id' => '4',
						'parent_id' => '8',
						'lft' => '10',
						'rght' => '15',
					],
					'Employee' => [
						'id' => '4',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '5',
								'parent_id' => '4',
								'lft' => '11',
								'rght' => '14',
							],
							'Employee' => [
								'id' => '5',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
							],
							'children' => []
						]
					]
				]
			],
			'Othertelephone' => [
				[
					'id' => '1',
					'value' => '+375171000001',
					'employee_id' => '8',
				],
				[
					'id' => '2',
					'value' => '+375171000002',
					'employee_id' => '8',
				]
			],
			'Othermobile' => [
				[
					'id' => '3',
					'value' => '+375291000003',
					'employee_id' => '8',
				],
				[
					'id' => '4',
					'value' => '+375291000004',
					'employee_id' => '8',
				]
			]
		];
		$fieldsLabel = [
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
			'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
			'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
			'Department.value' => __d('cake_ldap_field_name', 'Department'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
			'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => __d('cake_ldap_field_name', 'GUID'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => __d('cake_ldap_field_name', 'Distinguished name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => __d('cake_ldap_field_name', 'Company name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => __d('cake_ldap_field_name', 'Initials'),
			'Employee.block' => __d('cake_ldap_field_name', 'Block'),
			'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subordinate')
		];
		$fieldsConfig = [
			'Employee.id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'Employee.department_id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'Employee.manager_id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
				'type' => 'guid',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
				'type' => 'string',
				'truncate' => true,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
				'type' => 'string',
				'truncate' => true,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
				'type' => CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
				'type' => 'photo',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
				'type' => 'string',
				'truncate' => true,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
				'type' => 'string',
				'truncate' => true,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
				'type' => 'date',
				'truncate' => false,
			],
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
				'type' => 'string',
				'truncate' => false,
			],
			'Employee.block' => [
				'type' => 'boolean',
				'truncate' => false,
			],
			'Department.value' => [
				'type' => 'string',
				'truncate' => true,
			],
			'Othertelephone.{n}.value' => [
				'type' => 'string',
				'truncate' => false,
			],
			'Othermobile.{n}.value' => [
				'type' => 'string',
				'truncate' => false,
			],
			'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'type' => 'manager',
				'truncate' => false,
			],
			'Subordinate.{n}' => [
				'type' => 'element',
				'truncate' => false,
			]
		];
		$linkOpt = [
			'target' => '_blank'
		];
		$returnTableRow = false;
		$result = $this->_targetObject->getInfo($employee, $fieldsLabel, $fieldsConfig, $linkOpt, $returnTableRow);
		$expected = [
			__d('cake_ldap_field_name', 'Full name') => 'Голубев Е.В.',
			__d('cake_ldap_field_name', 'Display name') => 'Голубев Е.В.',
			__d('cake_ldap_field_name', 'Surname') => 'Голубев',
			__d('cake_ldap_field_name', 'Given name') => 'Егор',
			__d('cake_ldap_field_name', 'Middle name') => 'Владимирович',
			__d('cake_ldap_field_name', 'E-mail') => '<a href="mailto:e.golubev@fabrikam.com">e.golubev@fabrikam.com</a>',
			__d('cake_ldap_field_name', 'SIP telephone') => '5524',
			__d('cake_ldap_field_name', 'Telephone') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Other telephone') => '<ul class="list-unstyled list-compact"><li>+375171000001</li><li>+375171000002</li></ul>',
			__d('cake_ldap_field_name', 'Mobile telephone') => '+375295000005',
			__d('cake_ldap_field_name', 'Other mobile telephone') => '<ul class="list-unstyled list-compact"><li>+375291000003</li><li>+375291000004</li></ul>',
			__d('cake_ldap_field_name', 'Office room') => 'Гараж',
			__d('cake_ldap_field_name', 'Department') => 'АТО',
			__d('cake_ldap_field_name', 'Subdivision') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Position') => 'Заместитель начальника отдела, водитель',
			__d('cake_ldap_field_name', 'Manager') => '<a href="/employees/view/7" target="_blank" data-toggle="modal-popover" class="popup-link" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Хвощинский В.В.</a> - Начальник отдела',
			__d('cake_ldap_field_name', 'Birthday') => '12/14/1950',
			__d('cake_ldap_field_name', 'Computer') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Employee ID') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'GUID') => '{9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf}',
			__d('cake_ldap_field_name', 'Distinguished name') => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
			__d('cake_ldap_field_name', 'Photo') => '<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive" style="max-width:200px;min-height:200px;max-height:200px;">',
			__d('cake_ldap_field_name', 'Company name') => 'ТестОрг',
			__d('cake_ldap_field_name', 'Initials') => 'Е.В.',
			__d('cake_ldap_field_name', 'Block') => __d('view_extension', 'No'),
			__d('cake_ldap_field_name', 'Subordinate') => "\r\n" . '<ul class="bonsai-treeview" id="employee-tree">' . "\r\n" .
				"\t" . '<li data-id="4" class="parent"><a href="/employees/view/4" data-toggle="modal-popover" class="popup-link" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Дементьева А.С.</a> - Инженер' . "\r\n" .
					"\t" . '<ul>' . "\r\n" .
						"\t\t" . '<li data-id="5"><a href="/employees/view/5" data-toggle="modal-popover" class="popup-link" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Матвеев Р.М.</a> - Водитель</li>' . "\r\n" .
					"\t" . '</ul>' . "\r\n" .
				"\t" . '</li>' . "\r\n" .
				'</ul>' . "\r\n"
		];
		$this->assertData($expected, $result);

		$fieldsLabel = [
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Displ. name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surn.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Giv. name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Mid. name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP tel.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Tel.'),
			'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other tel.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mob. tel.'),
			'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mob. tel.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office'),
			'Department.value' => __d('cake_ldap_field_name', 'Depart.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdiv.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Pos.'),
			'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manag.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthd.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Comp.'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Empl. ID'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => __d('cake_ldap_field_name', 'GUID'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => __d('cake_ldap_field_name', 'Disting. name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => __d('cake_ldap_field_name', 'Comp. name'),
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => __d('cake_ldap_field_name', 'Init.'),
			'Employee.block' => __d('cake_ldap_field_name', 'Block'),
		];
		$result = $this->_targetObject->getInfo($employee, $fieldsLabel, $fieldsConfig, $linkOpt, $returnTableRow);
		$expected = [
			__d('cake_ldap_field_name', 'Full name') => 'Голубев Е.В.',
			__d('cake_ldap_field_name', 'Displ. name') => 'Голубев Е.В.',
			__d('cake_ldap_field_name', 'Surn.') => 'Голубев',
			__d('cake_ldap_field_name', 'Giv. name') => 'Егор',
			__d('cake_ldap_field_name', 'Mid. name') => 'Владимирович',
			__d('cake_ldap_field_name', 'E-mail') => '<a href="mailto:e.golubev@fabrikam.com">e.golubev@fabrikam.com</a>',
			__d('cake_ldap_field_name', 'SIP tel.') => '5524',
			__d('cake_ldap_field_name', 'Tel.') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Other tel.') => '<ul class="list-unstyled list-compact"><li>+375171000001</li><li>+375171000002</li></ul>',
			__d('cake_ldap_field_name', 'Mob. tel.') => '+375295000005',
			__d('cake_ldap_field_name', 'Other mob. tel.') => '<ul class="list-unstyled list-compact"><li>+375291000003</li><li>+375291000004</li></ul>',
			__d('cake_ldap_field_name', 'Office') => 'Гараж',
			__d('cake_ldap_field_name', 'Depart.') => 'АТО',
			__d('cake_ldap_field_name', 'Subdiv.') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Pos.') => 'Заместитель начальника отдела, водитель',
			__d('cake_ldap_field_name', 'Manag.') => '<a href="/employees/view/7" target="_blank" data-toggle="modal-popover" class="popup-link" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Хвощинский В.В.</a> - Начальник отдела',
			__d('cake_ldap_field_name', 'Birthd.') => '12/14/1950',
			__d('cake_ldap_field_name', 'Comp.') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'Empl. ID') => __d('view_extension', '&lt;None&gt;'),
			__d('cake_ldap_field_name', 'GUID') => '{9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf}',
			__d('cake_ldap_field_name', 'Disting. name') => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
			__d('cake_ldap_field_name', 'Photo') => '<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive" style="max-width:200px;min-height:200px;max-height:200px;">',
			__d('cake_ldap_field_name', 'Comp. name') => 'ТестОрг',
			__d('cake_ldap_field_name', 'Init.') => 'Е.В.',
			__d('cake_ldap_field_name', 'Block') => __d('view_extension', 'No'),
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->getInfo($employee, $fieldsLabel, $fieldsConfig, $linkOpt, true);
		$expected = [
			'<a href="/employees/view/8" target="_blank" data-toggle="modal-popover" class="popup-link" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Голубев Е.В.</a>',
			'Голубев Е.В.',
			'Голубев',
			'Егор',
			'Владимирович',
			'<a href="mailto:e.golubev@fabrikam.com">e.golubev@fabrikam.com</a>',
			'5524',
			__d('view_extension', '&lt;None&gt;'),
			'<ul class="list-unstyled list-compact"><li>+375171000001</li><li>+375171000002</li></ul>',
			'+375295000005',
			'<ul class="list-unstyled list-compact"><li>+375291000003</li><li>+375291000004</li></ul>',
			'Гараж',
			'АТО',
			__d('view_extension', '&lt;None&gt;'),
			'<div class="collapse-text-expanded"><div class="collapse-text-truncated">Заместитель начальника<a href="#" role="button" data-toggle="collapse-text-expand" class="collapse-text-action-btn" title="' . __d('view_extension', 'Expand text') . '"><span class="fas fa-angle-double-right"></span></a></div><div class="collapse-text-original">Заместитель начальника отдела, водитель<a href="#" role="button" data-toggle="collapse-text-roll-up" class="collapse-text-action-btn" title="' . __d('view_extension', 'Roll up text') . '"><span class="fas fa-angle-double-left"></span></a></div></div>',
			'<a href="/employees/view/7" target="_blank" data-toggle="modal-popover" class="popup-link" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Хвощинский В.В.</a> - Начальник отдела',
			[
				'12/14/1950',
				[
					'class' => 'text-center'
				]
			],
			__d('view_extension', '&lt;None&gt;'),
			__d('view_extension', '&lt;None&gt;'),
			'{9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf}',
			'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
			'<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive center-block" style="max-width:64px;min-height:64px;max-height:64px;">',
			'ТестОрг',
			'Е.В.',
			[
				__d('view_extension', 'No'),
				[
					'class' => 'text-center'
				]
			]
		];
		$this->assertData($expected, $result);
		$this->restoreLocale();
	}

/**
 * testGetPhotoImage method
 *
 * @return void
 */
	public function testGetPhotoImage() {
		$params = [
			[
				null, // $data
				true, // $returnTableRow
				0 // $size
			],
			[
				'SomeValue', // $data
				false, // $returnTableRow
				0 // $size
			],
			[
				hex2bin('ffd8ffe000104a46494600010200006400640000ffec00114475636b79000100040000003c0000ffee000e41646f62650064c000000001ffdb0084000604040405040605050609060506090b080606080b0c0a0a0b0a0a0c100c0c0c0c0c0c100c0e0f100f0e0c1313141413131c1b1b1b1c1f1f1f1f1f1f1f1f1f1f010707070d0c0d181010181a1511151a1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1fffc000110800c800c803011100021101031101ffc4006d0000020301010100000000000000000000020300010405060801010101010100000000000000000000000001020304100002020202020104020301000000000000010203110421053112224151130632426152141611010101010100000000000000000000000001021112ffda000c03010002110311003f00faa4019f80316d78651c1dd5cb03956ae4217ea05a8804a2012401201899417b202d4d00c534116e4500c8a068017102bd482b0032bf2074b53e8457675bc2036c40b020152f0061d95c328e26e2f2072ec8f254028804a0017a014e2004a49102a7b518fd4044fb182fa940aed21f701b5f6507f508d35ee465f503446c520a34b204f4029c0801c40282e40e8ea2e5115dad6f080d9102c0805480c7b2b86071b6e3e4d2399643900144035100bd4055ad4501cbdcdc8c13e40e06f7731867e441c4d8fd870dfc80cbff00a3e7f901a75ff63cb5f203b5a5dea935f22a3d06976319a5c8575e8b54901a52c811c0007002421c90743563ca0aec6bf8441ae2058100a90196f5c147276a3e4a39b6439080f4009440a97080e66feca845f2078fee3b4f5f6e483c5767dc4b2f922bcfec76936df24195f653cf901d4f6b34fc8e8ed75ddd4935f22f51ecba7ee73ebf228f69d66fa9a5c9477a8b149201e9640a7002e35f206dd68f822ba942e0835440b0201520335eb828e66cc7c9460b21c9501e804f4033eccbd62c83caf75b7eaa5c928f01dcedca4e58666d6b8f2bb6ac9b64eaf1cfb35ac7f4274e112d59afa0e9c54689a63a71b35559168bd38f4bd4ecce2d723a9c7bbe937dfc72cd4a9c7b4ebf63da2b92a3af5728a1bea01461c81ae88915d0a57041a22058100a9008b97051cebd14639c792a03d00a947080e5f612c4590788ef2d6fd8cd6a478edba6564d98b5b90a874f29fd0c75d264c7faf49afe23a7922cfd7a5fea5ea79677d0493fe23abe450e924bfa93abe5ae8eba55b5c0952e5e83abf68491b95cac7b6ea2d6d23a462bd36b3cc5151ad200a280d34a22b755e081e80b02014c04dbe0a305c8a32ca3c9503e800d91e00e276bc4592abc2f6eb32662b723954ea29cfc1cad76cc77b43a98c92e0c75d6475abe8e0d7f11d381b3f5f8bfea5eb3c65b3f5f8ffa93ad484cba28afea4eb5c64bfa951fa0952e4aa75bd27e0e99ae1a8f4bd4f183ac71b1eab4ff008a36c3725c00480d14915baa207a0201008c045a518ae4519dc792a2288016c7e20703b75f1666b51e1fb35f3673d3a6611a705ee8e55e8cc7a9eb54708c3a3bfaf18348b12b43a20d782b245bad0fb058c7751049f066b71c9dca63c90b1cb95494ce99ae3a8eb7591c347595e7d47a9d25f14748e75bd4782a2d20345488ad95903d01008046026c28c96a2a10d1444800b57c48af3fdb4731666b523c4f655bf7673d3b66326ba6a472aef98efe85cd60cb6ee6aecf08a71d085fc0ea70bb6e1d5e30dd7116473b625ed922f18bf1e646a56351d5ebeac3475cd79f51e974e3f14758e1637c63c1a604a003ab8915aab440e4040201180a99465b11421a2a22002c5c11638bd95598b335d32f23d8eb664f838e9e8cc73e1aed48e75da474756a92c11be3abae9a22f1b612782757893cb1d38cd656d9538cf3a1b22861afc963163a7a5461a3b65c371dcd586123ac79b4df08f06dcc6a210d8448a7c110310100804602ac28cd6142245452024a3c11639bbd5653315d32f35bfaff0027c1cb4f4e1861aebdbc1cabd11ba8d65f6237c6b8d58229914453630c84a2fc099a64b9ebafb100468592c674ddad563076cbcdbaead10c24758f3e9ae08d3998904322829b12030201008c054fc1465b5942195168026b822b1ed5794ccd6f2e06f53cb396a3d18ac0ab4a472b1e8cd6ca52c197586c9a41a07bf24e29f548335aa38c15954a2820635e59a91cf55b75ea3ae63cdbae8550c23ac70b4f8a2b23401c406c4808080402301567828c96b28437c94144062f0408be3944ab1c4de82e4e763ae6b933c291cabd38a65761cebd19a2959c07485fe4e435c3e9b033636d767018b07ed92c73a7551cb372386aba1456758f3eab6423c1b73a3c1595a01910a644808080402301361463b5960437c9414580d4c055cf820e2efbf262b79ae15f3c48e3a7a31410b4e75e9cd3bdf265db3512790e87d612b4c2780e5aa6c27c9a8e3aaddafcb475cbcdbaea6bae11d6385ad715c1595e0a2b01071414c44040402011809b1f0518ae668676f92a229051fe4204dd6f04a38fbd2ce4c558e16ce72ce55db359e32699cec77ce9a6a9e49c77ce9a61864e3a7b3e3c0e25d0931c72d68fab2d9a91c75a74b55783ac79f55d4a3c1d239d6b8b2a0b05130012401a20b020100a6026df05182e66a2334a5c9457b8152b0cf559aeb7825a39fb0fdb2628e75d4e4c56e565950d33363a4d2e10689c759b6aaf238dfb3e29b2712ece856d8e3174d9452cd48e574e8530c1a8e76b656f06e32d3099a0d4c0200900680804020152033dcf828e7deca324a5c97a8ac93aa1933368cf62667a334e19205ba72452e5ab9fa11650ff00c9fe071a9a321aafec4e35e8faf59fd8713d3555adfe0712d6cae9c158e9f1860a8345436122874665532322864580680b020100a6066bbc01cebca32b5c93a88913aa8e240b9d6409954414aa00d548025420a38ebafb03a7428443a742a4821b18805ea517ea05a45049941c6450e848a1d160101008053033dde00e7de883338f240518805e8414eb00255003f8882d5601a80071800c8c40351018a20128805e8053894560089143a050f8141a020100a9019ed0315ab93211ea012890128817ea04f402bf1a027e3405fe300940025000d400628804a205e00a68016808900c8228744a0ca20100a9008b7c1063b5102704049004901690169004a205fa944f502d44025100d4402480bc014c8058100891432250d89410100805301369063b1102882d0069017802d2009145e002480b48a09202f005904029802c0805a019128622820201008c04d8418ed010d9012640c401010024516802480248a2c09902016414c01604009007128622820201008c04d8418ae606572e480e1201d100f204c8048034012405945013204009014c01605004980c8b01912820201008fc008b5f0073f624418e53e481b54b20698be002c8113019100d004046515902640b401202002c0102260322c06c4a0c0807ffd9'), // $data
				true, // $returnTableRow
				28 // $size
			],
			[
				hex2bin('ffd8ffe000104a46494600010200006400640000ffec00114475636b79000100040000003c0000ffee000e41646f62650064c000000001ffdb0084000604040405040605050609060506090b080606080b0c0a0a0b0a0a0c100c0c0c0c0c0c100c0e0f100f0e0c1313141413131c1b1b1b1c1f1f1f1f1f1f1f1f1f1f010707070d0c0d181010181a1511151a1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1f1fffc000110800c800c803011100021101031101ffc4006d0000020301010100000000000000000000020300010405060801010101010100000000000000000000000001020304100002020202020104020301000000000000010203110421053112224151130632426152141611010101010100000000000000000000000001021112ffda000c03010002110311003f00faa4019f80316d78651c1dd5cb03956ae4217ea05a8804a2012401201899417b202d4d00c534116e4500c8a068017102bd482b0032bf2074b53e8457675bc2036c40b020152f0061d95c328e26e2f2072ec8f254028804a0017a014e2004a49102a7b518fd4044fb182fa940aed21f701b5f6507f508d35ee465f503446c520a34b204f4029c0801c40282e40e8ea2e5115dad6f080d9102c0805480c7b2b86071b6e3e4d2399643900144035100bd4055ad4501cbdcdc8c13e40e06f7731867e441c4d8fd870dfc80cbff00a3e7f901a75ff63cb5f203b5a5dea935f22a3d06976319a5c8575e8b54901a52c811c0007002421c90743563ca0aec6bf8441ae2058100a90196f5c147276a3e4a39b6439080f4009440a97080e66feca845f2078fee3b4f5f6e483c5767dc4b2f922bcfec76936df24195f653cf901d4f6b34fc8e8ed75ddd4935f22f51ecba7ee73ebf228f69d66fa9a5c9477a8b149201e9640a7002e35f206dd68f822ba942e0835440b0201520335eb828e66cc7c9460b21c9501e804f4033eccbd62c83caf75b7eaa5c928f01dcedca4e58666d6b8f2bb6ac9b64eaf1cfb35ac7f4274e112d59afa0e9c54689a63a71b35559168bd38f4bd4ecce2d723a9c7bbe937dfc72cd4a9c7b4ebf63da2b92a3af5728a1bea01461c81ae88915d0a57041a22058100a9008b97051cebd14639c792a03d00a947080e5f612c4590788ef2d6fd8cd6a478edba6564d98b5b90a874f29fd0c75d264c7faf49afe23a7922cfd7a5fea5ea79677d0493fe23abe450e924bfa93abe5ae8eba55b5c0952e5e83abf68491b95cac7b6ea2d6d23a462bd36b3cc5151ad200a280d34a22b755e081e80b02014c04dbe0a305c8a32ca3c9503e800d91e00e276bc4592abc2f6eb32662b723954ea29cfc1cad76cc77b43a98c92e0c75d6475abe8e0d7f11d381b3f5f8bfea5eb3c65b3f5f8ffa93ad484cba28afea4eb5c64bfa951fa0952e4aa75bd27e0e99ae1a8f4bd4f183ac71b1eab4ff008a36c3725c00480d14915baa207a0201008c045a518ae4519dc792a2288016c7e20703b75f1666b51e1fb35f3673d3a6611a705ee8e55e8cc7a9eb54708c3a3bfaf18348b12b43a20d782b245bad0fb058c7751049f066b71c9dca63c90b1cb95494ce99ae3a8eb7591c347595e7d47a9d25f14748e75bd4782a2d20345488ad95903d01008046026c28c96a2a10d1444800b57c48af3fdb4731666b523c4f655bf7673d3b66326ba6a472aef98efe85cd60cb6ee6aecf08a71d085fc0ea70bb6e1d5e30dd7116473b625ed922f18bf1e646a56351d5ebeac3475cd79f51e974e3f14758e1637c63c1a604a003ab8915aab440e4040201180a99465b11421a2a22002c5c11638bd95598b335d32f23d8eb664f838e9e8cc73e1aed48e75da474756a92c11be3abae9a22f1b612782757893cb1d38cd656d9538cf3a1b22861afc963163a7a5461a3b65c371dcd586123ac79b4df08f06dcc6a210d8448a7c110310100804602ac28cd6142245452024a3c11639bbd5653315d32f35bfaff0027c1cb4f4e1861aebdbc1cabd11ba8d65f6237c6b8d58229914453630c84a2fc099a64b9ebafb100468592c674ddad563076cbcdbaead10c24758f3e9ae08d3998904322829b12030201008c054fc1465b5942195168026b822b1ed5794ccd6f2e06f53cb396a3d18ac0ab4a472b1e8cd6ca52c197586c9a41a07bf24e29f548335aa38c15954a2820635e59a91cf55b75ea3ae63cdbae8550c23ac70b4f8a2b23401c406c4808080402301567828c96b28437c94144062f0408be3944ab1c4de82e4e763ae6b933c291cabd38a65761cebd19a2959c07485fe4e435c3e9b033636d767018b07ed92c73a7551cb372386aba1456758f3eab6423c1b73a3c1595a01910a644808080402301361463b5960437c9414580d4c055cf820e2efbf262b79ae15f3c48e3a7a31410b4e75e9cd3bdf265db3512790e87d612b4c2780e5aa6c27c9a8e3aaddafcb475cbcdbaea6bae11d6385ad715c1595e0a2b01071414c44040402011809b1f0518ae668676f92a229051fe4204dd6f04a38fbd2ce4c558e16ce72ce55db359e32699cec77ce9a6a9e49c77ce9a61864e3a7b3e3c0e25d0931c72d68fab2d9a91c75a74b55783ac79f55d4a3c1d239d6b8b2a0b05130012401a20b020100a6026df05182e66a2334a5c9457b8152b0cf559aeb7825a39fb0fdb2628e75d4e4c56e565950d33363a4d2e10689c759b6aaf238dfb3e29b2712ece856d8e3174d9452cd48e574e8530c1a8e76b656f06e32d3099a0d4c0200900680804020152033dcf828e7deca324a5c97a8ac93aa1933368cf62667a334e19205ba72452e5ab9fa11650ff00c9fe071a9a321aafec4e35e8faf59fd8713d3555adfe0712d6cae9c158e9f1860a8345436122874665532322864580680b020100a6066bbc01cebca32b5c93a88913aa8e240b9d6409954414aa00d548025420a38ebafb03a7428443a742a4821b18805ea517ea05a45049941c6450e848a1d160101008053033dde00e7de883338f240518805e8414eb00255003f8882d5601a80071800c8c40351018a20128805e8053894560089143a050f8141a020100a9019ed0315ab93211ea012890128817ea04f402bf1a027e3405fe300940025000d400628804a205e00a68016808900c8228744a0ca20100a9008b7c1063b5102704049004901690169004a205fa944f502d44025100d4402480bc014c8058100891432250d89410100805301369063b1102882d0069017802d2009145e002480b48a09202f005904029802c0805a019128622820201008c04d8418ed010d9012640c401010024516802480248a2c09902016414c01604009007128622820201008c04d8418ae606572e480e1201d100f204c8048034012405945013204009014c01605004980c8b01912820201008fc008b5f0073f624418e53e481b54b20698be002c8113019100d004046515902640b401202002c0102260322c06c4a0c0807ffd9'), // $data
				false, // $returnTableRow
				32 // $size
			]
		];
		$expected = [
			'<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive center-block" style="max-width:' . CAKE_LDAP_PHOTO_SIZE_SMALL . 'px;min-height:' . CAKE_LDAP_PHOTO_SIZE_SMALL . 'px;max-height:' . CAKE_LDAP_PHOTO_SIZE_SMALL . 'px;">',
			'<img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCABgAGADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD+/iiiigAooooAKK+If2tP+Cgv7Jv7FkOmj9oH4saZ4X13XLc3/h/wPpFpq/inx7r9uGaze6svCPhuxvb8aUHAB1jVlj0lWQqX3hBX4g+L/wDg5y+EGk/EC7tvBX7Pvj/4g/C9bYC21+51XSvAfiq11EXhP2r+ydYbWLPVtIvLUKWD/wDCP6ppS71kjctuUA/qcor+afwl/wAHOP7JeqX0Np4y+A/7Q/gu2mX5tT0+18BeMLe27Za1svF1jeEjtiz5Gcg1+yf7Kv7df7L/AO2fod9rX7PnxZ0LxncaNBa3PiPwjP8Aa9E8e+FRdOojHiLwfrEdlrNnbtnA1REfSSzKqOxGSAfYtFFFABRRRQAUUUUAFfK/7W37XXwZ/Yt+Dur/ABq+OGs6lp3hPTr+10Ow07QtMOr+KPFGu6rvGn+H/DeiiS2e/wBVnZCUjVwoWOQFhJhT9UV/NB/wc66PeXX7JnwC1uOWY2WkftH2Vvd2JIW3ubjVPht8QLOyuGHTKMZCDng4wBhiQD+Ur9uP9pq8/bB/au+M37RclnqGj2HjvxJaf8IroOr3dndan4e8DaFpGn+GvB+j3xs2Nmc2lp9uZrRiSfvfN0+U6Mk9TmigAr1r4B/G/wCIf7N/xg+Hvxs+Fev6loPjH4feJLTV7SWwuzajVtK+241jw1rC/cvPDfiOzzo2s6MQoBGCMdfJaKAP9Oz9hL9sXwL+3T+zt4W+PvgTSrvw3DrF3quheKPB2oX1tfap4K8ZaBdiy1jw5fXtotqmpqzfZNY0rVYsx6tpOrRTYjLhD9qV/Nb/AMGydnqsP7H/AMcr+6lWbS7v9pnVv7Hh+VfszWnw5+H9lqtyScHLEjkjJwRzuGP6UqACiiigAooooAK/AD/g45TQ7z/gnnLHfajptnr2nfHL4Zaz4d0++u7O21LVfst3qOkawNFtL3/S74Wekay15dixBBABdlBBP7/1/M3/AMHN3w0v/Ef7LvwI+KdrB51p8LPjldaXrrZGLbS/iP4Q1LRre8IxkAaxpOjWrE5BMgB9aAP4o6KKKACiiigD+8L/AINzY9DsP+CesNnZato17r2o/HP4nax4k06wurO61LSrm8vNOs9KXV7Gyzd6Q97pGiLeWgv1UgMdpIDg/v8AV/M5/wAGyfwwvvDn7LXx4+Kl1AIbX4p/Gu00fSGyM3WlfC/whp2i3F2R1AbWNX1m0VgqjKYyccf0x0AFFFFABRRRQAV84/tUfs2eBf2uPgB8Rv2ePiJNf2Xhj4kaKun3OraP9kGveHdSs72z1fR/EWjvdxXdqmraNrFnZ30T7MOVKBgJNx+jqZJ9xvpQB/lE/GP4Z618F/i38TvhF4igWHXfhZ4+8WeANQM9n9lW9ufDGr6lpC3gswOftv2P7bwMkr90YATziv6Gv+DjP9lzS/hB+1f4Q/aB8Ny2sOlftN+G9Xv/ABJoMGRdWvxG+HFnoGi6xq43DP2LxL4b1fw6o6AayNYZTgqT/PLQAV6P8HPhnrfxo+L/AMK/hB4dK/298TvH/hPwBo5g/wBKWyuvE+r6fpH2z7GRj/Q/tZvOQOGGc4584r+hv/g3I/Zf0v4v/tZ+MP2gfEc1rLpf7MnhyzuPDejEf6VdfEb4kWmvaNo+rgBgfsvhzw3ZeL93Ya1rfzZBoA/sV/ZT/Zt8B/sj/s//AAz/AGdfhwb+Xwl8M9EOkWmp6x9kbW/EOo3F5eavrPiLV2tkVZNW1nV7281eVthZWkCHAQEfSFMRNv8AQen/ANf/AD3p9ABRRRQAUUUUAFMk+430pJJBGMn/AD/n8K/Lz9tz/grH+yR+xZo/iPTfEfxI0Hxr8arKwvBoPwU8E3beJvFVzrn2O8OlW3i46Mz2XgfSTfx41W98S3dp5MRZ03lVoA/mI/4L9ftv/BL9rT4x/B/wR8E9W1nxJH+zofiz4W8d6xfeH9Z0PSj4x13WfCFldaRo51kWN7qx0hfB94by6bRSqncqlvvH+f2t7xR4o1jxt4m8V+MvEc3neI/GHiPxF4w16WDH2W71/wAT6xqOr6xeE4G/beXoJ5wMEDnIrBoAK/fr/ggP+298D/2TfjF8YPA/xp1jWfDiftFH4TeF/AWsadoOs65oA8Y6Fq/iGybSNY/sYXt3pP8AbFz4wtP7F1Z7EhySW46fgLW94X8U6x4I8T+FvG3hyY2fiPwh4j8P+MNCmuD/AMeWveGNY07V9G2g9CbyzY44J5B2gEgA/wBZ6NxIufwIqSvy0/Yj/wCCsn7JP7aOj+G9M8N/EPQvBHxqvNPtBr/wa8c3TeG/FUGumzQ6tZ+EP7YZLTxtpS3p2WL+HLu43xRh3w3A/UhH3f0Pr/8AX/z2oAfRRRQBVleKNCTJ5QiHnnjPyjJORzkeoFfj3+2L/wAFtv2Lv2TDqvh2x8X/APC/vivZD7Ofhx8GLu01+20m+O3daeL/AB1vfwh4aAO75RJrHiMAFR4bKkOf5KP+CgX/AAVj/ab/AG0fG/jXw5pXj3xJ8Mv2bzrWraR4R+E/hDVrzwvbav4Xtb37JZ3vxJ1iwUaz4w1nWgw1e9s9Xu/+EbKjA0UAZH5SGMxZ8qHyov8Anhakjv6bVbPcncB6jmgD9g/2wv8Agt5+23+1h/a/h3RvFNt+zx8K9RbyP+EF+EN3e22v6rphBDWfiP4kMbPxJq+c7LwaN/wjGifOw2YLAfj3/wAtJpussw+0TT8g3l7yRkZPBzn5cgk7cjJpfLf+83/kP/4mjy3/ALzf+Of/ABNABRR5b/3m/wDHP/iaPLf+83/jn/xNABRR5b/3m/8AHP8A4mjy3/vN/wCOf/E0AGR5kMsXE0Vz9pil6XlldtliT2UDjjIJIJILYr9g/wBjz/gt5+2z+ygdI8Paz4n/AOGifhbp5s4W8CfF66vLnX9M0wKyEeEviRm88SaUFBKqdbPibRgMYJZVA/Hzy3/vN/45/wDE0eXJ/eb8dmOOmcAHH0IOOhFAH+hj+x1/wW6/Yq/a0Gk+HL/xfL8Avi3qS/Z0+HPxhvLPQrXVr4bibTwh45yvhHxIAdv7syaN4jYfL/wjqAA1+xKzRFEkEgMcoHlnswIyMHqeP/r81/kiPGZYhFLD50X/ADyuScj7oxkcjIDZIyeQo2jIH6uf8E/f+Cs37Tn7GfjvwVoOs+O/EnxN/Zu/trS7Dxj8JfGN5e+J7fSfC91eCz1jWPhxe3n/ABOvDetaOD/a40j/AJFzVs5XSM5IAP/Z" class="img-thumbnail img-responsive" style="max-width:' . CAKE_LDAP_PHOTO_SIZE_LARGE . 'px;min-height:' . CAKE_LDAP_PHOTO_SIZE_LARGE . 'px;max-height:' . CAKE_LDAP_PHOTO_SIZE_LARGE . 'px;">',
			'<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive center-block" style="max-width:28px;min-height:28px;max-height:28px;">',
			'<img src="data:image/jpeg;base64,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" class="img-thumbnail img-responsive" style="max-width:32px;min-height:32px;max-height:32px;">',
		];

		$this->runClassMethodGroup('getPhotoImage', $params, $expected);
	}

/**
 * testGetFullName method
 *
 * @return void
 */
	public function testGetFullName() {
		$params = [
			[
				[], // $fullData
			],
			[
				['BADkey' => 'val'], // $fullData
			],
			[
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'голубев',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'голубев Е.В.',
				], // $fullData
			],
			[
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'голубев',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'егор',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'владимирович',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'голубев Е.В.',
				], // $fullData
			],
		];
		$expected = [
			__d('view_extension', '&lt;None&gt;'),
			__d('view_extension', '&lt;None&gt;'),
			'Голубев Е.В.',
			'Голубев Егор Владимирович'
		];

		$this->runClassMethodGroup('getFullName', $params, $expected);
	}
}
