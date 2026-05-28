<?php
namespace App\Providers;

use App\Models\Permohonan;
use App\Models\Policies\PermohonanPolicy;
use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\SpamDetectorService;
use App\Policies\PermohonanPolicy as PermohonanPolicyAlias;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SpamDetectorService::class);
    }

    public function boot(): void
    {
        Gate::policy(Permohonan::class, PermohonanPolicyAlias::class);

        // Share setting ke semua view
        View::composer('*', function ($view) {
            try {
                $view->with('__setting', fn(string $key, mixed $default = null) => Setting::get($key, $default));
            } catch (\Throwable) {}
        });

        // @role directive
        Blade::directive('role', function ($roles) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, array_map('trim', explode(',', {$roles})))): ?>";
        });
        Blade::directive('endrole', fn() => '<?php endif; ?>');
    }
}
