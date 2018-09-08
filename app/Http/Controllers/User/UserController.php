<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);

        $fields = $request->all();
        $fields['password'] = bcrypt($request->password);
        $fields['verified'] = User::USER_NOT_VERIFICATED;
        $fields['verifiication_token'] = User::generateVerificateToken();
        $fields['admin'] = User::USER_REGULAR;

        $user = User::create($fields);

        return response()->json(['data' => $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['data' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::USER_ADMIN . ',' . User::USER_REGULAR,
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $request->email != $user->email) 
        {
            $user->verified = User::USER_NOT_VERIFICATED;
            $user->verification_token = User::generateVerificateToken();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {
            if (!$user->idAdmin()) {
                return response()->json(['error' => 'Unicamente los usuarios verificados pueden cambiar su valor de administrador', 'code' => 409], 409);
            }
            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return response()->json(['Debes de especificar almenos algun valor diferente para hacer la actualizacion', 'code' => 422], 422);
        }

        $user->save();

        return response()->json(['data' => $user], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['data' => $user], 200);
    }
}
