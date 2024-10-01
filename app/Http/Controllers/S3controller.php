<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class S3controller extends Controller
{
    /**
     * Display the contents of an S3 bucket and connection information.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');


        // Retrieve all files from the S3 bucket; provide root path as argument
        $files = Storage::disk('s3')->listContents('', true); // The second argument is true to include subdirectories

        // Filter only files
        $files = collect($files)->filter(function ($file) {
            return $file['type'] === 'file';
        });
//        dd($files);

        // Sort files based on specified criteria
        if ($sortBy === 'name') {
            $files = $files->sortBy('path', SORT_REGULAR, $sortOrder === 'desc');
        } elseif ($sortBy === 'created_at' && isset($files[0]['lastModified'])) {
            $files = $files->sortBy('lastModified', SORT_REGULAR, $sortOrder === 'desc');
        }

//dd($files);
        // Pagination setup
        $perPage = 36;
        $currentPage = $request->get('page', 1);
        $paginatedFiles = new LengthAwarePaginator(
            $files->forPage($currentPage, $perPage),
            $files->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url()]
        );

        // Count the number of files
        $fileCount = $files->count();



        // Retrieve connection information from the environment or config
        $connectionInfo = [
            'AWS Access Key ID' => config('filesystems.disks.s3.key'),
            'bucket_name' => config('filesystems.disks.s3.bucket'),
            'AWS Region' => config('filesystems.disks.s3.region'),
            'AWS Endpoint' => config('filesystems.disks.s3.endpoint'),
        ];

        // Return the view with the list of paginated files, file count, and connection information
        return view('index', compact('paginatedFiles', 'fileCount', 'connectionInfo'));
    }



    public function uploadTestFile()
    {
        // Create some test content
        $testContent = "This is a test file uploaded on " . now();

        // Define a test file name with a unique timestamp
        $fileName = 'test-file-' . time() . '.txt';

        // Upload the file to S3
        Storage::disk('s3')->put($fileName, $testContent);

        // Return a success message with the file name
        return response()->json([
            'message' => 'Test file uploaded successfully',
            'file_name' => $fileName
        ]);
    }

    /**
     * Check if a file is an image based on its extension.
     *
     * @param string $fileName
     * @return bool
     */
    public function isImage($fileName)
    {
//        echo $fileName;
//        var_dump($fileName['path']);die;
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $extension = pathinfo($fileName['path'], PATHINFO_EXTENSION);

        return in_array(strtolower($extension), $imageExtensions);
    }

}
