<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Ramsey\Uuid\Uuid;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $tenancy = false;
    /**
     * If true, setup has run at least once.
     *
     * @var boolean
     */
    protected static $setUpRun = false;

    /**
     * Set up the test.
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!static::$setUpRun) {
            Artisan::call('migrate:refresh', ['--seed' => true]);
            Artisan::call('permission:cache-reset');
            static::$setUpRun = true;
            config(['tenancy.database.prefix' => 'yopractice_testing_']);
            //create test tenant
            if (!Tenant::count()) {
                $tenant = Tenant::create([
                    'name' => 'Test',
                    'id' => Uuid::uuid4()->toString()
                ]);
                $tenant->createDomain("test");
            }
            //drop our test databases
        }
        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function initializeTenancy(): void
    {
        $tenant = Tenant::find(1);
        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://' . $tenant->domains->first()->domain . '.' . config('app.central_domain'));
    }
}
