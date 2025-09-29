<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class ExportCompleteApp extends Command
{
    protected $signature = 'app:export-complete {--output=public/complete}';
    protected $description = 'Export complete MindVault app with ALL Filament features';

    public function handle()
    {
        $outputPath = $this->option('output');

        $this->info('🚀 Starting COMPLETE MindVault export...');

        // Clean and create output directory
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }
        File::makeDirectory($outputPath, 0755, true);

        // Copy all assets
        $this->copyAllAssets($outputPath);

        // Create demo user with data
        $user = $this->createDemoUser();

        // Export ALL routes and pages
        $this->exportAllPages($outputPath, $user);

        // Copy complete database
        $this->copyDatabase($outputPath);

        // Create comprehensive mobile app
        $this->createCompleteMobileApp($outputPath);

        // Clean up
        $user->delete();

        $this->info("✅ COMPLETE MindVault app exported to: {$outputPath}");
        $this->info("📱 Includes ALL Filament features and routes!");

        return 0;
    }

    private function copyAllAssets($outputPath)
    {
        $this->info('📦 Copying ALL assets...');

        // Public assets
        $publicAssets = [
            'build',
            'css',
            'js',
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

        // Copy Filament assets
        $filamentAssets = [
            'vendor/filament',
            'livewire',
            'flux'
        ];

        foreach ($filamentAssets as $asset) {
            $source = public_path($asset);
            $destination = $outputPath . '/' . $asset;

            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
            }
        }
    }

    private function createDemoUser()
    {
        $this->info('👤 Creating demo user with sample data...');

        return User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@mindvault.com',
            'email_verified_at' => now(),
        ]);
    }

    private function exportAllPages($outputPath, $user)
    {
        $this->info('📄 Exporting ALL MindVault pages...');

        // Login the user
        auth()->login($user);

        // Complete list of ALL your routes
        $routes = [
            // Main routes
            'dashboard' => '/dashboard',
            'login' => '/login',
            'register' => '/register',
            'settings-profile' => '/settings/profile',
            'settings-password' => '/settings/password',
            'settings-appearance' => '/settings/appearance',
            'settings-two-factor' => '/settings/two-factor',

            // User Panel - Main Dashboard
            'user-dashboard' => '/user',
            'user-login' => '/user/login',
            'user-register' => '/user/register',

            // User Panel - Notes
            'user-notes' => '/user/notes',
            'user-notes-create' => '/user/notes/create',

            // User Panel - Journal Entries
            'user-journal-entries' => '/user/journal-entries',
            'user-journal-entries-create' => '/user/journal-entries/create',

            // User Panel - Substances
            'user-substances' => '/user/substances',
            'user-substances-create' => '/user/substances/create',

            // User Panel - Research
            'user-my-researches' => '/user/my-researches',
            'user-my-researches-create' => '/user/my-researches/create',

            // User Panel - Research Libraries
            'user-research-libraries' => '/user/research-libraries',
            'user-research-libraries-create' => '/user/research-libraries/create',

            // User Panel - Feedback
            'user-feedback' => '/user/feedback',
            'user-feedback-create' => '/user/feedback/create',

            // User Panel - Nootropic Requests
            'user-nootropic-requests' => '/user/nootropic-requests',
            'user-nootropic-requests-create' => '/user/nootropic-requests/create',

            // Admin Panel - Dashboard
            'admin-dashboard' => '/admin',
            'admin-login' => '/admin/login',

            // Admin Panel - Resources
            'admin-feedback' => '/admin/feedback',
            'admin-feedback-create' => '/admin/feedback/create',
            'admin-journal-entries' => '/admin/journal-entries',
            'admin-journal-entries-create' => '/admin/journal-entries/create',
            'admin-nootropic-requests' => '/admin/nootropic-requests',
            'admin-research-libraries' => '/admin/research-libraries',
            'admin-research-libraries-create' => '/admin/research-libraries/create',
            'admin-substances' => '/admin/substances',
            'admin-substances-create' => '/admin/substances/create',
        ];

        $pagesDir = $outputPath . '/pages';
        File::makeDirectory($pagesDir, 0755, true);

        foreach ($routes as $name => $route) {
            try {
                $this->exportPage($name, $route, $pagesDir);
                $this->line("✅ Exported: {$name}");
            } catch (\Exception $e) {
                $this->warn("❌ Failed to export {$route}: " . $e->getMessage());
            }
        }
    }

    private function exportPage($name, $route, $pagesDir)
    {
        // Create HTTP request
        $request = \Illuminate\Http\Request::create($route, 'GET');
        $request->headers->set('Accept', 'text/html');

        // Handle the request
        $response = app()->handle($request);

        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();

            // Process for mobile
            $content = $this->processMobileContent($content, $name);

            // Create directory structure
            $pagePath = $pagesDir . '/' . dirname($name);
            if ($pagePath !== $pagesDir) {
                File::makeDirectory($pagePath, 0755, true);
            }

            // Save the page
            $fileName = basename($name) . '.html';
            $fullPath = dirname($name) === '.' ? $pagesDir . '/' . $fileName : $pagePath . '/' . $fileName;

            File::put($fullPath, $content);
        }
    }

    private function processMobileContent($html, $pageName)
    {
        // Add mobile viewport and meta tags
        $mobileHead = '
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="theme-color" content="#18181b">
        <style>
            /* Mobile optimizations for Filament */
            body { font-size: 14px !important; }
            .fi-sidebar { width: 100% !important; }
            .fi-topbar { padding: 0.5rem !important; }
            .fi-main { padding: 0.5rem !important; }
            .fi-table { overflow-x: auto !important; }
            .fi-form { max-width: 100% !important; }
            @media (max-width: 768px) {
                .fi-sidebar { transform: translateX(-100%) !important; }
                .fi-sidebar.open { transform: translateX(0) !important; }
            }
        </style>
        ';

        $html = str_replace('<head>', '<head>' . $mobileHead, $html);

        // Add mobile navigation script
        $mobileScript = '
        <script>
        // Mobile Filament optimizations
        document.addEventListener("DOMContentLoaded", function() {
            // Add mobile menu toggle
            const sidebar = document.querySelector(".fi-sidebar");
            const main = document.querySelector(".fi-main");

            if (sidebar && window.innerWidth < 768) {
                const toggle = document.createElement("button");
                toggle.innerHTML = "☰";
                toggle.style.cssText = "position:fixed;top:1rem;left:1rem;z-index:9999;background:#374151;color:white;border:none;padding:0.5rem;border-radius:0.25rem;";
                toggle.onclick = () => sidebar.classList.toggle("open");
                document.body.appendChild(toggle);
            }

            // Optimize tables for mobile
            const tables = document.querySelectorAll(".fi-table");
            tables.forEach(table => {
                table.style.fontSize = "12px";
                table.style.overflowX = "auto";
            });

            // Add back to main app button
            if (!window.location.pathname.includes("/user") && !window.location.pathname.includes("/admin")) {
                const backBtn = document.createElement("button");
                backBtn.innerHTML = "← Back to Main App";
                backBtn.style.cssText = "position:fixed;top:1rem;right:1rem;z-index:9999;background:#3b82f6;color:white;border:none;padding:0.5rem 1rem;border-radius:0.25rem;font-size:12px;";
                backBtn.onclick = () => window.parent.postMessage("navigate:dashboard", "*");
                document.body.appendChild(backBtn);
            }
        });
        </script>
        ';

        $html = str_replace('</body>', $mobileScript . '</body>', $html);

        return $html;
    }

    private function copyDatabase($outputPath)
    {
        $this->info('🗄️ Copying complete database...');

        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::copy($dbPath, $outputPath . '/database.sqlite');
        }
    }

    private function createCompleteMobileApp($outputPath)
    {
        $this->info('📱 Creating complete mobile app interface...');

        $appHtml = $this->generateCompleteMobileApp();
        File::put($outputPath . '/index.html', $appHtml);

        // Create manifest
        $manifest = [
            'name' => 'MindVault Complete',
            'short_name' => 'MindVault',
            'description' => 'Complete MindVault with all Filament features',
            'start_url' => './',
            'display' => 'standalone',
            'background_color' => '#18181b',
            'theme_color' => '#18181b',
            'icons' => [
                [
                    'src' => './favicon.ico',
                    'sizes' => '48x48',
                    'type' => 'image/x-icon'
                ],
                [
                    'src' => './apple-touch-icon.png',
                    'sizes' => '180x180',
                    'type' => 'image/png'
                ]
            ]
        ];

        File::put($outputPath . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
    }

    private function generateCompleteMobileApp()
    {
        return '<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MindVault Complete</title>
    <link rel="icon" href="./favicon.ico">
    <link rel="manifest" href="./manifest.json">
    <link href="./build/assets/app-DynmULMn.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #18181b; color: white; overflow: hidden; height: 100vh; }

        .app-container { display: flex; height: 100vh; width: 100vw; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 18rem;
            background: #18181b; border-right: 1px solid #3f3f46; z-index: 50;
            transform: translateX(-100%); transition: transform 0.3s ease; overflow-y: auto;
        }
        .sidebar.open { transform: translateX(0); }

        .sidebar-toggle {
            position: fixed; top: 1rem; left: 1rem; z-index: 60;
            background: #374151; color: white; border: 1px solid #4b5563;
            padding: 0.75rem; border-radius: 0.5rem; cursor: pointer;
        }

        .main-content { flex: 1; overflow: hidden; padding-top: 4rem; }
        .page-frame { width: 100%; height: 100%; border: none; background: white; }

        @media (min-width: 1024px) {
            .sidebar { transform: translateX(0); position: static; }
            .main-content { padding-top: 0; }
            .sidebar-toggle { display: none; }
        }

        .nav-brand { padding: 2rem 1rem; font-size: 1.5rem; font-weight: bold; color: white; border-bottom: 1px solid #3f3f46; text-align: center; }
        .nav-section { padding: 1rem 0; }
        .nav-section-title { padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #a1a1aa; text-transform: uppercase; }
        .nav-item {
            display: flex; align-items: center; padding: 0.75rem 1rem; color: #a1a1aa;
            border-radius: 0.375rem; margin: 0.25rem 0.5rem; cursor: pointer; transition: all 0.2s;
        }
        .nav-item:hover { background: #374151; color: white; }
        .nav-item.active { background: #3b82f6; color: white; }
        .nav-item svg { margin-right: 0.75rem; width: 1rem; height: 1rem; }

        .loading {
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            height: 100%; text-align: center; color: white; background: #18181b;
        }
        .spinner { width: 2rem; height: 2rem; border: 2px solid #3f3f46; border-top: 2px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="app-container">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
        </button>

        <div class="sidebar" id="sidebar">
            <div class="nav-brand">MindVault Complete</div>

            <div class="nav-section">
                <div class="nav-section-title">Main Dashboard</div>
                <div class="nav-item" onclick="loadPage(\'dashboard\', \'./pages/dashboard.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">User Panel</div>
                <div class="nav-item" onclick="loadPage(\'user-dashboard\', \'./pages/user-dashboard.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4z"></path></svg>
                    User Dashboard
                </div>
                <div class="nav-item" onclick="loadPage(\'user-notes\', \'./pages/user-notes.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"></path></svg>
                    Notes
                </div>
                <div class="nav-item" onclick="loadPage(\'user-journal\', \'./pages/user-journal-entries.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Journal Entries
                </div>
                <div class="nav-item" onclick="loadPage(\'user-substances\', \'./pages/user-substances.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3z" clip-rule="evenodd"></path></svg>
                    Substances
                </div>
                <div class="nav-item" onclick="loadPage(\'user-research\', \'./pages/user-my-researches.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    My Research
                </div>
                <div class="nav-item" onclick="loadPage(\'user-libraries\', \'./pages/user-research-libraries.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                    Research Libraries
                </div>
                <div class="nav-item" onclick="loadPage(\'user-nootropics\', \'./pages/user-nootropic-requests.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    Nootropic Requests
                </div>
                <div class="nav-item" onclick="loadPage(\'user-feedback\', \'./pages/user-feedback.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                    Feedback
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Admin Panel</div>
                <div class="nav-item" onclick="loadPage(\'admin-dashboard\', \'./pages/admin-dashboard.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    Admin Dashboard
                </div>
                <div class="nav-item" onclick="loadPage(\'admin-feedback\', \'./pages/admin-feedback.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2z" clip-rule="evenodd"></path></svg>
                    Manage Feedback
                </div>
                <div class="nav-item" onclick="loadPage(\'admin-substances\', \'./pages/admin-substances.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3z" clip-rule="evenodd"></path></svg>
                    Manage Substances
                </div>
                <div class="nav-item" onclick="loadPage(\'admin-journal\', \'./pages/admin-journal-entries.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Manage Journal Entries
                </div>
                <div class="nav-item" onclick="loadPage(\'admin-libraries\', \'./pages/admin-research-libraries.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                    Manage Libraries
                </div>
                <div class="nav-item" onclick="loadPage(\'admin-nootropics\', \'./pages/admin-nootropic-requests.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    Manage Nootropic Requests
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Settings</div>
                <div class="nav-item" onclick="loadPage(\'settings-profile\', \'./pages/settings-profile.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    Profile Settings
                </div>
                <div class="nav-item" onclick="loadPage(\'settings-password\', \'./pages/settings-password.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    Password Settings
                </div>
                <div class="nav-item" onclick="loadPage(\'settings-appearance\', \'./pages/settings-appearance.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path></svg>
                    Appearance
                </div>
                <div class="nav-item" onclick="loadPage(\'settings-2fa\', \'./pages/settings-two-factor.html\')">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Two-Factor Auth
                </div>
            </div>
        </div>

        <div class="main-content">
            <div id="loading" class="loading">
                <div class="spinner"></div>
                <h2>Loading MindVault...</h2>
                <p>Your complete knowledge management system</p>
            </div>
            <iframe id="app-frame" class="page-frame" style="display: none;" src=""></iframe>
        </div>
    </div>

    <script>
        let currentPage = null;

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("open");
        }

        function loadPage(pageName, pageUrl) {
            console.log("Loading page:", pageName, pageUrl);

            const frame = document.getElementById("app-frame");
            const loading = document.getElementById("loading");
            const sidebar = document.getElementById("sidebar");

            // Update active nav item
            document.querySelectorAll(".nav-item").forEach(item => item.classList.remove("active"));
            event.target.closest(".nav-item").classList.add("active");

            // Show loading
            loading.style.display = "flex";
            frame.style.display = "none";

            // Load page
            frame.src = pageUrl;
            frame.onload = () => {
                loading.style.display = "none";
                frame.style.display = "block";
                currentPage = pageName;
            };

            // Close sidebar on mobile
            sidebar.classList.remove("open");
        }

        // Initialize with dashboard
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                document.querySelector(".nav-item").click();
            }, 1000);

            // Close sidebar when clicking outside
            document.addEventListener("click", function(event) {
                const sidebar = document.getElementById("sidebar");
                const toggle = document.querySelector(".sidebar-toggle");

                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove("open");
                }
            });
        });
    </script>
</body>
</html>';
    }
}