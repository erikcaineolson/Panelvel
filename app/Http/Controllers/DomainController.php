<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
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
        //
    }

    public function create()
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function store()
    {
        //
    }

    public function update($id)
    {
        //
    }
}