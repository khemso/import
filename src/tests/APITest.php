<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class APITest extends TestCase
{
    /**
     * /api/v1/load 
     * Successfull upload
     *
     * @return void
     */
    public function test_uploading_file_successfully()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.csv');
        $this->call('POST', '/api/v1/load', [], [], ['file' => $file], []);

        // Assert the file was uploaded from response
        $this->seeStatusCode(200);
    }

    /**
     * /api/v1/load 
     * Wrong file format 
     * 
     * @return void
     */
    public function test_uploading_wrong_file()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.jpg');
        $this->call('POST', '/api/v1/load', [], [], ['file' => $file], []);

        // Assert the file was uploaded from response
        $this->seeStatusCode(409);
    }

    /**
     * /api/v1/sellers/{id}
     * Return seller info
     *
     * @return void
     */
    public function test_returning_seller_info()
    {
        $sale = App\Models\v1\Sale::factory()->create();

        $this->get("/api/v1/sellers/10", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'data' => [
                "seller_id",
                "seller_firstname",
                "seller_lastname",
                "date_joined",
                "country",
                "contacts" => [
                    [
                        "contact_region",
                        "contact_date",
                        "contact_customer_fullname",
                        "contact_type",
                    ]
                ],
                "sales" => [
                    [
                        "sale_date",
                        "customer_fullname",
                        "product_type_offered_id",
                        "product_type_offered",
                        "sale_net_amount",
                        "sale_gross_amount",
                        "sale_tax_rate",
                        "sale_product_total_cost",
                    ]
                ]
            ],
        ]);
    }
    
    /**
     * /api/v1/sellers/{id}/contacts
     * Return seller contacts
     *
     * @return void
     */
    public function test_returning_seller_contacts()
    {
        $this->get("/api/v1/sellers/10/contacts", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'data' => [
                "contacts" => [
                    [
                        "contact_region",
                        "contact_date",
                        "contact_customer_fullname",
                        "contact_type",
                    ]
                ]
            ],
        ]);
    }

    /**
     * /api/v1/sellers/{id}/sales
     * Return seller contacts
     *
     * @return void
     */
    public function test_returning_seller_sales()
    {
        $this->get("/api/v1/sellers/10/sales", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'data' => [
                "sales" => [
                    [
                        "sale_date",
                        "customer_fullname",
                        "product_type_offered_id",
                        "product_type_offered",
                        "sale_net_amount",
                        "sale_gross_amount",
                        "sale_tax_rate",
                        "sale_product_total_cost"
                    ]
                ]
            ],
        ]);
    }

     /**
     * /api/v1/sales/
     * Return seller contacts
     *
     * @return void
     */
    public function test_returning_year_sales()
    {
        $this->get("/api/v1/sales/2022", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'data' => [
                "sales" => [
                    [
                        "sale_date",
                        "seller_id",
                        "seller_firstname",
                        "seller_lastname",
                        "customer_fullname",
                        "product_type_offered_id",
                        "product_type_offered",
                        "sale_net_amount",
                        "sale_gross_amount",
                        "sale_tax_rate",
                        "sale_product_total_cost"
                    ]
                ],
                "summary" => [
                    [
                        "netAmount",
                        "grossAmount",
                        "taxAmount",
                        "totalCost",
                        "profit",
                        "profitPercentage"
                    ]
                ]
            ],
        ]);
    }
}
