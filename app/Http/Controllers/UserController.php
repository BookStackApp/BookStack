<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Oxbow\Http\Requests;
use Oxbow\Http\Controllers\Controller;
use Oxbow\User;

class UserController extends Controller
{

    protected $user;

    /**
     * UserController constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * Display a listing of the users.
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->user->all();
        return view('users/index', ['users'=> $users]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create()
    {
        return view('users/create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
            'password-confirm' => 'required|same:password'
        ]);

        $user = $this->user->fill($request->all());
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return redirect('/users');
    }


    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->user->findOrFail($id);
        return view('users/edit', ['user' => $user]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'min:5',
            'password-confirm' => 'same:password'
        ]);

        $user = $this->user->findOrFail($id);
        $user->fill($request->all());

        if($request->has('password') && $request->get('password') != '') {
            $password = $request->get('password');
            $user->password = Hash::make($password);
        }
        $user->save();
        return redirect('/users');
    }

    /**
     * Show the user delete page.
     * @param $id
     * @return \Illuminate\View\View
     */
    public function delete($id)
    {
        $user = $this->user->findOrFail($id);
        return view('users/delete', ['user' => $user]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->user->findOrFail($id);
        $user->delete();
        return redirect('/users');
    }
}
