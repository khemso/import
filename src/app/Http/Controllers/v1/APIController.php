<?php

namespace App\Http\Controllers\v1;

use App\Models\v1\Sale;
use App\Jobs\ProcessFileJob;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    /**
     * Accept CSV file and upload it to the sever then run the import data job
     *
     * @param Request $request
     * 
     * @return JSON response with success or error message
     * 
     */
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'  => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()], 409);
        }

        try {
            $file = $request->file('file');

            if ($file->isValid()) {
                $path = Storage::disk( env('UPLOAD_FILE_STORAGE') )->putFile(env('UPLOAD_FILE_PATH'), $file);

                //Job to process the file to make sure the api response is not delayed
                dispatch(new ProcessFileJob($path));
            }

            return response()->json(['status' => true, 'message' => 'Data saved successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Data couldn\'t be saved'], 409);
        }
    }

    /**
     * Provide complete seller data via id
     *
     * @param Request $request
     * @param mixed $id the seller ID
     * 
     * @return JSON of all seller data
     * 
     */
    public function seller(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], ['id'  => 'required|integer']);

        if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()], 409);
        }

        try {
            $data = Sale::where('seller_id', $id)->get();

            $response = [];
            if(count($data)){
                $response['seller_id'] = $data[0]['seller_id'];
                $response['seller_firstname'] = $data[0]['seller_firstname'];
                $response['seller_lastname'] = $data[0]['seller_lastname'];
                $response['date_joined'] = $data[0]['date_joined'];
                $response['country'] = $data[0]['country'];
                foreach ($data as $row){
                    $response['contacts'][] = [
                        'contact_region' => $row['contact_region'],
                        'contact_date' => $row['contact_date'],
                        'contact_customer_fullname' => $row['contact_customer_fullname'],
                        'contact_type' => $row['contact_type'],
                    ];

                    if($row['sale_net_amount'] != null)
                    $response['sales'][] = [
                        'sale_date' => $row['contact_date'],
                        'customer_fullname' => $row['contact_customer_fullname'],
                        'product_type_offered_id' => $row['contact_product_type_offered_id'],
                        'product_type_offered' => $row['contact_product_type_offered'],
                        'sale_net_amount' => $row['sale_net_amount'],
                        'sale_gross_amount' => $row['sale_gross_amount'],
                        'sale_tax_rate' => $row['sale_tax_rate'],
                        'sale_product_total_cost' => $row['sale_product_total_cost']
                    ];
                }

                return response()->json(['status' => true, 'data' => $response], 200);
            }

            return response()->json(['status' => false, 'message' => 'Error seller not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error retriving the data'], 409);
        }
    }

    /**
     * Provide a list of all contacts established by the seller.
     *
     * @param Request $request
     * @param mixed $id the seller ID
     * 
     * @return JSON of all seller contacts
     * 
     */
    public function contacts(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], ['id'  => 'required|integer']);

        if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()], 409);
        }

        try {
            $data = Sale::where('seller_id', $id)->get();

            $response = [];
            if(count($data)){
                foreach ($data as $row){
                    $response['contacts'][] = [
                        'contact_region' => $row['contact_region'],
                        'contact_date' => $row['contact_date'],
                        'contact_customer_fullname' => $row['contact_customer_fullname'],
                        'contact_type' => $row['contact_type'],
                    ];
                }

                return response()->json(['status' => true, 'data' => $response], 200);
            }

            return response()->json(['status' => false, 'message' => 'Error seller not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error retriving the data'], 409);
        }
    }

    /**
     * Provide a list of all sales data accomplished by the seller.
     *
     * @param Request $request
     * @param mixed $id the seller ID
     * 
     * @return JSON of all seller sales
     * 
     */
    public function sales(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], ['id'  => 'required|integer']);

        if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()], 409);
        }

        try {
            $data = Sale::where('seller_id', $id)->get();

            $response = [];
            if(count($data)){
                foreach ($data as $row){
                    if($row['sale_net_amount'] != null)
                    $response['sales'][] = [
                        'sale_date' => $row['contact_date'],
                        'customer_fullname' => $row['contact_customer_fullname'],
                        'product_type_offered_id' => $row['contact_product_type_offered_id'],
                        'product_type_offered' => $row['contact_product_type_offered'],
                        'sale_net_amount' => $row['sale_net_amount'],
                        'sale_gross_amount' => $row['sale_gross_amount'],
                        'sale_tax_rate' => $row['sale_tax_rate'],
                        'sale_product_total_cost' => $row['sale_product_total_cost']
                    ];
                }

                return response()->json(['status' => true, 'data' => $response], 200);
            }

            return response()->json(['status' => false, 'message' => 'Error seller not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error retriving the data'], 409);
        }
    }

    /**
     * Provide a list of all sales of a given year
     *
     * @param Request $request
     * @param mixed $id the seller ID
     * 
     * @return JSON of all seller sales
     * 
     */
    public function summary(Request $request, $year)
    {
        $validator = Validator::make(['year' => $year], ['year'  => 'required|digits:4']);

        if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()], 409);
        }

        try {
            $data = Sale::whereYear('contact_date', $year)->orderBy('created_at', 'DESC')->get();

            $response = [];
            $netAmount = 0;
            $grossAmount = 0;
            $taxAmount = 0;
            $totalCost = 0;
            if(count($data)){
                foreach ($data as $row){
                    if($row['sale_net_amount'] != null){
                        $netAmount += $row['sale_net_amount'];
                        $grossAmount += $row['sale_gross_amount'];
                        $taxAmount += $row['sale_gross_amount']  * $row['sale_tax_rate'];
                        $totalCost += $row['sale_product_total_cost'];

                        $response['sales'][] = [
                            'sale_date' => $row['contact_date'],
                            'seller_id' => $row['seller_id'],
                            'seller_firstname' => $row['seller_firstname'],
                            'seller_lastname' => $row['seller_lastname'],
                            'customer_fullname' => $row['contact_customer_fullname'],
                            'product_type_offered_id' => $row['contact_product_type_offered_id'],
                            'product_type_offered' => $row['contact_product_type_offered'],
                            'sale_net_amount' => $row['sale_net_amount'],
                            'sale_gross_amount' => $row['sale_gross_amount'],
                            'sale_tax_rate' => $row['sale_tax_rate'],
                            'sale_product_total_cost' => $row['sale_product_total_cost']
                        ];
                    }
                } 
 
                $profit = $grossAmount - $taxAmount - $totalCost;
                $profit_percentage = ($profit / $grossAmount) * 100;
                $response['summary'][] = [
                    'netAmount' => number_format($netAmount, 2),
                    'grossAmount' => number_format($grossAmount, 2),
                    'taxAmount' => number_format($taxAmount, 2),
                    'totalCost' => number_format($totalCost, 2),
                    'profit' => number_format($profit, 2),
                    'profitPercentage' => number_format($profit_percentage, 2) . '%'
                ];
            }

            return response()->json(['status' => true, 'data' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error retriving the data'], 409);
        }
    }
}
