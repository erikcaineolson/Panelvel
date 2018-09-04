<?php namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Subdirectory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
            'domains' => Domain::withTrashed()->get()->sort(),
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
        $input = $request->only([
            'domain_id',
            'subdirectory_name',
            'is_word_press',
        ]);

        try {
            Subdirectory::create([
                'domain_id'     => $input['domain_id'],
                'moniker'       => $input['subdirectory_name'],
                'is_word_press' => $input['is_word_press'],
            ]);

            $request->session()->flash('success', $input['subdirectory_name'] . ' has been successfully created.');

            Artisan::call('install-subdirectory');
        } catch (Exception $e) {
            $request->session()->flash('error', $input['subdirectory_name'] . ' was not created, please try again.');
        }

        return response()->redirectToRoute('subdirectory.index');
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
        $request = new Request();

        try {
            $subdirectory->delete();
            $request->flash('success', 'Subdirectory ' . $subdirectory->id . ' has been deleted.');
        } catch (Exception $e) {
            $request->flash('error', 'Subdirectory ' . $subdirectory->id . ' was not deleted, please try again.');
        }

        return response()->redirectToRoute('subdirectory.index');
    }
}
