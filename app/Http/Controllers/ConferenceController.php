<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Committee;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConferenceController extends Controller
{

    //conf title list
    public function index(Request $request){
        try {
            $conferenceTitles = Conference::select('title')->get();
            // dd($conferences[0]->title);
            return response()->json([
                'conferenceTitles'=>$conferenceTitles,
            ]);
        } catch (Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json(['error' => 'Failed to fetch conferences.'], 500);
        }
    }

    // conf detail
    public function show($id){
    $cacheKey = 'conference_' . $id;
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    }
    $conference = Conference::with('committee','conferenceimage')->find($id);
    // dd($conference->conferenceimage[0]->name);
    Cache::put($cacheKey, $conference, 60);
    // Return the response
    return response()->json($conference);
    }


    /////////create
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            // 'committee'=>'required',
            'title'=>'required',
            'conference_date'=>'required',
            'description'=>'required',
            'topics'=>'required',
            'general_chair'=>'required',
            'co_chair'=>'required',
            'program_chair'=>'required',
            'paper_sub_guide'=>'required',
            'sub_deadline'=>'required',
            // 'updated_sub_deadline'=>'required',
            'accept_noti'=>'required',
            'normal_fee'=>'required',
            'early_bird_fee'=>'required',
            'local_fee'=>'required',
            'sub_email'=>'required',
            'camera_ready'=>'required',
            'brochure'=>'required',
            'book'=>'required'
        ]);

        if ($validator->fails()) {
            // Return a JSON response with validation errors and status code 422
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $conf = new Conference();
            
            $conf->title = $request->title;
            $conf->conference_date = $request->conference_date;
            $conf->description = $request->description;
            $conf->description = $request->description; 
            $conf->topics = $request->topics;
            $conf->general_chair = $request->general_chair;
            $conf->co_chair = $request->co_chair;
            $conf->program_chair = $request->program_chair;
            $conf->paper_sub_guide = $request->paper_sub_guide;
            $conf->updated_sub_deadline = $request->updated_sub_deadline;
            $conf->accept_noti = $request->accept_noti;
            $conf->normal_fee = $request->normal_fee;
            $conf->early_bird_fee = $request->early_bird_fee;
            $conf->local_fee = $request->local_fee;
            $conf->sub_email = $request->sub_email;
            $conf->camera_ready = $request->camera_ready;
            $conf->brochure = $request->brochure;
            $conf->book = $request->book;
            $conf->save();

            foreach($conf->committee as $c){
                $com = new Committee();
                $com->name = $c->name;
                $com->rank = $c->rank;
                $com->university = $c->university;
                $com->nation = $c->nation;
                $com->type = $c->type;
                $com->conference_id = $conf->title; // get conf title
                $com->save();
            }
   
            // Process and save the images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Generate a unique name for the image
                    $imageName = time() . '_' . $image->getClientOriginalName();

                    // Store the image with the custom name
                    $path = $image->storeAs('images', $imageName);

                    // Create a new Image record and associate it with the post
                    $conf->conferenceimage()->create([
                        'name' => $path,
                        // Add more image properties as needed
                    ]);
                }
            }

            // Return a JSON response with a success message and status code 201
            return response()->json(['message' => 'conference created successfully'], 201);
        } catch (QueryException $e) {
            // Return a JSON response with an error message if creating the post fails
            return response()->json(['error' => 'Failed to create conference'], 500);
        }
    }


    public function destroy($id)
        {
            try {
                $conf = Conference::findOrFail($id);
                $post->delete();
                // Return a JSON response with a success message
                return response()->json(['message' => 'Post and associated images deleted successfully'], 200);
            } catch (\Exception $e) {
                // Return a JSON response with an error message if deletion fails
                return response()->json(['error' => 'Failed to delete post and associated images'], 500);
            }
        }

        public function deleteImage($confId, $imageId)
        {
            try {
                // Find the post by ID
                $conf = Conference::findOrFail($confId);
                
                // Find the image by ID and delete it
                $image = $conf->images()->findOrFail($imageId);
                Storage::delete($image->name);

                $image->delete();

                return response()->json(['message' => 'Image deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to delete image'], 500);
            }
        }

        public function update(Request $request, $id)
        {
            $validator = Validator::make($request->all(), [
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
                // 'committee'=>'required',
                'title'=>'required',
                'conference_date'=>'required',
                'description'=>'required',
                'topics'=>'required',
                'general_chair'=>'required',
                'co_chair'=>'required',
                'program_chair'=>'required',
                'paper_sub_guide'=>'required',
                'sub_deadline'=>'required',
                // 'updated_sub_deadline'=>'required',
                'accept_noti'=>'required',
                'normal_fee'=>'required',
                'early_bird_fee'=>'required',
                'local_fee'=>'required',
                'sub_email'=>'required',
                'camera_ready'=>'required',
                'brochure'=>'required',
                'book'=>'required'
            ]);
    
            // Check if validation fails
            if ($validator->fails()) {
                // Return a JSON response with validation errors and status code 422
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            try {
                // Find the post by ID
                $conf = Post::findOrFail($id);

                $conf->title = $request->title;
                $conf->conference_date = $request->conference_date;
                $conf->description = $request->description;
                $conf->description = $request->description; 
                $conf->topics = $request->topics;
                $conf->general_chair = $request->general_chair;
                $conf->co_chair = $request->co_chair;
                $conf->program_chair = $request->program_chair;
                $conf->paper_sub_guide = $request->paper_sub_guide;
                $conf->updated_sub_deadline = $request->updated_sub_deadline;
                $conf->accept_noti = $request->accept_noti;
                $conf->normal_fee = $request->normal_fee;
                $conf->early_bird_fee = $request->early_bird_fee;
                $conf->local_fee = $request->local_fee;
                $conf->sub_email = $request->sub_email;
                $conf->camera_ready = $request->camera_ready;
                $conf->brochure = $request->brochure;
                $conf->book = $request->book;
                $conf->save();
                
                ////committee
                // $conf->committee()->name = $request->committee_name;
                // $conf->committee()->rank = $request->committee_rank;
                // $conf->committee()->university = $request->committee_university;
                // $conf->committee()->nation = $request->committee_nation;
                // $conf->committee()->type = $request->committee_type;
                
    
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
}
