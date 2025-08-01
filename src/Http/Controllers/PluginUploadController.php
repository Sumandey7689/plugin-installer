<?php

namespace Sumandey8976\PluginInstaller\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;

class PluginUploadController extends Controller
{
    public function showForm()
    {
        return view('plugin_upload');
    }

    public function listPlugins()
    {
        $pluginsDir = base_path('plugins');
        $plugins = [];

        if (!File::exists($pluginsDir)) {
            File::makeDirectory($pluginsDir, 0755, true);
        }

        foreach (File::directories($pluginsDir) as $pluginPath) {
            $pluginJson = $pluginPath . '/plugin.json';
            if (File::exists($pluginJson)) {
                $data = json_decode(File::get($pluginJson), true);
                $plugins[] = [
                    'name' => $data['name'] ?? basename($pluginPath),
                    'version' => $data['version'] ?? '',
                    'author' => $data['author'] ?? '',
                    'desc' => $data['description'] ?? '',
                    'icon' => $data['icon'] ?? 'puzzle-piece'
                ];
            }
        }

        return response()->json($plugins);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip'
        ]);

        $zip = new ZipArchive;
        $zipPath = $request->file('plugin_zip')->getRealPath();

        if ($zip->open($zipPath) === true) {
            $folderName = $zip->getNameIndex(0);
            $slug = trim(explode('/', $folderName)[0]);

            $pluginsBasePath = base_path('plugins');
            if (!File::exists($pluginsBasePath)) {
                File::makeDirectory($pluginsBasePath, 0755, true);
            }

            $destinationPath = base_path("plugins/$slug");

            if (File::exists($destinationPath)) {
                File::deleteDirectory($destinationPath);
            }

            $zip->extractTo(base_path('plugins'));
            $zip->close();

            $migrationPath = "plugins/$slug/migrations";

            if (File::exists(base_path($migrationPath))) {
                Artisan::call('migrate:rollback', [
                    '--path' => $migrationPath,
                    '--force' => true
                ]);

                Artisan::call('migrate', [
                    '--path' => $migrationPath,
                    '--force' => true
                ]);
            }

            $pluginJsonPath = base_path("plugins/$slug/plugin.json");
            $composerOutput = [];

            if (File::exists($pluginJsonPath)) {
                $pluginData = json_decode(File::get($pluginJsonPath), true);

                if (!empty($pluginData['require']) && is_array($pluginData['require'])) {
                    foreach ($pluginData['require'] as $package => $version) {
                        $safePackage = escapeshellarg("{$package}:{$version}");

                        chdir(base_path());
                        exec("composer require {$safePackage} 2>&1", $output, $returnVar);
                        $composerOutput = array_merge($composerOutput, $output);

                        if ($returnVar !== 0) {
                            return response()->json([
                                'message' => "Plugin installed, but failed to install required package: {$package}",
                                'composer_output' => $composerOutput
                            ], 500);
                        }
                    }
                }

                if (!empty($pluginData['service_provider']) && class_exists($pluginData['service_provider'])) {
                    app()->register($pluginData['service_provider']);
                }
            }

            return response()->json([
                'message' => 'Plugin installed successfully.',
                'composer_output' => $composerOutput
            ]);
        }

        return response()->json(['message' => 'Failed to extract plugin ZIP.'], 500);
    }
}
