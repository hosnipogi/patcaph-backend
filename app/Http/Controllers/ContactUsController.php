<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        if (!Auth::check()) {
            $validated = $request->validate([
                "email"  => "required|email",
                "name"  => "max:255",
                "subject"   => "max:255",
                "message"   => "required"
            ]);
        } else {
            $auth = Auth::user();
            $validated = $request->validate([
                "subject" => 'max:255',
                "message" => 'required'
            ]);

            $firstname = $auth->userProfile ? $auth->userProfile->firstname : '(No specified)';
            $surname = $auth->userProfile ? $auth->userProfile->surname : '(Not specified)';
            $fullname = $firstname . ' ' . $surname;
            $validated['email'] = $auth->email;
            $validated['name'] = $fullname;
        }

        Mail::to(config('mail.defaultMailToAddress'))->send(new ContactUsMail($validated));
        return response('Message sent', 200);
    }
}
