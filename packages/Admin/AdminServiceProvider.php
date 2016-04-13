<?php
namespace Packages\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
	
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// if (! $this->app->routesAreCached()) {
			require __DIR__.'/routes.php';
		// }
		$this->loadViewsFrom(__DIR__.'/views', 'admin');
	}
	
	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register()
	{
// 		$this->app->singleton('Riak\Contracts\Connection', function ($app) {
// 			return new Connection(config('riak'));
// 		});
	}
	
}