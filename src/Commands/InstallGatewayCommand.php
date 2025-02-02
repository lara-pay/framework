<?php

namespace LaraPay\Framework\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\{select, spin, info};

class InstallGatewayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:install {github?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a gateway from GitHub repository.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $repo = $this->argument('github');

        if(!$repo) {
            $repo = select(
                label: 'Select the gateway you want to install',
                options: config('larapay.suggested_gateways')
            );
        }

        // Ensure it's in the format "owner/repo"
        if (!preg_match('/^[a-zA-Z0-9_-]+\/[a-zA-Z0-9._-]+$/', $repo)) {
            $this->error('Invalid Repository format. Use owner/repo (e.g., laravel/framework).');
            exit(1);
        }

        $githubUrl = "https://github.com/{$repo}";

        // Optional: Validate if the repo actually exists on GitHub
        if (!$this->repositoryExists($repo)) {
            $this->error('Repository does not exist on GitHub.');
            exit(1);
        }

        $githubUrl = "https://github.com/{$repo}";

        $response = spin(
            message: 'Installing gateway...',
            callback: fn () => $this->install($githubUrl),
        );

        info('Gateway has been installed successfully.');
    }

    /**
     * Check if the repository exists on GitHub.
     */
    private function repositoryExists(string $repo): bool
    {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
        ])->get("https://api.github.com/repos/{$repo}");

        return $response->successful();
    }

    public function install($githubUrl)
    {
        // Convert GitHub repo URL to ZIP download URL
        $zipUrl = $this->convertToZipUrl($githubUrl);

        // Download the zip file
        $zipPath = storage_path('app/temp_gateway.zip');
        $this->downloadFile($zipUrl, $zipPath);

        // Extract to app/Gateways
        $extractPath = app_path('Gateways');
        $this->extractZip($zipPath, $extractPath);

        // Cleanup
        unlink($zipPath);
    }

    /**
     * Convert GitHub repo URL to downloadable ZIP URL.
     */
    private function convertToZipUrl(string $url): string
    {
        // Parse the URL to extract the owner and repo name
        $parsedUrl = parse_url($url);
        $pathSegments = explode('/', trim($parsedUrl['path'], '/'));

        if (count($pathSegments) < 2) {
            $this->error('Invalid GitHub repository URL.');
            exit(1);
        }

        [$owner, $repo] = $pathSegments;

        // GitHub API URL for releases
        $apiUrl = "https://api.github.com/repos/$owner/$repo/releases/latest";

        // Fetch the latest release data using Laravel HTTP client
        $response = Http::withHeaders([
            'User-Agent' => 'Laravel Gateway Installer'
        ])->get($apiUrl);

        if ($response->successful() && isset($response->json()['zipball_url'])) {
            return $response->json()['zipball_url']; // Return latest release ZIP
        }

        // If no releases found, use the default branch ZIP
        return "https://github.com/$owner/$repo/archive/refs/heads/main.zip";
    }

    /**
     * Download file from URL.
     */
    private function downloadFile(string $url, string $path): void
    {
        info("Downloading: $url");

        // Make an HTTP GET request with Laravel HTTP client
        $response = Http::withHeaders([
            'User-Agent' => 'Laravel Gateway Installer'
        ])->get($url);

        if (!$response->successful()) {
            $this->error('Failed to download the file. HTTP Status: ' . $response->status());
            exit(1);
        }

        // Ensure the storage directory exists
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Save the file
        File::put($path, $response->body());

        if (!File::exists($path)) {
            $this->error('Failed to save the downloaded file.');
            exit(1);
        }

        info('Gateway has been installed in the app/Gateways directory.');
    }


    /**
     * Extract ZIP file.
     */
    /**
     * Extract ZIP file and move inner contents.
     */
    private function extractZip(string $zipPath, string $extractPath): void
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            $this->error('Failed to open the ZIP file.');
            exit(1);
        }

        // Create a temporary directory to extract
        $tempExtractPath = storage_path('app/temp_extracted');
        if (File::exists($tempExtractPath)) {
            File::deleteDirectory($tempExtractPath);
        }
        File::makeDirectory($tempExtractPath, 0755, true);

        // Extract ZIP to the temporary directory
        $zip->extractTo($tempExtractPath);
        $zip->close();
        $this->info('Extraction complete.');

        // Find the first folder inside (GitHub auto-generates one)
        $subFolders = File::directories($tempExtractPath);
        if (empty($subFolders)) {
            $this->error('No folder found inside the ZIP.');
            File::deleteDirectory($tempExtractPath);
            exit(1);
        }

        // Move files from the extracted subfolder to the final location
        $innerFolder = $subFolders[0];
        File::copyDirectory($innerFolder, $extractPath);

        // Cleanup temporary files
        File::deleteDirectory($tempExtractPath);

        $this->info("Gateway successfully extracted to: $extractPath");
    }
}
