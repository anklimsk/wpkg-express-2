<?php
/**
 * Extended Test Case bake template
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command.Task
 */

// @codingStandardsIgnoreFile
echo "<?php\n";
?>
<?php foreach ($uses as $dependency) : ?>
App::uses('<?php echo $dependency[0]; ?>', '<?php echo $dependency[1]; ?>');
<?php endforeach; ?>

/**
 * <?php echo $fullClassName; ?> Test Case
 */
<?php if ($type === 'Controller') : ?>
class <?php echo $fullClassName; ?>Test extends AppControllerTestCase {
<?php else : ?>
class <?php echo $fullClassName; ?>Test extends AppCakeTestCase {
<?php endif; ?>
<?php if ($type === 'Controller') : ?>

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = '<?php echo $plugin . $className; ?>';
<?php endif; ?>

<?php if (!empty($fixtures)) : ?>
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'<?php echo join("',\n\t\t'", $fixtures); ?>'
	];

<?php endif; ?>
<?php if (!empty($construction)) : ?>
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
<?php echo $preConstruct ? "\t\t" . $preConstruct : ''; ?>
		$this-><?php echo '_targetObject = ' . $construction; ?>
<?php echo $postConstruct ? "\t\t" . $postConstruct : ''; ?>
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this-><?php echo '_targetObject'; ?>);

		parent::tearDown();
	}

<?php endif; ?>
<?php foreach ($methods as $method) : ?>
/**
 * test<?php echo Inflector::camelize($method); ?> method
 *
 * @return void
 */
	public function test<?php echo Inflector::camelize($method); ?>() {
		$this->markTestIncomplete('test<?php echo Inflector::camelize($method); ?> not implemented.'); // Comment or remove this string for implement test

<?php if ($type === 'Controller') : ?>
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/<?php echo (!empty($plugin) ? Inflector::underscore(rtrim($plugin, '.')) . '/' : '') . Inflector::underscore($className); ?>/<?php echo Inflector::underscore($method); ?>', $opt);
		$expected = [];
		$this->assertData($expected, $result);
<?php
	else :
		$reflectParams = [];
		if (class_exists($className) && method_exists($className, $method)) {
			$reflectMethod = new ReflectionMethod($className, $method);
			$reflectParams = $reflectMethod->getParameters();
		}
		if (!empty($reflectParams)) : ?>
		$params = [
			[
<?php
			foreach ($reflectParams as $i => $reflectParam) {
				echo ($i > 0 ? "\r\n" : '') . str_repeat(' ', 16) . 'null, // $' . $reflectParam->getName() . "\n";
			}
?>
			], // Params for step 1
		];
		$expected = [
			null, // Result of step 1
		];
		$this->runClassMethodGroup('<?php echo $method; ?>', $params, $expected);
<?php else : ?>
		$result = $this->_targetObject-><?php echo $method; ?>();
		$expected = null;
		$this->assertData($expected, $result);
<?php endif; ?>
<?php endif; ?>
	}

<?php endforeach; ?>
}
