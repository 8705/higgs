<?php
App::uses('Divide', 'Model');

/**
 * Divide Test Case
 *
 */
class DivideTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.divide'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Divide = ClassRegistry::init('Divide');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Divide);

		parent::tearDown();
	}

}
