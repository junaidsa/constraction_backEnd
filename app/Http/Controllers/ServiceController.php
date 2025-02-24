<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // Import Laravel's File facade
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


// Use Intervention Image to resize images
// composer require intervention/image:
// 
class ServiceController extends Controller
{
    public function index()
    {
        try {
            $services = Service::orderBy('created_at', 'DESC')->get();
            return $this->json_response('success', 'Services', 'Services fetched successfully', 200, $services);
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
        }
    }
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
                'slug' =>'required|string|unique:services,slug|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // Handle image upload
            $service = new Service();
                $service->title = $request->title;
                $service->slug = Str::slug($request->slug);
                $service->short_desc = $request->short_desc;
                $service->content = $request->content;
                $service->status = $request->status;
                $service->save();

                // Handle image upload
                if ($request->imageId > 0) {
                    // Delete the temp image
                    $tempImage = TempImage::find($request->imageId);
                    $tempImage = TempImage::find($request->imageId);
                    if (!$tempImage || !file_exists(public_path('/uploads/temp/'.$tempImage->name))) {
                        return response()->json(['error' => 'Temp image not found'], 400);
                    }
                    
                    $extArray = explode('.', $tempImage->name);
                    $ext = end($extArray);
                    $filename = Str::slug($service->title) . '-' . time() . '.' . $ext;
                    // Create two size images for the service large and small sizes
                    $sourcePath = public_path('/uploads/temp/' . $tempImage->name);
                    $smallPath = public_path('/uploads/services/small/' . $filename);
                    $largePath = public_path('/uploads/services/large/' . $filename);
                    
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($sourcePath);
                    
                    if (!$image) {
                        return response()->json(['error' => 'Failed to read image'], 500);
                    }
                    // Resize the image to the small sizes
                    $image->coverDown(500, 600);
                    $image->save($smallPath);
                    // Resize the image to the large sizes
                    $image->scaleDown(1200);
                    $image->save($largePath);
                    $service->image = $filename;
                    $service->save();
                    // Delete the temp image
                    if ($tempImage) {
                        File::delete(public_path('/uploads/temp/'.$tempImage->name));
                        TempImage::destroy($request->imageId);
                    }
                    }
            return $this->json_response('success', 'Service', 'Service created successfully', 200, $service->toArray());
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return $this->json_response('success', 'Service', 'Service Not Found', 404);
            }
            return $this->json_response('success', 'Service', 'Service fetched successfully', 200, $service->toArray());
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
        }
    }
    public function edit(Service $service) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return $this->json_response('error', 'Service', 'Service Not Found', 404);
            }
    
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $service->title = $request->title;
            $service->slug = Str::slug($request->slug);
            $service->short_desc = $request->short_desc;
            $service->content = $request->content;
            $service->status = $request->status;
            $service->save();
            // i have to handle the image upload here if the image is provided this is a image temp image
            // i pass image id from the request and update the service image
            // after that i delete the temp image
            if ($request->imageId > 0) {
                // Delete the temp image
                $tempImage = TempImage::find($request->imageId);
                $oldimage = $service->image;
                $tempImage = TempImage::find($request->imageId);
                if (!$tempImage || !file_exists(public_path('/uploads/temp/'.$tempImage->name))) {
                    return response()->json(['error' => 'Temp image not found'], 400);
                }
                
                $extArray = explode('.', $tempImage->name);
                $ext = end($extArray);
                $filename = Str::slug($service->title) . '-' . time() . '.' . $ext;
                // Create two size images for the service large and small sizes
                $sourcePath = public_path('/uploads/temp/' . $tempImage->name);
                $smallPath = public_path('/uploads/services/small/' . $filename);
                $largePath = public_path('/uploads/services/large/' . $filename);
                
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sourcePath);
                
                if (!$image) {
                    return response()->json(['error' => 'Failed to read image'], 500);
                }
                // Resize the image to the small sizes
                $image->coverDown(500, 600);
                $image->save($smallPath);
                // Resize the image to the large sizes
                $image->scaleDown(1200);
                $image->save($largePath);
                $service->image = $filename;
                $service->save();
                // Delete the previce image
                if ($oldimage) {
                    File::delete(public_path('/uploads/services/large/'.$oldimage));
                    File::delete(public_path('/uploads/services/small/'.$oldimage));
                }
                // Delete the temp image
                if ($tempImage) {
                    File::delete(public_path('/uploads/temp/'.$tempImage->name));
                    TempImage::destroy($request->imageId);
                }

            }
            return $this->json_response('success', 'Service', 'Service updated successfully', 200, $service->toArray());
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return $this->json_response('success', 'Service', 'Service Not Found', 404);
            }
            $service->delete();
            return $this->json_response('success', 'Service', 'Service deleted successfully', 200, $service);
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
        }
    }

    public function getServices(Request $request){
try {
    $limit = $request->get('limit');
    $services = Service::active()
        ->orderBy('created_at', 'DESC')
        ->when($limit, function ($query, $limit) {
            return $query->take($limit);
        })->get();
    if (!$services) {
        return $this->json_response('success', 'Service', 'No Services found', 404);
    }
    return $this->json_response('success', 'Service', 'Services get successfully', 200,$services);

} catch (\Exception $e) {
    return response()->json(['error' =>  $e->getMessage(), 'line' => $e->getLine(), 'File' => $e->getFile()], 500);
}
    }
}
