<?php

namespace App\Console\Commands;

use App\Jobs\TextToJSONFileJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TextToJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:text-to-json {disk?}';


    protected $prevFileName = '';


    protected $numbering = 0;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {

        if($this->argument("disk")) {
            $disk = $this->argument("disk");
            $textFiles = collect(Storage::disk($disk)->allFiles())
            ->filter(function ($file) {
                return !preg_match('/\/\.[^\/]+$/', $file);
            });   
        } else {
            $disk = 'text-files';
            $textFiles = collect(Storage::disk($disk)->allFiles("split-texts"))
                ->filter(function ($file) {
                    return !preg_match('/\/\.[^\/]+$/', $file);
                });   
        }
    
        
        try {
            foreach($textFiles as $index => $txtFile){
                if(substr($txtFile,0,1) == "."){
                    continue;
                }

                TextToJSONFileJob::dispatch($txtFile, $disk);
            }
            $this->line('Text files converted to JSON successfully!');
        } catch (\Exception $e) {
            $this->error('Error occurred during text to Excel conversion: ' . $e->getMessage());
        }
    }


    function sanitizeTxtFile($file){
        $excludeInNaming = [];
     
        for($i = 0; $i < 4000; $i++){
            array_push($excludeInNaming, "_". str_pad($i, 5, '0', STR_PAD_LEFT));
        }
        
        return str_replace($excludeInNaming, "", $file);
    }
}
