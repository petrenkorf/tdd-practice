<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Breadcrumbs;

class AdministratorController extends Controller
{
    //
    public function edit($id)
    {
        $breadcrumbs = new Breadcrumbs(
            app()['request'],
            app()['config'],
            app()['router']
        );

        return dd($breadcrumbs->getLinks());
    }
}
