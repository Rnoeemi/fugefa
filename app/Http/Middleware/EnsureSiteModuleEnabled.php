<?php

namespace App\Http\Middleware;

use App\Enums\SiteModule;
use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteModuleEnabled
{
    public function __construct(
        protected ModuleService $modules,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $siteModule = SiteModule::tryFrom($module);

        abort_unless(
            $siteModule instanceof SiteModule && $this->modules->isEnabled($siteModule),
            404,
        );

        return $next($request);
    }
}
