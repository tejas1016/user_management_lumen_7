<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userReq = $request->only(['first_name', 'last_name', 'email', 'phone']);
        $user = User::create($userReq);
        if ($user) {
            return response()->json(array("status" => 201, "errormsg" => 'Success'));
        }
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json(array("status" => 200, "data" => $user));
        } else {
            return response()->json(array("status" => 204, "errormsg" => "Data not found."));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userReq = $request->only(['first_name', 'last_name', 'email', 'phone']);
        $user = User::where(['id' => $id])->update($userReq);
        if ($user) {
            return response()->json(array("status" => 200, "errormsg" => "Success"));
        } else {
            return response()->json(array("status" => 204, "errormsg" => "Data not found."));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::where(['id' => $id])->delete();
        if ($user) {
            return response()->json(array("status" => 200, "errormsg" => "Success"));
        } else {
            return response()->json(array("status" => 204, "errormsg" => "Data not found."));
        }
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = DB::table('users')->where(['deleted_at' => null])
            ->where(function ($query) use ($request) {
                if ($request->has(['search_type', 'search_by']) && $request->filled(['search_type', 'search_by'])) {
                    switch ($request->input('search_type')) {
                        case 1:
                            $query->where('users.first_name', 'like', '%' . $request->input('search_by') . '%');
                            break;
                        case 2:
                            $query->where('users.last_name', 'like', '%' . $request->input('search_by') . '%');
                            break;
                        case 3:
                            $query->where('users.email', 'like', '%' . $request->input('search_by') . '%');
                            break;
                        case 4:
                            $query->where('users.phone', 'like', '%' . $request->input('search_by') . '%');
                            break;
                    }
                }
            });
        $users = $users->get();
        if (count($users) > 0) {
            return response()->json(array("status" => 200, "data" => $users));
        } else {
            return response()->json(array("status" => 204, "errormsg" => "Data not found."));
        }
    }

}
