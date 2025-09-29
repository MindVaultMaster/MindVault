<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->registration()
            ->colors([
                'primary' => [
                    50 => '#eff6ff',
                    100 => '#dbeafe',
                    200 => '#bfdbfe',
                    300 => '#93c5fd',
                    400 => '#60a5fa',
                    500 => '#3b82f6',
                    600 => '#2563eb',
                    700 => '#1d4ed8',
                    800 => '#1e40af',
                    900 => '#1e3a8a',
                    950 => '#172554',
                ],
                'secondary' => [
                    50 => '#f0f9ff',
                    100 => '#e0f2fe',
                    200 => '#bae6fd',
                    300 => '#7dd3fc',
                    400 => '#38bdf8',
                    500 => '#0ea5e9',
                    600 => '#0284c7',
                    700 => '#0369a1',
                    800 => '#075985',
                    900 => '#0c4a6e',
                    950 => '#082f49',
                ],
                'success' => [
                    50 => '#ecfdf5',
                    100 => '#d1fae5',
                    200 => '#a7f3d0',
                    300 => '#6ee7b7',
                    400 => '#34d399',
                    500 => '#10b981',
                    600 => '#059669',
                    700 => '#047857',
                    800 => '#065f46',
                    900 => '#064e3b',
                    950 => '#022c22',
                ],
                'warning' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#f59e0b',
                    600 => '#d97706',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'danger' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#ef4444',
                    600 => '#dc2626',
                    700 => '#b91c1c',
                    800 => '#991b1b',
                    900 => '#7f1d1d',
                    950 => '#450a0a',
                ],
                'gray' => [
                    50 => '#f8fafc',
                    100 => '#f1f5f9',
                    200 => '#e2e8f0',
                    300 => '#cbd5e1',
                    400 => '#94a3b8',
                    500 => '#64748b',
                    600 => '#475569',
                    700 => '#334155',
                    800 => '#1e293b',
                    900 => '#0f172a',
                    950 => '#020617',
                ],
            ])
            ->brandName('🧠 MindVault')
            ->navigationGroups([
                '📊 Tracking',
                '📈 Analytics',
                '🔬 Community',
                '🧠 MindVault',
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                \App\Filament\User\Widgets\StatsOverviewWidget::class,
                \App\Filament\User\Widgets\GuidedDailyPromptsWidget::class,
                \App\Filament\User\Widgets\TrendChartsWidget::class,
                \App\Filament\User\Widgets\RecentEntriesWidget::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Switch to Admin Dashboard')
                    ->url('/admin')
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn (): bool => auth()->user()?->is_admin ?? false),
            ]);
    }
}
