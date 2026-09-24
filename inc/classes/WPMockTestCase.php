<?php
/**
 * Shared WP_Mock base test case.
 *
 * @package BuiltNorth\WPMockStandIns
 */

namespace BuiltNorth\WPMockStandIns;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase as BaseTestCase;

/**
 * Base test case for WP_Mock suites.
 *
 * Handles the WP_Mock setUp/tearDown pairing and the few hook helpers that every
 * package's suite needs. Lives here rather than in each package because the
 * definition is identical everywhere it is used, and a per-package copy drifts:
 * a helper added or fixed in one copy silently does not exist in the others.
 *
 * A package needing extra helpers extends this rather than replacing it, so the
 * shared behaviour stays in one place.
 */
abstract class WPMockTestCase extends BaseTestCase {

	/**
	 * Set up before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	/**
	 * Tear down after each test.
	 */
	public function tearDown(): void {
		WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Make a filter return a fixed value regardless of its arguments.
	 *
	 * Note the argument matcher: WP_Mock's onFilter()->with() compares literal
	 * values, so this helper is only appropriate when the test does not care what
	 * the filter is passed. A test asserting on specific arguments should call
	 * WP_Mock::onFilter() directly with those literals.
	 *
	 * @param string $hook_name    Hook name.
	 * @param mixed  $return_value Value the filter should return.
	 */
	protected function mock_filter( $hook_name, $return_value ) {
		WP_Mock::onFilter( $hook_name )
			->with( Mockery::any() )
			->reply( $return_value );
	}

	/**
	 * Expect an action to have been registered.
	 *
	 * @param string $action        Action name.
	 * @param mixed  $callback      Callback.
	 * @param int    $priority      Priority.
	 * @param int    $accepted_args Number of accepted arguments.
	 */
	protected function expect_action_added( $action, $callback, $priority = 10, $accepted_args = 1 ) {
		WP_Mock::expectActionAdded( $action, $callback, $priority, $accepted_args );
	}

	/**
	 * Expect a filter to have been registered.
	 *
	 * @param string $filter        Filter name.
	 * @param mixed  $callback      Callback.
	 * @param int    $priority      Priority.
	 * @param int    $accepted_args Number of accepted arguments.
	 */
	protected function expect_filter_added( $filter, $callback, $priority = 10, $accepted_args = 1 ) {
		WP_Mock::expectFilterAdded( $filter, $callback, $priority, $accepted_args );
	}
}
