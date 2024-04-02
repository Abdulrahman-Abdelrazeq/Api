<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $images = Image::all();
        return response()->json($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $property_id = $request->input('property_id');
        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp', // Assuming maximum file size is 2MB (2048 KB)
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $image) {
            $url = $image->getClientOriginalName();
            $image->move(public_path('images'), $url);

            $image = new Image ();
            $image->url = $url;
            $image->property_id = $property_id;
            $image->save();
            // $uploadedImages[] = Image::create(['url' => $url, 'property_id' => $property_id]);
        }

        // if (count($uploadedImages) > 0) {
        //     return response()->json(['success' => true, 'message' => 'Images uploaded successfully', 'images' => $uploadedImages]);
        // } else {
        //     return response()->json(['success' => false, 'message' => 'No images uploaded', 'images' => []]);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
        return $image;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
        $request->validate([
            'new_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validate only if new image is provided
        ]);

        // Check if a new image file is uploaded
        if ($request->hasFile('new_image')) {
            // Delete the previous image file
            if (File::exists(public_path('images/' . $image->url))) {
                File::delete(public_path('images/' . $image->url));
            }

            // Store the new image file in the specified destination folder
            $url = time() . '_' . $request->file('new_image')->getClientOriginalName();
            $request->file('new_image')->move(public_path('images'), $url);

            // Update the image url in the database
            $image->url = $url;
        }

        $result = $image->save();

        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //

        $image->delete();
        return response()->json(['message' => 'image deleted successfully']);
    }
}
