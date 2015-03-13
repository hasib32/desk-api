<?php namespace Hasan\DeskApi;

use Hasan\DeskApi\Providers\Laravel\LaravelSession;
use Illuminate\Support\ServiceProvider;

class DeskApiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('hasan/desk-api');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['DeskApi'] = $this->app->share(function($app){
			$configurations = \Config::get('desk-api::config');
			$client = new \GuzzleHttp\Client(['base_url'  => $configurations['credentials']['url']]);
			$storage = new LaravelSession();
            $authentication = "Hasan\\DeskApi\\Authentications\\";
            $authentication .= $configurations['authentication'];
			return new $authentication($client, $storage, $configurations);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
