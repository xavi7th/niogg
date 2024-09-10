<?php

namespace Modules\Conference\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Conference\Models\ConferenceRegistrant;

class ConferenceRegistrantFactory extends Factory
{
  protected $model = ConferenceRegistrant::class;

  public function definition(): array
  {
    return [
      'full_name' => $this->faker->name(),
      'email' => $this->faker->email(),
      'phone' => $this->faker->phoneNumber(),
      'conference_tag' => 'launch_conference',
      'password' => '$2y$12$x5Nvli.fe..7PXUgACxFEeVaXTn3ewioX/vP1OlEn1jXDogHcoxwq', //pass
      'registration_id' => $this->faker->bothify('???-##############'),
    ];
  }
}
