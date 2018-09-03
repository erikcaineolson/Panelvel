<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Subdirectory;
use Illuminate\Http\Request;

use App\Http\Requests;

class SubdirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('subdirectories.index', [
            'subdirectories' => Subdirectory::withTrashed()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('subdirectories.form', [
            'domains' => Domain::all()->sort(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Subdirectory $subdirectory
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Subdirectory $subdirectory)
    {
        return response()->view('subdirectories.details', [
            'subdirectory' => $subdirectory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Subdirectory $subdirectory
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Subdirectory $subdirectory)
    {
        return response()->view('subdirectories.form', [
            'subdirectory' => $subdirectory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Subdirectory             $subdirectory
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subdirectory $subdirectory)
    {
        return response()->redirectToRoute('subdirectory.index')->with('error', 'Updates not yet supported.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Subdirectory $subdirectory
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subdirectory $subdirectory)
    {
        return response()->redirectToRoute('subdirectory.index')->with('error', 'Deletes not yet supported.');
    }
}
