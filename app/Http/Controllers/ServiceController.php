<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      try {
        $services = Service::orderBy('created_at', 'DESC')->get();
        return $this->json_response('success', 'Services', 'Services fetched successfully', 200, $services);
      } catch (\Exception $e) {
        return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
  }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|unique:services,slug|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        
            $data  =  [
                'title' => $request->title,
                'slug' => Str::slug($request->slug),
                'short_desc' => $request->short_desc,
                'content' => $request->content,
                'status' => $request->status,
            ];
            $service = Service::create($data);
            return $this->json_response('success', 'Service', 'Service created successfully', 200, $service->toArray());
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
        }        
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
