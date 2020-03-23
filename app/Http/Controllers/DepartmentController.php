<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public $company_id;

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role_or_permission:super-admin|admin|departments|users']);
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
        $items = Department::where('company_id', $this->company_id)->get()->toTree();

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
        $item = Department::create($request->all());

        return response(['message' => __('app.successfully_save')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $company_id = auth()->user()->company_id;

        $item = $department->update($request->all());

        return response(['message' => __('app.successfully_save')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
        $department->delete();

        return response(['message' => __('app.successfully_delete')], 200);
    }
}
