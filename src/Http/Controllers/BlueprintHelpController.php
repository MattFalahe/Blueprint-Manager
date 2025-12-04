<?php

namespace BlueprintManager\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;

class BlueprintHelpController extends Controller
{
    /**
     * Display the help and documentation page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('blueprint-manager::help.index');
    }
}
