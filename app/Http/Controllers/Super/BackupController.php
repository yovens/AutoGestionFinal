<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use App\Support\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        return view('super.backups', ['backups' => Backup::list()]);
    }

    public function store()
    {
        $code = Artisan::call('app:backup', ['--no-mail' => true]);

        return $code === 0
            ? back()->with('success', 'Sauvegarde créée.')
            : back()->with('error', 'La sauvegarde a échoué : ' . trim(Artisan::output()));
    }

    public function download(string $file)
    {
        $path = Backup::path($file);
        Audit::log('backup.download', 'Sauvegarde téléchargée : ' . basename($path));

        return response()->download($path);
    }

    public function destroy(string $file)
    {
        $path = Backup::path($file);
        File::delete($path);
        Audit::log('backup.delete', 'Sauvegarde supprimée : ' . basename($path));

        return back()->with('success', 'Sauvegarde supprimée.');
    }
}
