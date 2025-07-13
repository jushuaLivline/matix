<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class ConvertTextToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:convert-text-to-excel';


    protected $prevFileName = '';
    protected $numbering = 0;

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
        $textFiles = collect(Storage::disk('text-files')->allFiles());
        try {
            foreach($textFiles as $index => $txtFile){
                $this->convertTextFilesToExcel($txtFile);
            }
            $this->line('Text files converted to Excel successfully!');
        } catch (\Exception $e) {
            $this->error('Error occurred during text to Excel conversion: ' . $e->getMessage());
        }
    }

    public function convertTextFilesToExcel($textFile)
    {

        $outputDir = Storage::disk('excel-files')->path('excel-file');
        $successCount = 0;
        $rowsPerFile = 1000;

        // Check if the file exists before processing
        if (!Storage::disk('text-files')->exists($textFile)) {
            return; // Skip to the next file if it doesn't exist
        }

        $filePath = Storage::disk('text-files')->path($textFile);

        $fileLines = file($filePath, FILE_IGNORE_NEW_LINES);

        // Split the lines into chunks of rows
        $chunks = array_chunk($fileLines, $rowsPerFile);

        // Create the Excel files
        foreach ($chunks as $chunkIndex => $chunk) {
            // Create a new Excel file
            $excelFile = new Spreadsheet();
            $sheet = $excelFile->getActiveSheet();
            // Add the data to the sheet
            foreach ($chunk as $rowIndex => $row) {
                $rowData = explode("\t", $row); // Modify the delimiter if needed
                // Set the data in each cell
                foreach ($rowData as $columnIndex => $data) {
                    $value = trim($data);
                    $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $value);
                }
            }

            // Save the Excel file
            $filename = pathinfo($textFile, PATHINFO_FILENAME);
            $numbering = str_pad(($chunkIndex + 1), 5, '0', STR_PAD_LEFT);

            if($this->prevFileName == $this->sanitizeTxtFile($filename)){
                $this->numbering++;
                $numbering = str_pad(($this->numbering), 5, '0', STR_PAD_LEFT);
            }else{
                $this->numbering = 0;
                $numbering = str_pad(($chunkIndex + 1), 5, '0', STR_PAD_LEFT);
            }


            $this->prevFileName = $this->sanitizeTxtFile($filename);
            $outputFilename = $this->sanitizeTxtFile($filename) . '_' . $numbering . ".xlsx";
            $writer = new Xlsx($excelFile);
            $writer->save($outputDir . '/' . $outputFilename);

            $successCount++;
        }

        if ($successCount === 0) {
            throw new \Exception('No text files were converted to Excel.');
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
