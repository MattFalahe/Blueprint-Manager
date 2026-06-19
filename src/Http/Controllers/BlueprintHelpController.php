<?php

namespace BlueprintManager\Http\Controllers;

use BlueprintManager\Services\VersionChecker;
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
        // Installed-vs-latest status for the Version Status card. Cached +
        // timeout-guarded inside the service, so a Packagist hiccup never
        // blocks the Help page.
        $versionStatus = app(VersionChecker::class)->getStatus();

        return view('blueprint-manager::help.index', compact('versionStatus'));
    }
}
