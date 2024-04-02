<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
//  require_once(__DIR__.'/Controller.php');
use App\Http\Requests\CreateUserRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        // return view('users.index', ['users' => $users]);
        return $users;
    }
    public function show(user $user)
    {
        return $user;
    }



    public function update(Request $request, User $user)
    {
        $userData = $request->only(['name', 'email']);

        // Check if password is provided and validate it
        if ($request->has('password')) {
            $passwordValidator = Validator::make($request->all(), [
                'password' => 'required|min:8',
            ]);

            if ($passwordValidator->fails()) {
                return response($passwordValidator->errors()->all(), 422);
            }

            $userData['password'] = Hash::make($request->input('password')); // Use Hash::make() function
        }

        // Validate name and email fields if they are provided
        $validator = Validator::make($userData, [
            'name' => 'sometimes|required|min:3', // Use 'sometimes' to allow optional field
            'email' => 'sometimes|required|email', // Use 'sometimes' to allow optional field
        ]);

        if ($validator->fails()) {
            return response($validator->errors()->all(), 422);
        }

        // Update only the allowed fields
        $user->update($userData);

        return response($user->fresh(), 200);
    }

    




    public function destroy(user $user)
    {

        $user->delete();
        return response('User deleted successfully.', 204);

        // return  'User deleted successfully.';

   }
}
