<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Subscriber;
use App\Models\SubscriberImport;
use App\Models\SuppressionList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProcessSubscriberImport implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SubscriberImport $import,
        public bool $hasHeader = true
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mark as processing
            $this->import->update([
                'status' => SubscriberImport::STATUS_PROCESSING,
                'started_at' => now(),
            ]);

            $filePath = Storage::disk('local')->path($this->import->file_path);
            
            if (!file_exists($filePath)) {
                throw new \Exception('Import file not found');
            }

            $handle = fopen($filePath, 'r');
            $errors = [];
            $imported = 0;
            $skipped = 0;
            $errorCount = 0;
            $rowNumber = 0;
            $totalRows = 0;

            // Count total rows first
            while (fgets($handle) !== false) {
                $totalRows++;
            }
            
            if ($this->hasHeader && $totalRows > 0) {
                $totalRows--;
            }

            $this->import->update(['total_rows' => $totalRows]);

            // Reset file pointer
            rewind($handle);

            // Skip header row if specified
            if ($this->hasHeader) {
                fgetcsv($handle);
            }

            // Process rows
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                if (empty($row[0])) {
                    $skipped++;
                    $this->updateProgress($rowNumber, $imported, $skipped, $errorCount);
                    continue;
                }

                $email = trim(strtolower($row[0]));
                
                // Validate email
                $validator = Validator::make(['email' => $email], [
                    'email' => ['required', 'email', 'max:255'],
                ]);

                if ($validator->fails()) {
                    $errorCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $email,
                        'error' => 'Invalid email format',
                    ];
                    $this->updateProgress($rowNumber, $imported, $skipped, $errorCount);
                    continue;
                }

                // Check if suppressed
                if (SuppressionList::isSuppressed($this->import->list->brand_id, $email)) {
                    $skipped++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $email,
                        'error' => 'Email is in suppression list',
                    ];
                    $this->updateProgress($rowNumber, $imported, $skipped, $errorCount);
                    continue;
                }

                // Check if already exists
                $exists = Subscriber::where('list_id', $this->import->list_id)
                    ->where('email', $email)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $this->updateProgress($rowNumber, $imported, $skipped, $errorCount);
                    continue;
                }

                // Create subscriber
                try {
                    Subscriber::create([
                        'list_id' => $this->import->list_id,
                        'email' => $email,
                        'first_name' => !empty($row[1]) ? trim($row[1]) : null,
                        'last_name' => !empty($row[2]) ? trim($row[2]) : null,
                        'custom_fields' => [],
                        'status' => Subscriber::STATUS_SUBSCRIBED,
                        'subscribed_at' => now(),
                        'ip_address' => null,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ];
                }

                $this->updateProgress($rowNumber, $imported, $skipped, $errorCount);

                // Update every 100 rows to avoid too many database writes
                if ($rowNumber % 100 === 0) {
                    $this->import->list->updateSubscriberCounts();
                }
            }

            fclose($handle);

            // Final update
            $this->import->list->updateSubscriberCounts();

            // Mark as completed
            $this->import->update([
                'status' => SubscriberImport::STATUS_COMPLETED,
                'imported_count' => $imported,
                'skipped_count' => $skipped,
                'error_count' => $errorCount,
                'errors' => array_slice($errors, 0, 100), // Store max 100 errors
                'completed_at' => now(),
            ]);

            // Delete the uploaded file after processing
            Storage::disk('local')->delete($this->import->file_path);

        } catch (\Exception $e) {
            $this->import->update([
                'status' => SubscriberImport::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Update import progress.
     */
    private function updateProgress(int $processed, int $imported, int $skipped, int $errors): void
    {
        $this->import->update([
            'processed_rows' => $processed,
            'imported_count' => $imported,
            'skipped_count' => $skipped,
            'error_count' => $errors,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->import->update([
            'status' => SubscriberImport::STATUS_FAILED,
            'error_message' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
