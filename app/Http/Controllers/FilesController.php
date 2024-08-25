<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return File
     */
    public function store(array $data, array $extraRules = [], $disk = '', $folder = 'files')
    {
        $rules = [
            'file' => ['file']
        ];
        Validator::make($data, array_merge($rules, $extraRules))->validate();
        $file = new File();
        $file->created_by_id = Auth::id();
        $file->record_id = empty($data['record_id']) ? null : $data['record_id'];
        $file->category = empty($data['category']) ? null : $data['category'];
        $file->file_type_id = empty($data['file_type_id']) ? null : $data['file_type_id'];
        $file->disk = $disk;
        $file->file_size = $data['file']->getSize();
        $file->mime_type = $data['file']->getMimeType();
        $file->name = !empty($data['name']) ? $data['name'] : $data['file']->getClientOriginalName();
        $file->description = empty($data['description']) ? null : $data['description'];
        $file_name = $data['file']->store($folder, $disk);
        $file->link = $file_name;
        $file->save();
        return $file;
    }
    public function upload(Request $request)
    {
        $path = Storage::disk('public')->putFile('media', $request->file('file'));
        $file = new File();
        $file->created_by_id = Auth::id();
        $file->category = 'article_file';
        $file->disk = 'public';
        $file->file_size = $request->file('file')->getSize();
        $file->mime_type = $request->file('file')->getMimeType();
        $file->name = $request->file('file')->getClientOriginalName();
        $file->link = $path;
        $file->save();
        return response()->json([
            'url' => Storage::disk('public')->url($path)
        ]);

    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, array $data, array $extraRules = [], $disk = '', $folder = 'files')
    {
        $file = File::find($id);
        $rules = [
            'file' => ['nullable', 'file']
        ];
        Validator::make($data, array_merge($rules, $extraRules))->validate();
        $file->name = empty($data['name']) ? null : $data['name'];
        $file->file_type_id = empty($data['file_type_id']) ? null : $data['file_type_id'];
        $file->description = empty($data['description']) ? null : $data['description'];
        if (!empty($data['file'])) {
            if (!$file->name) {
                $file->name = !empty($data['name']) ? $data['name'] : $data['file']->getClientOriginalName();
            }
            $file->disk = $disk;
            $file->file_size = $data['file']->getSize();
            $file->mime_type = $data['file']->getMimeType();
            $file_name = $data['file']->store($folder, $disk);
            $file->link = $file_name;
        }
        $file->save();
        return $file;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        if (Storage::disk($file->disk)->exists($file->link)) {
            Storage::disk($file->disk)->delete( $file->link);
        }
        $file->delete();
    }

    public function download(File $file)
    {

        if (Storage::disk($file->disk)->exists($file->link)) {
            return Storage::disk($file->disk)->download($file->link);
        }
    }
}
