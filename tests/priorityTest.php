<?php
/**
 * Test reading and setting the priority
 */
class priorityTest extends WP_UnitTestCase {
	/**
	 * @dataProvider defaultPriorityProvider
	 */
	public function testInstallPriority( $priority ) {
		KilroyWasHere::install();
		$this->assertEquals( $priority, KilroyWasHere::init()->priority() );
	}

	/**
	 * @depends testInstallPriority
	 * @dataProvider newPriorityProvider
	 */
	public function testWritePriority( $priority ) {
		$this->assertTrue( update_option( 'kilroywashere-priority', $priority ) );
		$this->assertEquals( $priority, KilroyWasHere::init()->priority() );
	}

	public function defaultPriorityProvider() {
		return array(
			array( 99 )
		);
	}

	public function newPriorityProvider() {
		return array(
			array( 20 )
		);
	}
}
