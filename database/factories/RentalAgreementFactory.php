<?php

namespace Database\Factories;

use App\Models\RentalAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalAgreementFactory extends Factory
{
    protected $model = RentalAgreement::class;

    public function definition(): array
    {
        return [
            'room_id' => 6, // mặc định có thể override
            'status' => $this->faker->randomElement(['Active', 'Signed', 'Cancelled']),
            'renter_id' => 1, // nếu có
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
        ];
    }
}
