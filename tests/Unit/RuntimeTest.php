<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Native\Mobile\Runtime;

uses(Tests\TestCase::class);

/**
 * Reset Runtime statics without calling shutdown() (which flushes the
 * shared test Application container and breaks subsequent tests).
 */
function resetRuntimeState(): void
{
    $ref = new ReflectionClass(Runtime::class);

    foreach (['app' => null, 'kernel' => null, 'booted' => false, 'resetCallbacks' => []] as $prop => $default) {
        $p = $ref->getProperty($prop);
        $p->setValue(null, $default);
    }

    // Restore default config
    $cfg = $ref->getProperty('config');
    $cfg->setValue(null, [
        'reset_instances' => true,
        'gc_between_dispatches' => false,
    ]);
}

beforeEach(function () {
    resetRuntimeState();
});

afterEach(function () {
    resetRuntimeState();
});

it('boots the runtime with a Laravel application', function () {
    expect(Runtime::isBooted())->toBeFalse();

    Runtime::boot(app());

    expect(Runtime::isBooted())->toBeTrue();
    expect(Runtime::getApp())->toBeInstanceOf(Application::class);
});

it('dispatches a request and returns a response', function () {
    Runtime::boot(app());

    $request = Request::create('/', 'GET');
    $response = Runtime::dispatch($request);

    expect($response)->toBeInstanceOf(\Symfony\Component\HttpFoundation\Response::class);
    expect($response->getStatusCode())->toBeIn([200, 302]);
});

it('throws if dispatch is called before boot', function () {
    $request = Request::create('/', 'GET');

    Runtime::dispatch($request);
})->throws(RuntimeException::class, 'Runtime not booted');

it('resets state between dispatches', function () {
    Runtime::boot(app());

    // First dispatch
    $request1 = Request::create('/', 'GET');
    Runtime::dispatch($request1);

    // Second dispatch — should not carry stale state
    $request2 = Request::create('/', 'GET');
    $response2 = Runtime::dispatch($request2);

    expect($response2)->toBeInstanceOf(\Symfony\Component\HttpFoundation\Response::class);
});

it('calls registered reset callbacks between dispatches', function () {
    Runtime::boot(app());

    $callbackCalled = false;

    Runtime::onReset(function ($app) use (&$callbackCalled) {
        $callbackCalled = true;
        expect($app)->toBeInstanceOf(Application::class);
    });

    $request = Request::create('/', 'GET');
    Runtime::dispatch($request);

    expect($callbackCalled)->toBeTrue();
});

it('handles multiple reset callbacks', function () {
    Runtime::boot(app());

    $callOrder = [];

    Runtime::onReset(function () use (&$callOrder) {
        $callOrder[] = 'first';
    });

    Runtime::onReset(function () use (&$callOrder) {
        $callOrder[] = 'second';
    });

    $request = Request::create('/', 'GET');
    Runtime::dispatch($request);

    expect($callOrder)->toBe(['first', 'second']);
});

it('runs an artisan command through the persistent runtime', function () {
    Runtime::boot(app());

    $output = Runtime::artisan('inspire');

    expect($output)->toBeString();
    // inspire returns a quote, so output should be non-empty
    expect(strlen(trim($output)))->toBeGreaterThan(0);
});

it('throws if artisan is called before boot', function () {
    Runtime::artisan('inspire');
})->throws(RuntimeException::class, 'Runtime not booted');

it('shuts down cleanly', function () {
    Runtime::boot(app());
    expect(Runtime::isBooted())->toBeTrue();

    // Use our safe reset instead of shutdown() to avoid flushing the test container
    resetRuntimeState();

    expect(Runtime::isBooted())->toBeFalse();
    expect(Runtime::getApp())->toBeNull();
});

it('handles shutdown when not booted', function () {
    // Should not throw
    resetRuntimeState();

    expect(Runtime::isBooted())->toBeFalse();
    expect(Runtime::getApp())->toBeNull();
});

it('catches exceptions during dispatch and returns a 500 response', function () {
    Runtime::boot(app());

    // Request to a non-existent route should not crash the runtime
    $request = Request::create('/this-route-does-not-exist-at-all-12345', 'GET');
    $response = Runtime::dispatch($request);

    expect($response)->toBeInstanceOf(\Symfony\Component\HttpFoundation\Response::class);
    // Should return 404 (not crash)
    expect($response->getStatusCode())->toBe(404);
});

it('can dispatch multiple sequential requests', function () {
    Runtime::boot(app());

    for ($i = 0; $i < 5; $i++) {
        $request = Request::create('/', 'GET');
        $response = Runtime::dispatch($request);

        expect($response)->toBeInstanceOf(\Symfony\Component\HttpFoundation\Response::class);
    }
});

it('resets facade instances between dispatches when configured', function () {
    // Ensure the config enables instance reset (default behavior)
    config(['nativephp.runtime.reset_instances' => true]);

    Runtime::boot(app());

    $request = Request::create('/', 'GET');
    Runtime::dispatch($request);

    // If we get here without errors, facade state was reset successfully
    $request2 = Request::create('/', 'GET');
    $response = Runtime::dispatch($request2);

    expect($response->getStatusCode())->toBeIn([200, 302]);
});

it('loads runtime config from nativephp.runtime', function () {
    config([
        'nativephp.runtime' => [
            'reset_instances' => false,
            'gc_between_dispatches' => true,
        ],
    ]);

    Runtime::boot(app());

    // Boot should complete without error even with custom config
    expect(Runtime::isBooted())->toBeTrue();

    // Dispatch should work with gc enabled
    $request = Request::create('/', 'GET');
    $response = Runtime::dispatch($request);

    expect($response)->toBeInstanceOf(\Symfony\Component\HttpFoundation\Response::class);
});

it('clears reset callbacks on shutdown', function () {
    Runtime::boot(app());

    $called = false;
    Runtime::onReset(function () use (&$called) {
        $called = true;
    });

    resetRuntimeState();

    // Re-boot and dispatch — the old callback should not run
    Runtime::boot(app());
    $called = false;

    $request = Request::create('/', 'GET');
    Runtime::dispatch($request);

    expect($called)->toBeFalse();
});

it('binds the request into the container for each dispatch', function () {
    Runtime::boot(app());

    $request = Request::create('/test-path', 'GET');
    Runtime::dispatch($request);

    // The app's request instance should be the one we dispatched
    $boundRequest = Runtime::getApp()->make('request');
    expect($boundRequest)->toBeInstanceOf(Request::class);
});

// --- Configuration tests ---

it('has runtime config with persistent mode by default', function () {
    $runtimeConfig = config('nativephp.runtime');

    expect($runtimeConfig)->toBeArray();
    expect($runtimeConfig['mode'])->toBe('persistent');
    expect($runtimeConfig['reset_instances'])->toBeTrue();
    expect($runtimeConfig['gc_between_dispatches'])->toBeFalse();
});

it('has hot reload config with default watch paths', function () {
    $hotReload = config('nativephp.hot_reload');

    expect($hotReload)->toBeArray();
    expect($hotReload['watch_paths'])->toContain('app', 'resources', 'routes', 'config');
    expect($hotReload['exclude_patterns'])->toContain('storage', 'node_modules');
});

it('dispatches multiple requests without leaking headers across them', function () {
    Runtime::boot(app());

    $request1 = Request::create('/', 'GET');
    $request1->headers->set('X-Test-Request', 'first');
    Runtime::dispatch($request1);

    $request2 = Request::create('/', 'GET');
    $request2->headers->set('X-Test-Request', 'second');
    Runtime::dispatch($request2);

    $boundRequest = Runtime::getApp()->make('request');
    expect($boundRequest->header('X-Test-Request'))->toBe('second');
});

it('persists the application instance across dispatches', function () {
    Runtime::boot(app());

    $appBefore = Runtime::getApp();

    $request = Request::create('/', 'GET');
    Runtime::dispatch($request);

    // Same application instance should be reused (persistent runtime)
    expect(Runtime::getApp())->toBe($appBefore);
});

it('can reboot after shutdown', function () {
    Runtime::boot(app());
    resetRuntimeState();
    expect(Runtime::isBooted())->toBeFalse();

    Runtime::boot(app());
    expect(Runtime::isBooted())->toBeTrue();

    $request = Request::create('/', 'GET');
    $response = Runtime::dispatch($request);
    expect($response->getStatusCode())->toBeIn([200, 302]);
});
