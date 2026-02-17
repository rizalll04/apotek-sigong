<?php

namespace App\Jobs;

use App\Models\ForecastParameter;
use App\Models\Produk;
use App\Services\ForecastOptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Background job untuk fine-tuning parameter optimasi peramalan.
 *
 * Job ini dijalankan secara periodik (setiap hari, default pukul 02:00)
 * untuk melakukan fine-tuning dengan grid search 729 iterasi pada produk
 * yang sudah memiliki coarse search results.
 *
 * Keuntungan background job:
 * - User mendapat hasil cepat (phase coarse ~300ms)
 * - System computing time dioptimalkan dengan fine-tuning di background
 * - Fine-tuning hasil tersimpan untuk akses mendatang
 * - Job bisa di-reschedule/re-run tanpa affecting user experience
 */
class FinetuneOptimizationParameters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // Set queue priority: low priority background job
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * Strategi:
     * 1. Query semua produk yang ada
     * 2. Untuk setiap produk:
     *    a. Check apakah sudah memiliki cached parameters
     *    b. Jika cache > 90 hari atau tidak ada, lakukan fine-tuning
     *    c. Fine-tuning akan menjalankan 729 iterasi grid search
     *    d. Update database dengan hasil baru
     * 3. Log hasil fine-tuning untuk monitoring
     *
     * @param ForecastOptimizationService $service
     * @return void
     */
    public function handle(ForecastOptimizationService $service)
    {
        Log::info("FinetuneOptimizationParameters job started");

        try {
            $products = Produk::all();
            $processedCount = 0;
            $skippedCount = 0;

            foreach ($products as $product) {
                // Check if product has cached parameters
                $cached = ForecastParameter::where('product_id', $product->id)->first();

                // Determine if optimization needed
                $needsOptimization = false;

                if (!$cached) {
                    // No cache exists, create one with fine-tuning
                    $needsOptimization = true;
                } elseif ($cached->updated_at->diffInDays(now()) > 90) {
                    // Cache is older than 90 days, refresh with fine-tuning
                    $needsOptimization = true;
                }

                if ($needsOptimization) {
                    try {
                        Log::info("Fine-tuning optimization for product {$product->id} ({$product->nama})");

                        // Perform fine-tuning (729 iterations, ~2-3 seconds)
                        $result = $service->optimizeParameters($product->id, 12, $fineSearch = true);

                        Log::info("Fine-tuning completed for product {$product->id}", [
                            'alpha' => $result['optimal']['alpha'],
                            'beta' => $result['optimal']['beta'],
                            'gamma' => $result['optimal']['gamma'],
                            'mape' => $result['optimal']['mape'],
                            'computation_time_ms' => $result['metadata']['computation_time_ms']
                        ]);

                        $processedCount++;
                    } catch (\Exception $e) {
                        Log::error("Failed to fine-tune product {$product->id}: {$e->getMessage()}");
                        $skippedCount++;
                    }
                } else {
                    $skippedCount++;
                }
            }

            Log::info("FinetuneOptimizationParameters job completed", [
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'total_products' => count($products)
            ]);

        } catch (\Exception $e) {
            Log::error("FinetuneOptimizationParameters job failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error("FinetuneOptimizationParameters job failed permanently: {$exception->getMessage()}");
    }
}
