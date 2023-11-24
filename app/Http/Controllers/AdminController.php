<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //เทส API สำหรับดู oldCase ใน database
    function oldCase(){
        $oldCase=DB::table('data_newer1')->get();
        return $oldCase;
    }

    function index()
    {
        return view('welcome');
    }

    function CBR_form()
    {
        return view('form');
    }

    function about() 
    {
        return view('about');
    }

    
}
