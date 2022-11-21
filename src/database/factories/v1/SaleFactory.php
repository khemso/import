<?php

namespace Database\Factories\v1;

use App\Models\v1\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => null,
            'hash' => md5(time()),
            'seller_id' => 10,
            'seller_firstname' => 'Manfred',
            'seller_lastname' => 'Schmidt',
            'date_joined' => '2022-01-01',
            'country' => 'DE',
            'contact_region' => 'Bayern',
            'contact_date' => '2022-05-01',
            'contact_customer_fullname' => $this->faker->name,
            'contact_type' => 'Phone',
            'contact_product_type_offered_id' => 11,
            'contact_product_type_offered' => 'Canned sausages',
            'sale_net_amount' => 293.12,
            'sale_gross_amount' => 367.3,
            'sale_tax_rate' => 0.19,
            'sale_product_total_cost' => 187.23
        ];
    }
}
