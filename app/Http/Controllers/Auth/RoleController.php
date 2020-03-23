<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'role_or_permission:super-admin|admin|users']);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
        return auth()->user()->getRoleNames();
    }

    public function get(Request $request)
    {
        if (auth()->user()->hasRole('super-admin'))
            $roles = Role::all();
        else $roles = Role::where('name', '<>', 'super-admin')->get();

        return response($roles);
    }
}
