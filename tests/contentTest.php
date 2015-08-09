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
		$this->assertEquals( $content, KilroyWasHere::init()->content() );
	}

	/**
	 * @depends testInstallContent
	 * @dataProvider newContentProvider
	 */
	public function testWriteContent( $content ) {
		$this->assertTrue( update_option( 'kilroywashere-content', $content ) );
		$this->assertEquals( $content, KilroyWasHere::init()->content() );
	}

	/**
	 * @depends testInstallContent
	 * @dataProvider newContentProvider
	 */
	public function testWriteComment( $content ) {
		$this->assertTrue( update_option( 'kilroywashere-content', $content ) );

		ob_start();
		KilroyWasHere::init()->html_comment();
		$html_content = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( "\n<!--\n" . $content . "\n-->\n\n", $html_content );
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
