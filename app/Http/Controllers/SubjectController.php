<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

use HTMLPurifier;
use HTMLPurifier_Config;


class SubjectController extends Controller
{
    public function index (Request $request)
    {
        $cacheKey = 'subjects_' . serialize($request->all());
        if (Cache::has($cacheKey)) {
            $subjects = Cache::get($cacheKey);
        } else {
            $sortBy = $request->query('sort_by', 'id');
            $sortDir = $request->query('sort_dir','asc');
            $faculty = $request->query('faculty');
            $query = Subject::orderBy($sortBy, $sortDir);
            $perPage = $request->query('per_page', 10); // Default per page is 10
            if($faculty) {
                $query = $query->where('faculty', $faculty);
            }
            $subjects = $query->paginate($perPage);

            Cache::put($cacheKey, $subjects, 60); 
        }

    
        return response()->json($subjects);
    }
    
    public function store(Request $request)
    {
    // Define validation rules
    $validator = Validator::make($request->all(), [
        'subject_code' => 'required|string',
        'name' => 'required|string',
        'faculty' => 'required|string',
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // Sanitize input using HTML Purifier
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifierConfig);

        // Sanitize each input field
        $subjectCode = $purifier->purify($request->subject_code);
        $name = $purifier->purify($request->name);
        $faculty = $purifier->purify($request->faculty);

        // Create a new Subject instance
        $subject = new Subject();
        // Assign sanitized input data to model properties
        $subject->subject_code = $subjectCode;
        $subject->name = $name;
        $subject->faculty = $faculty;
        // Save the subject to the database
        $subject->save();

        // Return success message
        return response()->json(['message' => 'Subject created'], 201);
    } catch (QueryException $e) {
        // If an exception occurs, return an error response
        return response()->json(['error' => 'Failed to create Subject'], 500);
    }
}

    public function show($id)
    {
        $cacheKey = 'subject_' . $id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        try {
            
            $subject = Subject::find($id);
    
            Cache::put($cacheKey, $subject, 60); 
    
            return response()->json($subject);
        } catch (ModelNotFoundException $e) {
            
            return response()->json(['error' => 'Subject not found'], 404);
        }
    }
    public function update(Request $request, $id)
{
    // Define validation rules
    $validator = Validator::make($request->all(), [
        'subject_code' => 'required|string',
        'name' => 'required|string',
        'faculty' => 'required|string',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // Sanitize input using HTML Purifier
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($purifierConfig);

        // Sanitize each input field
        $subjectCode = $purifier->purify($request->subject_code);
        $name = $purifier->purify($request->name);
        $faculty = $purifier->purify($request->faculty);

        // Find the existing subject by id
        $subject = Subject::findOrFail($id);
        
        // Assign sanitized input data to the model properties
        $subject->subject_code = $subjectCode;
        $subject->name = $name;
        $subject->faculty = $faculty;

        // Save the updated subject
        $subject->save();

        // Return success message
        return response()->json(['message' => 'Subject updated successfully'], 200);
    } catch (QueryException $e) {
        // If an exception occurs during database operation, return an error
        return response()->json(['error' => 'Failed to update post'], 500);
    }
}
    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();
            return response()->json(['message' => 'Subject deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete subject'], 500);
        }
    }

}