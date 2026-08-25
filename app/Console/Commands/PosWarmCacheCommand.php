<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class PosWarmCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:warm-cache {--tenant= : Warm cache for a specific tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warms up the POS caching layer for lightning fast loading';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\ProductCatalogService $catalogService)
    {
        $tenantId = $this->option('tenant');
        
        $this->info("Memulai proses warm up cache POS...");

        $query = \App\Models\Store::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $stores = $query->get();

        if ($stores->isEmpty()) {
            $this->warn("Tidak ada store aktif ditemukan.");
            return;
        }

        $bar = $this->output->createProgressBar($stores->count());

        foreach ($stores as $store) {
            // Ini akan memicu cache miss di dalam logic rememberForever dan melakukan query buildCatalog
            $catalogService->invalidateCatalog($store->id);
            $catalogService->getCatalog($store->id, $store->tenant_id);
            
            // Warm up kategori dan pajak
            $catalogService->invalidateCategories($store->tenant_id);
            $catalogService->getCategories($store->tenant_id);
            
            $catalogService->invalidateTaxRates($store->tenant_id);
            $catalogService->getTaxRates($store->tenant_id);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Cache POS berhasil di-warm up untuk {$stores->count()} toko!");
    }
}
