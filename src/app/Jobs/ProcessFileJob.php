<?php

namespace App\Jobs;

use Log;
use App\Models\v1\Sale;
use Illuminate\Support\Facades\Storage;

class ProcessFileJob extends Job
{
    private $path;
    private $storage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->storage = Storage::disk( env('UPLOAD_FILE_STORAGE') );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->storage->exists($this->path)) {
            $file = $this->storage->get($this->path);

            try{

                foreach(preg_split("/((\r?\n)|(\r\n?))/", $file) as $index => $line){
                    if(!$index || empty($line)) continue;
                    
                    $row = [];
                    list(
                        $row['id'],
                        $row['seller_id'],
                        $row['seller_firstname'],
                        $row['seller_lastname'],
                        $row['date_joined'],
                        $row['country'],
                        $row['contact_region'],
                        $row['contact_date'],
                        $row['contact_customer_fullname'],
                        $row['contact_type'],
                        $row['contact_product_type_offered_id'],
                        $row['contact_product_type_offered'],
                        $row['sale_net_amount'],
                        $row['sale_gross_amount'],
                        $row['sale_tax_rate'],
                        $row['sale_product_total_cost']
                    ) = preg_split("/\,/", $line);
    
                    //Replacing empty values with null
                    $row = array_map(function($value) {
                        return $value === "" ? NULL : $value;
                    }, $row);
    
                    $row['date_joined'] = \Carbon\Carbon::parse($row['date_joined'])->format('Y-m-d');
                    $row['contact_date'] = \Carbon\Carbon::parse($row['contact_date'])->format('Y-m-d');
                    $row['hash'] = md5($row['seller_id'] . $row['country'] . $row['contact_date'].$row['contact_customer_fullname'].$row['contact_product_type_offered_id']);
    
                    Sale::firstOrCreate(
                        ['hash' => $row['hash']],
                        $row
                    );
                } 
    
                $this->storage->delete($this->path);
                //Log::info("File ($this->path) processed successfully");
            } catch (Exception $e) {
                Log::alert("File ($this->path) didn't process successfully");
            }

            
        }

        return;
    }
}
