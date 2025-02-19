<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function changeLanguage($lang)
    {
        if(!in_array($lang,['en','fa'])){
            return response()->json(['message'=>'Invalid language'],400);
        }

        if(Auth::check()){
            Auth::user()->update(['locale'=>$lang]);
        }
        
        Session::put('locale',$lang);
        App::setlocale($lang);

        return response()->json(['messahe'=>'language changed']);
    }
}
