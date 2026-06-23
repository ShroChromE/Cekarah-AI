<?php

namespace App\Console\Commands;

use App\Models\KnowledgeDocument;
use App\Services\KnowledgeIndexer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('knowledge:index')]
#[Description('Generate embeddings for unindexed or stale knowledge documents')]
class IndexKnowledge extends Command
{
    public function handle(KnowledgeIndexer $indexer): int
    {
        $documents = KnowledgeDocument::needsIndexing()->get();

        if ($documents->isEmpty()) {
            $this->info('All documents are already indexed.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();

        $failed = 0;

        foreach ($documents as $document) {
            try {
                $indexer->index($document);
                $bar->advance();
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Failed [{$document->id}] {$document->title}: {$e->getMessage()}");
                $failed++;
            }
        }

        $bar->finish();
        $this->newLine();

        $succeeded = $documents->count() - $failed;
        $this->info("Done. {$succeeded} indexed, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
