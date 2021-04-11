<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;
use Lukasss93\Larex\Support\Utils;

class LarexLocalizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:localize {--import : Import untranslated strings to CSV file }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Localize untranslated strings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $reader = CsvReader::create(base_path(config('larex.csv.path')));
        $csvRows = $reader->getRows()->collect();
        $filesFound = Utils::findFiles(config('larex.search.dirs'), config('larex.search.patterns'));

        $stringsSaved = $csvRows->map(fn($item) => "{$item['group']}.{$item['key']}")->values();
        $stringsFound = Utils::parseStrings($filesFound, config('larex.search.functions'));

        $unlocalizedStrings = $stringsFound
            ->reject(fn($item) => $stringsSaved->contains($item['string']))
            ->groupBy('filename')
            ->map->sortBy('line')
            ->map->values()
            ->flatten(1);

        if ($unlocalizedStrings->isEmpty()) {
            $this->info('No unlocalized strings found.');
            return 0;
        }

        $subject = Str::plural('string', $unlocalizedStrings->count());
        $this->warn("{$unlocalizedStrings->count()} unlocalized {$subject} found:");

        foreach ($unlocalizedStrings as $item) {
            $this->line("<fg=red>{$item['string']} is untranslated at line {$item['line']}, column {$item['column']} in {$item['filepath']}</>");
        }

        if ($this->option('import')) {
            $this->line('');
            $this->warn('Adding unlocalized string to CSV file...');

            $languages = $reader->getHeader()->skip(2);

            $unlocalizedStringsToAdd = $unlocalizedStrings
                ->map(function ($item) use ($languages) {
                    $couple = Str::of($item['string'])->explode('.');

                    $output = collect([])
                        ->put('group', $couple->get(0))
                        ->put('key', $couple->skip(1)->implode('.'));

                    foreach ($languages as $lang) {
                        $output->put($lang, '');
                    }

                    return $output->toArray();
                });

            CsvWriter::create(base_path(config('larex.csv.path')))
                ->addRows($csvRows->toArray())
                ->addRows($unlocalizedStringsToAdd->toArray());

            $this->info('Done.');
        }

        return 0;
    }
}
