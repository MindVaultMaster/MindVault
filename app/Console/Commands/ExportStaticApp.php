<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\User;

class ExportStaticApp extends Command
{
    protected $signature = 'app:export-static {--output=public/mobile}';
    protected $description = 'Export Laravel app as static files for mobile';

    public function handle()
    {
        $outputPath = $this->option('output');

        $this->info('Starting static app export...');

        // Create output directory
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }
        File::makeDirectory($outputPath, 0755, true);

        // Copy assets
        $this->copyAssets($outputPath);

        // Export pages
        $this->exportPages($outputPath);

        // Copy database
        $this->copyDatabase($outputPath);

        // Create mobile app structure
        $this->createMobileApp($outputPath);

        $this->info("Static app exported to: {$outputPath}");

        return 0;
    }

    private function copyAssets($outputPath)
    {
        $this->info('Copying assets...');

        // Copy public assets
        $publicAssets = [
            'build',
            'css',
            'favicon.ico',
            'favicon.svg',
            'apple-touch-icon.png'
        ];

        foreach ($publicAssets as $asset) {
            $source = public_path($asset);
            $destination = $outputPath . '/' . $asset;

            if (File::exists($source)) {
                if (File::isDirectory($source)) {
                    File::copyDirectory($source, $destination);
                } else {
                    File::copy($source, $destination);
                }
            }
        }
    }

    private function exportPages($outputPath)
    {
        $this->info('Exporting pages...');

        // Create a demo user for page generation
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@mindvault.com',
        ]);

        // Login the user for authenticated pages
        auth()->login($user);

        // Pages to export
        $pages = [
            'dashboard' => '/dashboard',
            'login' => '/login',
            'register' => '/register',
            'settings/profile' => '/settings/profile',
            'settings/password' => '/settings/password',
            'settings/appearance' => '/settings/appearance',
        ];

        foreach ($pages as $name => $route) {
            try {
                $this->exportPage($name, $route, $outputPath);
            } catch (\Exception $e) {
                $this->warn("Failed to export {$route}: " . $e->getMessage());
            }
        }

        // Clean up demo user
        $user->delete();
    }

    private function exportPage($name, $route, $outputPath)
    {
        $request = Request::create($route, 'GET');
        $response = app()->handle($request);

        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();

            // Process the HTML for mobile
            $content = $this->processMobileHtml($content);

            // Create directory structure
            $pagePath = $outputPath . '/pages/' . dirname($name);
            File::makeDirectory($pagePath, 0755, true);

            // Save the page
            $fileName = basename($name) . '.html';
            File::put($pagePath . '/' . $fileName, $content);

            $this->line("Exported: {$name}");
        }
    }

    private function processMobileHtml($html)
    {
        // Add mobile-specific meta tags
        $mobileHead = '
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        ';

        $html = str_replace('<head>', '<head>' . $mobileHead, $html);

        // Add mobile app bridge script
        $bridgeScript = '
        <script>
        // Mobile App Bridge
        window.MobileApp = {
            storage: {
                set: function(key, value) {
                    localStorage.setItem(key, JSON.stringify(value));
                },
                get: function(key) {
                    const item = localStorage.getItem(key);
                    return item ? JSON.parse(item) : null;
                },
                remove: function(key) {
                    localStorage.removeItem(key);
                }
            },
            navigate: function(page) {
                // Handle navigation in mobile app
                if (window.Android) {
                    window.Android.navigate(page);
                } else {
                    // Fallback for web
                    window.location.href = page;
                }
            }
        };
        </script>
        ';

        $html = str_replace('</body>', $bridgeScript . '</body>', $html);

        return $html;
    }

    private function copyDatabase($outputPath)
    {
        $this->info('Copying database...');

        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::copy($dbPath, $outputPath . '/database.sqlite');
        }
    }

    private function createMobileApp($outputPath)
    {
        $this->info('Creating mobile app structure...');

        // Create main app HTML file
        $appHtml = $this->generateMobileAppHtml();
        File::put($outputPath . '/app.html', $appHtml);

        // Create app manifest
        $manifest = [
            'name' => 'MindVault',
            'short_name' => 'MindVault',
            'description' => 'Your personal knowledge management system',
            'start_url' => './app.html',
            'display' => 'standalone',
            'background_color' => '#18181b',
            'theme_color' => '#18181b',
            'icons' => [
                [
                    'src' => './favicon.ico',
                    'sizes' => '48x48',
                    'type' => 'image/x-icon'
                ]
            ]
        ];

        File::put($outputPath . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
    }

    private function generateMobileAppHtml()
    {
        return '
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MindVault</title>

    <link rel="icon" href="./favicon.ico">
    <link rel="manifest" href="./manifest.json">

    <link href="./build/assets/app-DynmULMn.css" rel="stylesheet">
    <link href="./css/mindvault-theme.css" rel="stylesheet">

    <style>
        .mobile-container {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        .page-content {
            width: 100%;
            height: 100%;
            border: none;
            overflow-y: auto;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgb(24 24 27);
            color: white;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div id="loading" class="loading">
            <div>
                <h2>Loading MindVault...</h2>
                <p>Setting up your personal knowledge management system</p>
            </div>
        </div>

        <iframe id="app-frame" class="page-content" style="display: none;"></iframe>
    </div>

    <script>
        // Mobile App Router
        class MobileRouter {
            constructor() {
                this.currentPage = null;
                this.frame = document.getElementById("app-frame");
                this.loading = document.getElementById("loading");
                this.init();
            }

            init() {
                // Start with dashboard
                setTimeout(() => {
                    this.navigate("dashboard");
                }, 1000);
            }

            navigate(page) {
                console.log("Navigating to:", page);

                const pageMap = {
                    "dashboard": "./pages/dashboard.html",
                    "login": "./pages/login.html",
                    "register": "./pages/register.html",
                    "profile": "./pages/settings/profile.html",
                    "password": "./pages/settings/password.html",
                    "appearance": "./pages/settings/appearance.html"
                };

                const url = pageMap[page] || pageMap["dashboard"];

                this.frame.src = url;
                this.frame.onload = () => {
                    this.loading.style.display = "none";
                    this.frame.style.display = "block";
                    this.currentPage = page;
                };
            }
        }

        // Initialize router
        window.router = new MobileRouter();

        // Global navigation function
        window.navigateTo = function(page) {
            window.router.navigate(page);
        };

        // Android bridge (if available)
        if (window.Android) {
            window.Android.onReady();
        }
    </script>
</body>
</html>';
    }
}