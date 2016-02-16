<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Domain;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DomainController
 * @package app\Http\Controllers
 *
 * @Middleware("Auth")
 */
class DomainController extends Controller
{
    protected $profile;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //$domains = Domain::withTrashed();
        $domains = Domain::all();

        return response()->view('domains.index', [
            'domains' => $domains,
        ]);
    }

    public function create()
    {
        return response()->view('domains.form');
    }

    public function destroy($id)
    {
        $domain = Domain::find($id);
        $request = new Request();

        try {
            $domain->delete();

            $request->session()->flash('success', 'Domain ' . $id . ' has been created!');
        } catch (Exception $e) {
            $request->session()->flash('danger', 'Domain ' . $id . ' was not created, please try again.');
        }

        return view('domains.index');
    }

    /**
     * Store a record
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $input = $request->only([
            'domain_name',
            'username',
            'password',
            'is_word_press',
        ]);

        try {
            $domain = Domain::create([
                'name'          => $input['domain_name'],
                'is_word_press' => $input['is_word_press'],
                'user_id'       => Auth::user()->id,
            ]);

            $request->session()->flash('success', $input['domain_name'] . ' has been created!');
        } catch (Exception $e) {
            $request->session()->flash('danger', $input['domain_name'] . ' was not created, please try again.');
        }

        return redirect()->route('domain.index');
    }
}