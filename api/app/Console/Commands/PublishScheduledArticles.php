<?php
namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled';
    protected $description = 'Publish articles whose scheduled_at time has passed';

    public function handle(): int
    {
        $count = Article::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update([
                'status'       => 'published',
                'published_at' => now(),
            ]);

        $this->info("Published {$count} scheduled article(s).");
        return Command::SUCCESS;
    }
}
