<?php
/**
 * Class EypdTest
 *
 * @package Early_Years
 */

/**
 * Sample test case.
 */
class EypdTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}


	/**
	 *
	 */
	function test_maybe_url() {
		$no_protocol = 'url.net';
		$random      = 'random string';

		$result_protocol = eypd_maybe_url( $no_protocol );
		$result_random   = eypd_maybe_url( $random );

		$this->assertFalse( $result_random );
		$this->assertStringMatchesFormat( '//url.net', $result_protocol );

	}
}
