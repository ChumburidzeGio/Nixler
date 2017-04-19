<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Newsletter;

class NewsletterController extends Controller
{
    /**
     * Subscribe for newsletter
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        if($request->email){
            Newsletter::subscribeOrUpdate($request->email);
        }
        
        return back()->with('subscribe', 'Thank you, we will notice you soon !');
    }
}
