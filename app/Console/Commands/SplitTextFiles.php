<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SplitTextFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:split-files {file : The path to the large text file} {lines : Number of lines per small file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = database_path($this->argument('file'));
        $linesPerFile = (int) $this->argument('lines') ?? 1000;

        // Open the large text file
        $largeFile = fopen($filePath, 'r');
        if ($largeFile === false) {
            $this->error("Failed to open the file: $filePath");
            return 1;
        }

        // Read the large file line by line
        $currentLine = 0;
        $currentFileIndex = 1;
        $smallFile = null;

        while (!feof($largeFile)) {
            $line = fgets($largeFile);

            // Create a new small file if needed
            if ($currentLine === 0) {
                $smallFilePath = $this->getSmallFilePath($filePath, $currentFileIndex);
                $smallFile = fopen($smallFilePath, 'w');
                if ($smallFile === false) {
                    $this->error("Failed to create a small file: $smallFilePath");
                    fclose($largeFile);
                    return 1;
                }
            }

            // Write the line to the small file
            fwrite($smallFile, $line);

            // Increment the line counter
            $currentLine++;

            // If reached the desired lines per small file, close it and reset the counters
            if ($currentLine === $linesPerFile) {
                fclose($smallFile);
                $currentLine = 0;
                $currentFileIndex++;
            }
        }

        // Close the large file
        fclose($largeFile);

        $this->info("Splitting complete. Created $currentFileIndex small files.");
        return 0;
    }

    protected function getSmallFilePath($filePath, $fileIndex)
    {
        $pathInfo = pathinfo($filePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        $numbering = str_pad(($fileIndex + 1), 5, '0', STR_PAD_LEFT);
        return "$directory/split-texts/$filename" . "_" . "$numbering.$extension";
    }
}
