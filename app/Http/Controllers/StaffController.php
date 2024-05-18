<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\ResearchInterest;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use HTMLPurifier;
use HTMLPurifier_Config;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'staff_' . serialize($request->all());

        if (Cache::has($cacheKey)) {
            $staff = Cache::get($cacheKey);
        } else {
            $sortBy = $request->query('sort_by', 'id');
            $sortDir = $request->query('sort_dir', 'asc');
            $staffId = $request->query('staff_id');
            $faculty = $request->query('faculty');
            $query = Staff::orderBy($sortBy, $sortDir);

            if ($staffId) {
                $query->where('staff_id', $staffId);
            }
            if ($faculty) {
                $query->whereHas('subjects', function ($q) use ($faculty) {
                    $q->where('faculty', $faculty);
                });
            }
            
            // Eager load publications and research interests
            $staff = $query->with('publications', 'researchInterests')->paginate(10);

            Cache::put($cacheKey, $staff, 60);
        }
        return response()->json($staff);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'name' => 'required|string',
            'position' => 'required|string',
            'biography' => 'required|string',
            'education' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);

            $staffName = $purifier->purify($request->name);
            $position = $purifier->purify($request->position);
            $biography = $purifier->purify($request->biography);
            $education = $purifier->purify($request->education);

            $staff = new Staff();
            $staff->name = $staffName;
            $staff->position = $position;
            $staff->biography = $biography;
            $staff->education = $education;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('images', $imageName);
                $staff->image = $path; // Store the path in the image column
            }

            $staff->save();

            return response()->json(['message' => 'Staff created successfully'], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create Staff: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a new publication.
     */
    public function storePublication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publications' => 'required|string',
            'staff_id' => 'required|integer|exists:staff,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);

            $publications = $purifier->purify($request->publications);
            $staffId = $purifier->purify($request->staff_id);

            $publication = new Publication();
            $publication->publications = $publications;
            $publication->staff_id = $staffId; 
            $publication->save();

            return response()->json(['message' => 'Publication created successfully'], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create Publication: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a new research interest.
     */
    public function storeResearchInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'research' => 'required|string',
            'staff_id' => 'required|integer|exists:staff,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);

            $research = $purifier->purify($request->research);
            $staffId = $purifier->purify($request->staff_id);

            $researchInterest = new ResearchInterest();
            $researchInterest->research = $research;
            $researchInterest->staff_id = $staffId; 
            $researchInterest->save();

            return response()->json(['message' => 'Research Interest created successfully'], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create Research Interest: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = 'staff_' . $id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $staff = Staff::with('image')->find($id);
            $publication = Publication::where('staff_id', $id)->get();
            $researchInterest = ResearchInterest::where('staff_id', $id)->get();

            $data = [
                'staff' => $staff,
                'publication' => $publication,
                'researchInterest' => $researchInterest,
            ];

            Cache::put($cacheKey, $data, 60);

            return response()->json($data);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Staff not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStaff(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'name' => 'required|string',
            'position' => 'required|string',
            'biography' => 'required|string',
            'education' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);
            $staff = Staff::findOrFail($id);

            // Update fields
            $staff->update([
                'name' => $purifier->purify($request->name),
                'position' => $purifier->purify($request->position),
                'biography' => $purifier->purify($request->biography),
                'education' => $purifier->purify($request->education)
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($staff->image) {
                    Storage::delete($staff->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('images', $imageName);
                $staff->image = $path;
            }

            $staff->save();

            return response()->json(['message' => 'Staff updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Staff not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update staff: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a publication.
     */
    public function updatePublication(Request $request, int $publicationId)
    {
        $validator = Validator::make($request->all(), [
            'publications' => 'required|string',
            'staff_id' => 'required|integer|exists:staff,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $publication = Publication::findOrFail($publicationId);
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);

            $publication->update([
                'publications' => $purifier->purify($request->publications),
                'staff_id' => $request->staff_id
            ]);

            return response()->json(['message' => 'Publication updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Publication not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update publication: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a research interest.
     */
    public function updateResearchInterest(Request $request, int $researchInterestId)
    {
        $validator = Validator::make($request->all(), [
            'research' => 'required|string',
            'staff_id' => 'required|integer|exists:staff,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $researchInterest = ResearchInterest::findOrFail($researchInterestId);
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($purifierConfig);

            $researchInterest->update([
                'research' => $purifier->purify($request->research),
                'staff_id' => $request->staff_id
            ]);

            return response()->json(['message' => 'Research interest updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Research interest not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update research interest: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the staff record
            $staff = Staff::findOrFail($id);

            // Delete associated publications and research interests
            $staff->publications()->delete();
            $staff->researchInterests()->delete();

            // Delete the image if it exists
            if ($staff->image) {
                Storage::delete($staff->image);
            }

            // Delete the staff record
            $staff->delete();

            return response()->json(['message' => 'Staff and associated publications and research interest deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Staff not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete staff and publications and research interest: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete the image of a staff member.
     */
    public function deleteImage($staffId)
    {
        try {
            $staff = Staff::findOrFail($staffId);

            // Check if the staff has an image
            if ($staff->image) {
                // Delete the image from storage
                Storage::delete($staff->image);

                // Set the image column to null
                $staff->image = null;
                $staff->save();

                return response()->json(['message' => 'Image deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'No image found for this staff member'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Staff not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete image: ' . $e->getMessage()], 500);
        }
    }
}
