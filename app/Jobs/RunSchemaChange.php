<?php

namespace App\Jobs;

use App\Events\SchemaChangeProgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Symfony\Component\Process\Process;
use Throwable;
use function Illuminate\Log\log as log;

class RunSchemaChange implements ShouldQueue//, ShouldBeUnique
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 60 * 60 * 24 * 7;  // 1 week

    public function __construct(
        private readonly string $command,
    )
    {
        log()->info('Creating ' . $command);
    }

    public function handle(): void
    {
        log()->info('Executing ' . $this->command);

        $process = new Process(['pt-online-schema-change', '--dry-run']);  // TODO: replace with generated command
        $process->start();

        foreach ($process as $type => $data) {
            $message = trim($data);
            log()->info("Schema change: " . $message);
            broadcast(new SchemaChangeProgress($message));
        }
    }

    public function failed(?Throwable $exception): void
    {
        // Send user notification of failure, etc...
    }
}
