@component('mail::message')
# New Message for PATCA admins

From: {{$form['email']}}<br>
Name: {{$form['name'] ? $form['name'] : '(Anonymous)'}}<br>
Subject: {{$form['subject'] ? $form['subject'] : '(No Subject)'}}<br>
<br>
Message: {{$form['message']}}

@endcomponent
