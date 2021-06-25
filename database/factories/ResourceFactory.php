<?php

namespace Database\Factories;

use App\Enums\ResourceType;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'external_metadata_record_id' => $this->faker->uuid,
            'title' => $this->faker->text(24),
            'version' => $this->faker->uuid(),
            'description' => $this->faker->text(256),
            'type' => $this->faker->randomElement(ResourceType::getValues()),
            'status' => $this->faker->randomElement([
                'draft', 'under_preparation', 'under_review', 'approved', 'published'
            ]),
            'pii_status' => $this->faker->randomElement(['pending', 'passed', 'failed']),
            'findable_score' => $this->faker->randomFloat(2, 0, 10),
            'accessible_score' => $this->faker->randomFloat(2, 0, 10),
            'interoperable_score' => $this->faker->randomFloat(2, 0, 10),
            'reusable_score' => $this->faker->randomFloat(2, 0, 10),
            'fair_scoring' => $this->faker->randomFloat(2, 0, 10),
            'published_at' => null,
        ];
    }
}
