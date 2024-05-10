<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    

    public function index(Request $request)
    {
        // Define a cache key based on the request parameters
        $cacheKey = 'posts_' . serialize($request->all());
    
        // Check if the data is already cached
        if (Cache::has($cacheKey)) {
            // If cached, retrieve and return the cached data
            $posts = Cache::get($cacheKey);
        } else {
            // If not cached, fetch the posts from the database
            $sortBy = $request->query('sort_by', 'created_at');
            $sortDir = $request->query('sort_dir', 'desc');
            $adminId = $request->query('admin_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $searchQuery = $request->query('search');
            $perPage = $request->query('per_page', 10); // Default per page is 10
    
            $query = Post::orderBy($sortBy, $sortDir);
    
            if ($adminId) {
                $query->where('admin_id', $adminId);
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'like', '%' . $searchQuery . '%')
                      ->orWhere('body', 'like', '%' . $searchQuery . '%');
                });
            }
    
            $posts = $query->paginate($perPage);
    
            // Cache the fetched posts for 60 minutes (adjust as needed)
            Cache::put($cacheKey, $posts, 60); // Cache for 60 minutes
        }
    
        // Return the response
        return response()->json($posts);
    }
    
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000', // Example validation rules for images
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors and status code 422
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            
            // Validation passed, proceed with creating the post
            // Create a new post
            $post = new Post();
            
            $post->title = $request->title;
            $post->body = $request->body;
            $post->user_id = $request->user_id;
            $post->save();

            // Process and save the images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Generate a unique name for the image
                    $imageName = time() . '_' . $image->getClientOriginalName();

                    // Store the image with the custom name
                    $path = $image->storeAs('images', $imageName);

                    // Create a new Image record and associate it with the post
                    $post->images()->create([
                        'name' => $path,
                        // Add more image properties as needed
                    ]);
                }
            }

            // Return a JSON response with a success message and status code 201
            return response()->json(['message' => 'Post created successfully'], 201);
        } catch (QueryException $e) {
            // Return a JSON response with an error message if creating the post fails
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }
    
public function show($id)
{
    // Define cache key for the specific post
    $cacheKey = 'post_' . $id;

    // Check if the post data is already cached
    if (Cache::has($cacheKey)) {
        // If cached, retrieve and return the cached post data
        return Cache::get($cacheKey);
    }

    // If not cached, fetch the post from the database
    $post = Post::with('images')->find($id);

    // Cache the retrieved post data for 60 minutes (adjust as needed)
    Cache::put($cacheKey, $post, 60); // Cache for 60 minutes

    // Return the response
    return response()->json($post);
}

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000', // Example validation rules for images
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors and status code 422
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find the post by ID
            $post = Post::findOrFail($id);

            // Update the post attributes
            $post->title = $request->title;
            $post->body = $request->body;
            $post->user_id = $request->admin_id;
            $post->save();

            // Process and save the images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Generate a unique name for the image
                    $imageName = time() . '_' . $image->getClientOriginalName();

                    // Store the image with the custom name
                    $path = $image->storeAs('images', $imageName);

                    // Create a new Image record and associate it with the post
                    $post->images()->create([
                        'name' => $path,
                        // Add more image properties as needed
                    ]);
                }
            }

            // Return a JSON response with a success message and status code 200
            return response()->json(['message' => 'Post updated successfully'], 200);
        } catch (QueryException $e) {
            // Return a JSON response with an error message if updating the post fails
            return response()->json(['error' => 'Failed to update post'], 500);
        }
    }

    
public function destroy($id)
{
    try {
        // Find the post by ID
        $post = Post::findOrFail($id);
        
        // Retrieve all images associated with the post
        $images = $post->images;
        
        // Delete the corresponding image files from storage
        // foreach ($images as $image) {
        //     Storage::delete($image->name);
        // }

        // Soft delete the post
        $post->delete();

        // Return a JSON response with a success message
        return response()->json(['message' => 'Post and associated images deleted successfully'], 200);
    } catch (\Exception $e) {
        // Return a JSON response with an error message if deletion fails
        return response()->json(['error' => 'Failed to delete post and associated images'], 500);
    }
}
    public function deleteImage($postId, $imageId)
{
    try {
        // Find the post by ID
        $post = Post::findOrFail($postId);
        
        // Find the image by ID and delete it
        $image = $post->images()->findOrFail($imageId);
        Storage::delete($image->name);

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete image'], 500);
    }
}


}
