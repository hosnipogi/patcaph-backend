<?php

namespace Database\Factories;

use App\Custom\ProfileFields;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    private function generateFacilityHistory($length) {
        $field = ProfileFields::getFields();
        $facilities = $field['facilities'];
        $designation = $field['designation'];
        $facility = [];
        for ($i = 0; $i < $length; $i++) {
        $arr = $facilities[rand(0,count($facilities) - 1)];
        $fac = [];
        $fac['facility'] = $arr['facility'];
        $fac['area'] = $arr['area'][rand(0, count($arr['area']) - 1)];
        $fac['from'] = $this->faker->date();
        $fac['to'] = $this->faker->date();
        $fac['designation'] = $designation[rand(0, count($designation) - 1)];
        array_push($facility, $fac);
        }
        return $facility;
     }

    public function definition()
    {
        $alphabet = range('A', 'Z');
        $fields = ProfileFields::getFields();
        $batch = $fields['batch'];
        $civil_status = $fields['civil_status'];
        $licenseNumber = rand(100000, 1000000);

        $surname = $this->faker->lastName;
        $wiresign = $alphabet[rand(0, count($alphabet) - 1)] . $alphabet[rand(0, count($alphabet) - 1)];

        File::copy(storage_path('app/users/Bona-HK-.jpg'), storage_path('app/users/' . $surname . '-' . $wiresign . '-' . $licenseNumber . '.jpg'));

        return [
        'firstname' => $this->faker->colorName,
        'middlename' => $this->faker->monthName,
        'surname' => $surname,
        'wiresign' => $wiresign,
        'address' => $this->faker->address,
        'gender' => 'male',
        'civilStatus' => $civil_status[rand(0, count($civil_status) -1)],
        'birthday' => $this->faker->dateTime,
        'birthplace' => $this->faker->country,
        'facility' => $this->generateFacilityHistory(2),
        'dateEmployed' => $this->faker->dateTime,
        'batch' => $batch[rand(0, count($batch) - 1)],
        'ATCLicenseExpiry' => $this->faker->dateTime,
        'medicalLicenseExpiry' => $this->faker->dateTime,
        'licenseNumber' => $licenseNumber,
        'contactNumber' => rand(9150000000, 9159999999),
        'membership_status' => "Pending",
    ];
    }
}
