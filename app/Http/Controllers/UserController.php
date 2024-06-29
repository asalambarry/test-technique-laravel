<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = User::with('services');

        if ($request->has('service')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('name', $request->service);
            });
        }

        if ($request->has('sort_by')) {
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($request->sort_by, $sortOrder);
        }

        $users = $query->paginate(10);

        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('services')->findOrFail($id);
        return response()->json($user);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'position' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user->update($request->all());

        if ($request->has('services')) {
            $user->services()->sync($request->services);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
