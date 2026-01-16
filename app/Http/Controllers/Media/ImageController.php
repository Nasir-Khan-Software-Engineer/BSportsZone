<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\MediaImage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class ImageController extends Controller
{
    public function index()
    {
        return view('media.image.index');
    }

    public function datatable(Request $request)
    {
        $POSID = auth()->user()->POSID;
        $searchCriteria = $request->input('search');

        $query = MediaImage::where('media_images.POSID', $POSID)
            ->with('creator')
            ->where(function($query) use ($searchCriteria) {
                $query->where('file_name', 'like', "%{$searchCriteria}%")
                      ->orWhere('relation', 'like', "%{$searchCriteria}%")
                      ->orWhere('type', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = MediaImage::where('POSID', $POSID)->count();
        $filteredRecord = $query->count();

        // Handle sorting
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        // Special handling for each column
        if ($orderColumn == 0) {
            // Order by ID
            $images = (clone $query)->orderBy('id', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 1) {
            // Order by file_name
            $images = (clone $query)->orderBy('file_name', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 2) {
            // Order by size
            $images = (clone $query)->orderBy('size', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 3) {
            // Order by type
            $images = (clone $query)->orderBy('type', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 4) {
            // Order by relation
            $images = (clone $query)->orderBy('relation', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 5) {
            // Order by created_at
            $images = (clone $query)->orderBy('created_at', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } else {
            // Default sorting by ID descending
            $images = (clone $query)->orderBy('id', 'desc')
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        }

        $images->transform(function($image) {
            $image->formattedDate = formatDate($image->created_at);
            $image->formattedTime = formatTime($image->created_at);
            $image->formattedSize = $this->formatFileSize($image->size);
            $image->createdBy = $image->creator ? $image->creator->name : 'N/A';
            $image->imageUrl = asset($image->file_path);
            $image->fullPath = asset($image->file_path);
            return $image;
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $filteredRecord,
            'data' => $images
        ]);
    }

    public function store(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'image' => 'required|file|mimes:gif,jpg,jpeg,png|max:1024', // Max 1MB
                'relation' => 'required|string|in:Product,Banner,Review,Other',
            ], [
                'image.required' => 'Please select an image file.',
                'image.file' => 'The uploaded file is not valid.',
                'image.mimes' => 'The image must be a file of type: gif, jpg, jpeg, png.',
                'image.max' => 'The image size must not exceed 1MB.',
                'relation.required' => 'Please select a relation.',
                'relation.in' => 'The selected relation is invalid.',
            ]);

            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            
            // Validate extension (note: user mentioned .php but that's likely a typo, we'll use png instead)
            $allowedExtensions = ['gif', 'jpg', 'jpeg', 'png'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid file type. Allowed types: gif, jpg, jpeg, png',
                ], 422);
            }

            // Create directory path: public/images/posid/relation
            $relation = $request->input('relation');
            $directory = public_path("images/{$POSID}/{$relation}");

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Create file name with datetime stamp at the end: originalname_Y-m-d_H-i-s.extension
            $timestamp = now()->format('Y-m-d_H-i-s');
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $fileName = $fileNameWithoutExt . '_' . $timestamp . '.' . $extension;
            $filePath = $directory . '/' . $fileName;

            // Move uploaded file
            $file->move($directory, $fileName);

            // Store in database
            $mediaImage = new MediaImage();
            $mediaImage->POSID = $POSID;
            $mediaImage->file_name = $fileName;
            $mediaImage->file_path = "images/{$POSID}/{$relation}/{$fileName}";
            $mediaImage->size = $size;
            $mediaImage->type = $extension;
            $mediaImage->relation = $relation;
            $mediaImage->created_by = auth()->user()->id;
            $mediaImage->save();

            $mediaImage->load('creator');
            $mediaImage->formattedDate = formatDate($mediaImage->created_at);
            $mediaImage->formattedTime = formatTime($mediaImage->created_at);
            $mediaImage->formattedSize = $this->formatFileSize($mediaImage->size);
            $mediaImage->createdBy = auth()->user()->name;

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully.',
                'image' => $mediaImage
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors(),
            ], 422);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong, please try later.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function show(MediaImage $image)
    {
        $POSID = auth()->user()->POSID;
        
        // Ensure image belongs to current POSID
        if ($image->POSID != $POSID) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.',
            ], 403);
        }
        
        $image->load('creator');
        $image->formattedDate = formatDate($image->created_at);
        $image->formattedTime = formatTime($image->created_at);
        $image->formattedSize = $this->formatFileSize($image->size);
        $image->createdBy = $image->creator ? $image->creator->name : 'N/A';
        $image->imageUrl = asset($image->file_path);

        return response()->json([
            'status' => 'success',
            'image' => $image,
        ]);
    }

    public function destroy(MediaImage $image)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            // Ensure image belongs to current POSID
            if ($image->POSID != $POSID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.',
                ], 403);
            }

            // Delete physical file
            $fullPath = public_path($image->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Delete database record
            $image->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Image deleted successfully.',
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong, please try later.',
            ], 500);
        }
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
