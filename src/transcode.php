<?php
namespace Instatom;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TranscodeServiceProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$app['transcode'] = $app->protect(function ($name) use ($app) {
			$default = $app['transcode.default_name'] ? $app['transcode.default_name'] : '';
			$name = $name ?: $default;

			return new Transcode($app->escape($name));
		});
	}

	public function boot(Application $app)
	{
	}
}

/**
* Transcode JSON to Atom
* construct with new Transcode($username);
* then get();
*/
class Transcode {
	private $username = '';
	public function __construct($username) {
		$this->username = $username;
	}
	/**
	 * fetch data from instagram feed JSON
	 *
	 * @return array
	 * @author gaspard
	 */
	public function get() {
		$empty = array(array(
			'link' => '',
			'created_time' => '',
			'thumbnail_url' => '',
			'caption' => '',
			'username' => '',
			'full_name' => '',
		));
		$media = file_get_contents("http://instagram.com/{$this->username}/media");
		if(strlen($media) < 1) return $empty;

		$json = json_decode($media);
		if(!isset($json->items) || count($json->items) == 0) return $empty;

		$results = array();
		foreach( $json->items as $item ) {
			$results[] = array(
				'link' => $item->link,
				'created_time' => $item->created_time,
				'thumbnail_url' => $item->images->thumbnail->url,
				'caption' => isset( $item->caption->text ) ? $item->caption->text : '',
				'username' => $item->user->username,
				'full_name' => $item->user->full_name,
			);
		}
		return $results;
	}
}
