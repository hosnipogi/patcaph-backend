<?php

namespace App\Custom;

use App\Models\UserProfile;
use Error;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileFields
{
    private static $fields;

    public function __construct() {
        $fields = Storage::disk('local')->get('fields.json');
        $fields = json_decode($fields, true);
        self::$fields = $fields;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    private static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    private static function validateImage($value, $fail)
    {
        try {
            preg_match("/data:image\/(.*?);/", $value, $image_extension);
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $value); // remove the type part
            imagecreatefromstring(base64_decode($image));
        } catch (\Throwable $th) {
            if ($value == null) {
                $fail('Please upload an image');
            } else {
                $fail($th);
            }
        }
    }

    private static function validateFacility($value, $fail) {
        try {
            $fields = self::$fields;
            $facility = strval($value['facility']);
            $area = strval($value['area']);
            $from = strval($value['from']);
            $to = strval($value['to']);
            $designation = strval($value['designation']);

            if ($facility === '' || $area === '' || $from === '' || $to === '' || $designation === '') {
                $fail('Invalid selection');
                return;
            }

            $filtered = array_values(array_filter($fields['facilities'], function($fac) use ($facility) {
                if ($fac['facility'] === $facility) {
                    return $fac;
                }
            }));

            if (count($filtered)) {
                if (!in_array($area, $filtered[0]['area'])) {
                    $fail('Invalid facility area selection');
                }
            } else {
                $fail("Invalid facility selection");
            }

            if (!in_array($designation, $fields['designation'])) {
                $fail('Invalid designation');
            }

            if (!self::validateDate($from) || !self::validateDate($to)) {
                $fail('Check facility field, invalid date');
            }

            return;
        } catch (\Throwable $th) {
            $fail($th);
            return $th;
        }
    }

    public static function validate($request)
    {
        $data = Validator::make($request->input(), [
            'firstname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'middlename' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'surname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'address' => 'required|max:255',
            'contactNumber' => 'required|regex:/^\+?[0-9\s]{6,}$/', //min of 6 digits
            'gender' => 'required|in:' . implode(",", self::$fields['gender']),
            'civilStatus' => 'required|in:' . implode(",", self::$fields['civil_status']),
            'birthday' => 'required|date_format:Y-m-d',
            'birthplace' => 'required|max:255',
            'batch' => 'required|in:' . implode(",", self::$fields['batch']),
            'wiresign' => 'required|max:2|alpha',
            'dateEmployed' => 'required|date_format:Y-m-d',
            'licenseNumber' => 'required|unique:user_profiles|regex:/^[0-9]{6}$/',
            'ATCLicenseExpiry' => 'required|date_format:Y-m-d',
            'medicalLicenseExpiry' => 'required|date_format:Y-m-d',
            'facility' => function ($_, $value, $fail) {
                foreach ($value as $facility) {
                    self::validateFacility($facility, $fail);
                }
            },
            'photo' => function ($_, $value, $fail) {
                self::validateImage($value, $fail);
            },
        ])->validate();

        $data['birthday'] = Carbon::parse($data['birthday']);
        $data['dateEmployed'] = Carbon::parse($data['dateEmployed']);
        $data['ATCLicenseExpiry'] = Carbon::parse($data['ATCLicenseExpiry']);
        $data['medicalLicenseExpiry'] = Carbon::parse($data['medicalLicenseExpiry']);

        Arr::pull($data, 'photo');

        $img = Image::make($request->input('photo'))
            ->fit(800)
            ->encode('jpg');
        $filename =
            'users/' .
            $data['surname'] .
            "-" .
            $data['wiresign'] .
            "-" .
            $data['licenseNumber'] .
            ".jpg";
        Storage::put($filename, $img);

        return $data;
    }

    public static function updateProfile($request, $user)
    {
        $data = Validator::make($request->input(), [
            'civilStatus' => 'in:' . implode(",", self::$fields['civil_status']),
            'address' => 'max:255',
            'contactNumber' => 'numeric|min:6',
            'ATCLicenseExpiry' => 'date_format:Y-m-d',
            'medicalLicenseExpiry' => 'date_format:Y-m-d',
            'facility' => function ($_, $values, $fail) {
                foreach ( $values as $facility) {
                    self::validateFacility($facility, $fail);
                }
            },
        ])->validate();

        if ($request['photo']) {
            $request->validate([
                'photo' => function ($attr, $value, $fail) {
                    self::validateImage($value, $fail);
                },
            ]);

            $img = Image::make($request->input('photo'))
                ->fit(800)
                ->encode('jpg');
            $filename =
                'users/' .
                $user->surname .
                "-" .
                $user->wiresign .
                "-" .
                $user->licenseNumber .
                ".jpg";
            Storage::put($filename, $img);
        }

        return $data;
    }
}
