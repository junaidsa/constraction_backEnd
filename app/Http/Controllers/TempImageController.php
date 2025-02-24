<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,jpg',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first('image')
            ]);
        }
    
        $image = $request->file('image');
        if ($image) {
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext; 
            $model = new TempImage();
            $model->name = $imageName;
            $model->save();
    
            $sourcePath = public_path('/uploads/temp/' . $imageName);
            $disPath = public_path('/uploads/temp/thumb/' . $imageName);
            $image->move(public_path('/uploads/temp/'), $imageName);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($sourcePath);
            $image->coverDown(300, 300);
            $image->save($disPath);
    
            return response()->json([
                'status' => true,
                'message' => 'Temp image created successfully',
                'data' => $model->toArray()
            ], 200);
        }
    
        return response()->json([
            'status' => false,
            'error' => 'Image upload failed'
        ], 400);
    }
}
