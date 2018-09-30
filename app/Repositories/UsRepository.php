<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class UsRepository
{
    protected $filename = 'contact_us.txt';

    public function getData() {
        $content = '';
        if(Storage::disk('local')->exists($this->filename)) {
            $content = Storage::disk('local')->get($this->filename);
        }
        return $content;
    }

    public function setData($content) {
        Storage::disk('local')->put($this->filename, $content);
    }
}
