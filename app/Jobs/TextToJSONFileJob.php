<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TextToJSONFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $file, public $fromSplitTexts = null)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $outputDir = Storage::disk('database-json-files')->path('/');
        $successCount = 0;
        $rowsPerFile = 500;

        if($this->fromSplitTexts){
            $disk = $this->fromSplitTexts;
        }else{
            $disk = "text-files";    
        }
            
        // Check if the file exists before processing
        if (!Storage::disk($disk)->exists($this->file)) {
            return; // Skip to the next file if it doesn't exist
        }

        $filePath = Storage::disk($disk)->path($this->file);
        
        
        $fileLines = file($filePath, FILE_IGNORE_NEW_LINES);

        // Split the lines into chunks of rows
        $chunks = array_chunk($fileLines, $rowsPerFile);

        // Create the Json files
        foreach ($chunks as $chunkIndex => $chunk) {
            $dataArray = array();

            foreach ($chunk as $rowIndex => $row) {
                $rowArray = array();
                $rowData = explode("\t", $row); // Modify the delimiter if needed
                // Set the data in each cell
                foreach ($rowData as $columnIndex => $data) {
                    $value = trim($data);
                    array_push($rowArray, $value);
                }

                array_push($dataArray, $rowArray);
            }

            // Save the Excel file
            $filename = pathinfo($this->file, PATHINFO_FILENAME);
            $numbering = str_pad($this->numbering(), 5, '0', STR_PAD_LEFT);

            $outputFilename = $this->sanitizeTxtFile($filename) . '_' . $numbering . ".json";
            $json = json_encode($dataArray);
            file_put_contents($outputDir . '/' . $outputFilename, $json);
            $successCount++;
        }

        if ($successCount === 0) {
            throw new \Exception('No text files were converted to JSON.');
        }
    }

    function sanitizeTxtFile($file){
        $excludeInNaming = [".txt", "split-texts", "/"];
     
        for($i = 0; $i < 4000; $i++){
            array_push($excludeInNaming, "_". str_pad($i, 5, '0', STR_PAD_LEFT));
        }
        
        return str_replace($excludeInNaming, "", $file);
       
    }

    function numbering(){
        $fileSearch = Storage::disk("database-json-files")->path("/");
        $files = File::glob($fileSearch  . "/" . $this->sanitizeTxtFile($this->file) . "*");
        
        return count($files) + 1 ?? 0;
    }
}
