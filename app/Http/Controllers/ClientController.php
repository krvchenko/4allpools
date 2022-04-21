<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
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

		$query = Client::joinManager()->with('manager');

		if ($filter = json_decode($request->filter)) {
			$query = $query->filter($filter->column, $filter->value);
		}

		if ($search = $request->search) {
			$query = $query->search($search);
		}

		$query = $query->orderBy($sort, $order);

		$clients = $perPage ? $query->paginate($perPage) : $query->get();

		return response()->json($clients, 200);
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
		if ($client = Client::with('manager')->find($id)) {
			return response()->json($client, 200);
		}

		return response()->json([
			'status' => 404,
			'message' => 'Клиент не найден',
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
			'email' => 'required|email|unique:clients,email,'.$id,
			'name' => 'required|max:255',
		]);

		if ($client = Client::find($id)) {
			$client->update($request->except([]));
			return response()->json($client->load(['manager']), 200);
		}

		return response()->json([
			'status' => 401,
			'message' => 'Клиент не найден',
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
		if ($client = Client::find($id)) {
			$client->delete();
			return response()->json($client, 200);
		}

		return response()->json([
			'status' => 401,
			'message' => 'Клиент не найден',
		], 401);
	}
}
