<?php namespace App\Http\Controllers;

use App\Models\Domain;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * Class DomainController
 * @package App\Http\Controllers
 *
 * @Middleware("Auth")
 */
class DomainController extends Controller
{
    protected $profile;

    /**
     * DomainController constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all records (include soft-deleted records)
     *
     * @return Response
     */
    public function index()
    {
        $domains = Domain::withTrashed()->get();

        return response()->view('domains.index', [
            'domains' => $domains,
        ]);
    }

    /**
     * Display the create form
     *
     * @return Response
     */
    public function create()
    {
        return response()->view('domains.form');
    }

    /**
     * Delete a record
     *
     * @param $id
     *
     * @return Response
     */
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
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'domain_name' => 'required|unique:name|min:4|max:255',
        ]);

        $input = $request->only([
            'domain_name',
            'is_word_press',
            'is_secure',
        ]);

        $password = str_random(12);

        try {
            Domain::create([
                'name'          => $input['domain_name'],
                'is_word_press' => $input['is_word_press'],
                'username'      => $input['domain_name'],
                'password'      => $password,
                'user_id'       => Auth::user()->id,
                'is_secure'     => $input['is_secure'],
            ]);

            $request->session()->flash('success', $input['domain_name'] . ' has been created!');

            Artisan::call('install-site');
        } catch (Exception $e) {
            $request->session()->flash('danger', $input['domain_name'] . ' was not created, please try again.');
        }

        return redirect()->route('domain.index');
    }
}
