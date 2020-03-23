<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $company_id;

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super-admin|admin']);
        $this->middleware(function ($request, $next) {
            $this->company_id = auth()->user()->company_id;
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = User::where('company_id', $this->company_id);
        if (!request()->user()->hasRole('super-admin')) {
            $items = $items->role(['admin', 'user']);
        }
        $items = $items->get();

        if (!request()->expectsJson()) {
            return view('index');
        }

        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['company_id' => $this->company_id]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['string', 'min:6', 'confirmed'],
            'department_id' => ['required'],
        ]);
        if (!empty($request->password)) {
            $request->merge(['password' => bcrypt($request->password)]);
        }

        event(new Registered($user = User::create($request->all())));
        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        return response(['message' => __('app.successfully_save')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (!empty($request->password)) {
            $request->validate([
                'password' => ['string', 'min:6', 'confirmed'],
            ]);
            $request->merge(['password' => bcrypt($request->password)]);
        }
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        if ($request->has(['name', 'email'])) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ]);
        }

        $item = $user->update($request->all());

        return response(['message' => __('app.successfully_save')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        $user->delete();

        return response(['message' => __('app.successfully_delete')], 200);
    }
}
