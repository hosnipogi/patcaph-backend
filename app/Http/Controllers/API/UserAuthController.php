<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{

    private $auth;
    public function __construct(Auth $auth) {
        $this->middleware(function($request, $next) use ($auth) {
            $this->auth = $auth::user();
            return $next($request);
        });
    }

    public function main() {
            if ($auth = $this->auth) {
                $member['hasProfile'] = $auth->userProfile !== null ? true : false;
                $member['email'] = $auth->email;
            return $member;
            }
        return response(0);
    }

    public function dashboard() {
        $auth = $this->auth;
        $profile = $auth->userProfile;
        $name = $profile->firstname . ' ' . $profile->surname;
        $user['email'] = $auth->email;
        $user['name'] = $name;
        $user['role'] = $auth->getRoleNames();
        return $user;
    }

    public function photo() {
        $auth = $this->auth->userProfile;
        $surname = $auth->surname;
        $wiresign = $auth->wiresign;
        $licenseNumber = $auth->licenseNumber;
        $file = $surname . '-' . $wiresign . '-' . $licenseNumber . '.jpg';
        if (Storage::exists('users/' . $file)) {
            return response()->file(storage_path('app/users/' . $file), ['Cache-Control' => 'no-store']);
        } else {
            abort(404);
        }
    }

    public function profile() {
        return  $this->auth->userProfile;
    }
}
