<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManagerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$perPage = $request->per_page;
		$sort = $request->has('sort') ? $request->sort : 'id';
		$order = $request->has('order') ? $request->order : 'desc';

		$query = User::query();

		if ($filter = json_decode($request->filter)) {
			$query = $query->filter($filter->column, $filter->value);
		}

		if ($search = $request->search) {
			$query = $query->search($search);
		}

		$query = $query->orderBy($sort, $order);

		$users = $perPage ? $query->paginate($perPage) : $query->get();

		return response()->json($users, 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if ($user = User::find($id)) {
			return response()->json($user, 200);
		}

		return response()->json([
			'status' => 404,
			'message' => 'Пользователь не найден',
		], 404);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
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
		$this->validate($request, [
			'email' => 'required|email|unique:users,email,'.$id,
			'name' => 'required|max:255',
		]);

		if ($user = User::find($id)) {
			$user->update($request->except([]));
			return response()->json($user, 200);
		}

		return response()->json([
			'status' => 401,
			'message' => 'Пользователь не найден',
		], 401);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if ($user = User::find($id)) {
			$user->delete();
			return response()->json($user, 200);
		}

		return response()->json([
			'status' => 401,
			'message' => 'Пользователь не найден',
		], 401);
	}
}
