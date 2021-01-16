<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO create middleware to check if user has verified email
        // TODO create eloquent queries to check if the APPROVED USER has verified email

        if (request()->query('unverified') == 1) {
            $unverified = User::doesnthave('userProfile')->paginate(10);
            return UserResource::collection($unverified);
        }

        if (request()->query('approved') == 1) {
            $approved = User::with('userProfile')->whereHas('userProfile', function($q) {
                return $q->where('membership_status', '=', 'Approved');
            })->where('email', '!=', Auth::user()->email)->paginate(10);
            return UserResource::collection($approved);
        }

        if (request()->query('pending') == 1) {
            $pending = User::whereHas('userProfile', function($q){
                $q->where('membership_status', '=', 'Pending');
            })->with('userProfile')->where('email', '!=', Auth::user()->email)->paginate(10);

            return UserResource::collection($pending);
        }
    }

    /**
     * Show photo of selected user
     * @return \Illuminate\Http\Response
     */
    public function photo($id)
    {
        $user = User::findOrFail($id);
        $user->userProfile;
        $surname = $user->userProfile->surname;
        $wiresign = $user->userProfile->wiresign;
        $licenseNumber = $user->userProfile->licenseNumber;

        $file = $surname . '-' . $wiresign . '-' . $licenseNumber . '.jpg';
        if (Storage::exists('users/' . $file)) {
            return response()->file(storage_path('app/users/' . $file));
        } else {
            abort(404);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->userProfile;
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->userProfile->membership_status = "Approved";
        return $user->userProfile->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['delete' => 'success', 'user' => $user]);
    }
}
