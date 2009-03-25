<?php

class pkFeed
{
	/**
	 * Takes the url/routing rule of a feed and adds it to the request attributes to be read by
	 * include_feeds() (see feedHelper.php), which is called in the layout. Allows for dynamic
	 * inclusion of rel tags for RSS.
	 * http://spindrop.us/2006/07/04/dynamic-linking-to-syndication-feeds-with-symfony/
	 *
	 * @author Dave Dash
	 */
	public static function addFeed($request, $feed)
	{
		$request->setAttribute($feed, $feed, 'helper/asset/auto/feed');
	}
}
