<?php

namespace App\Http\Controllers\API;

use App\Custom\ProfileFields;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserProfileController extends Controller
{
    private $auth;
    public function __construct(Auth $auth) {
        $this->middleware(function($request, $next) use ($auth) {
            $this->auth = $auth::user();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->auth->userProfile;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Custom\ProfileFields $profile
     * @param  \App\Models\UserProfile  $profile
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ProfileFields $input, UserProfile $profile)
    {
        $auth = $this->auth;
        $validated = $input::validate($request);
        $profile->fill($validated);
        $profile->membership_status = 'Pending';
        $profile->user()->associate($auth);

        if ($result = $profile->save()) {
            $auth->assignRole('member');
            return $result;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Custom\ProfileFields $input
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileFields $input)
    {
        $auth = $this->auth->userProfile;

        if ($request['facility'] === $auth->facility
        && $request['civilStatus'] === $auth->civilStatus
        && $request['contactNumber'] === $auth->contactNumber
        && $request['address'] === $auth->address
        && $request['ATCLicenseExpiry'] === $auth->ATCLicenseExpiry
        && $request['medicalLicenseExpiry'] === $auth->medicalLicenseExpiry
        && !$request['photo']) {
            abort(404, 'Nothing to update');
        }

        $validated = $input::updateProfile($request, $this->auth->userProfile);
        $auth->facility = $validated['facility'];
        $auth->civilStatus = $validated['civilStatus'];
        $auth->contactNumber = $validated['contactNumber'];
        $auth->address = $validated['address'];
        $auth->ATCLicenseExpiry = $validated['ATCLicenseExpiry'];
        $auth->medicalLicenseExpiry = $validated['medicalLicenseExpiry'];

        if ($auth->save()) {
            return $auth;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }

}
