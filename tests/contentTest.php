<?php
/**
 * Test reading and writing of content
 */
class contentTest extends WP_UnitTestCase {
	/**
	 * @dataProvider defaultContentProvider
	 */
	public function testInstallContent( $content ) {
		KilroyWasHere::install();
		$this->assertEquals( $content, KilroyWasHere::init()->get() );
	}

	/**
	 * @depends testInstallContent
	 * @dataProvider newContentProvider
	 */
	public function testWriteContent( $content ) {
		$this->assertTrue( update_option( 'kilroywashere-content', $content ) );
		$this->assertEquals( $content, KilroyWasHere::init()->get() );
	}

	public function defaultContentProvider() {
		return array(
			array( "`     ,,,\n     (o o)\n--ooO-(_)-Ooo---" )
		);
	}

	public function newContentProvider() {
		return array(
			array( "TEST  TEST  TEST" )
		);
	}
}
