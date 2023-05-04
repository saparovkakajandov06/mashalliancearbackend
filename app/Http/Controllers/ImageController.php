<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends BaseController
{
    public function getImage($path)
    {
        if (Storage::disk('public')->has('images/'.$path))
            return response()->file(Storage::disk('public')->path('images/'.$path));
        
        return $this->sendError([], 'Файла не существует');
    }
}
