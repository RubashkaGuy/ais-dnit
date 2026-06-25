<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ContractsRevenueChart;
use App\Filament\Widgets\ContractsTrendChart;
use App\Filament\Widgets\EmployeesByDepartmentChart;
use App\Filament\Widgets\QualificationControlWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopCoursesRevenueChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Дом Науки и Техники')
            ->brandLogo(fn (): HtmlString => new HtmlString(
                '<div style="display:flex;align-items:center;gap:.55rem;min-width:0;height:100%;">'
                .'<img src="'.asset('images/image.png').'" alt="ДНиТ" style="height:28px;width:28px;object-fit:contain;flex-shrink:0;display:block;" />'
                .'<div style="display:flex;flex-direction:column;line-height:1.15;min-width:0;">'
                .'<span style="font-size:.875rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Дом Науки и Техники</span>'
                .'<span style="font-size:.65rem;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#1FA8E3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">АИС учёта</span>'
                .'</div>'
                .'</div>'
            ))
            ->darkModeBrandLogo(fn (): HtmlString => new HtmlString(
                '<div style="display:flex;align-items:center;gap:.55rem;min-width:0;height:100%;">'
                .'<img src="'.asset('images/image.png').'" alt="ДНиТ" style="height:28px;width:28px;object-fit:contain;flex-shrink:0;display:block;" />'
                .'<div style="display:flex;flex-direction:column;line-height:1.15;min-width:0;">'
                .'<span style="font-size:.875rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Дом Науки и Техники</span>'
                .'<span style="font-size:.65rem;font-weight:500;letter-spacing:.06em;text-transform:uppercase;color:#7CC9EE;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">АИС учёта</span>'
                .'</div>'
                .'</div>'
            ))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/image.png'))
            ->colors([
                'primary' => Color::hex('#1FA8E3'),
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => <<<'HTML'
                    <details class="fi-login-help">
                        <summary>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <span>Как получить аккаунт?</span>
                        </summary>
                        <div class="fi-login-help-body">
                            <p>Учётные записи создаёт администратор АИС.</p>
                            <p>Для получения доступа обратитесь к администратору ДНиТ: сообщите ФИО, должность и подразделение — после этого вы получите логин и пароль.</p>
                        </div>
                    </details>
                    <style>
                        .fi-login-help {
                            margin-top: 1rem;
                            border: 1px solid var(--gray-200);
                            border-radius: 0.5rem;
                            background-color: rgba(31, 168, 227, 0.04);
                            overflow: hidden;
                        }
                        .dark .fi-login-help {
                            border-color: var(--gray-700);
                            background-color: rgba(124, 201, 238, 0.06);
                        }
                        .fi-login-help > summary {
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            padding: 0.625rem 0.875rem;
                            font-size: 0.875rem;
                            font-weight: 500;
                            color: var(--primary-700);
                            cursor: pointer;
                            list-style: none;
                            user-select: none;
                        }
                        .dark .fi-login-help > summary {
                            color: var(--primary-300);
                        }
                        .fi-login-help > summary::-webkit-details-marker {
                            display: none;
                        }
                        .fi-login-help > summary svg {
                            width: 1rem;
                            height: 1rem;
                            flex-shrink: 0;
                        }
                        .fi-login-help > summary:hover {
                            background-color: rgba(31, 168, 227, 0.08);
                        }
                        .fi-login-help-body {
                            padding: 0 0.875rem 0.75rem 0.875rem;
                            font-size: 0.8125rem;
                            line-height: 1.45;
                            color: var(--gray-700);
                        }
                        .dark .fi-login-help-body {
                            color: var(--gray-300);
                        }
                        .fi-login-help-body p {
                            margin: 0.375rem 0;
                        }
                    </style>
                HTML,
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => <<<'HTML'
                    <style>
                        /* Table header — heading и тулбар (поиск, фильтры) в одну строку */
                        .fi-ta-header-ctn {
                            display: flex;
                            flex-wrap: wrap;
                            align-items: center;
                            column-gap: 1rem;
                            row-gap: 0.75rem;
                        }
                        .fi-ta-header-ctn > .fi-ta-header {
                            flex: 1 1 16rem;
                            min-width: 0;
                        }
                        .fi-ta-header-ctn > .fi-ta-header-toolbar {
                            flex: 0 1 auto;
                            margin-inline-start: auto;
                        }
                        .fi-ta-header-ctn > .fi-ta-filters-above-content-ctn {
                            flex: 1 1 100%;
                        }

                        /* === Светлая тема: усиленные контрасты === */
                        html:not(.dark) .fi-body {
                            background-color: #EEF2F7;
                        }

                        html:not(.dark) .fi-sidebar,
                        html:not(.dark) .fi-topbar {
                            background-color: #FFFFFF;
                            border-color: var(--gray-200);
                        }
                        html:not(.dark) .fi-topbar {
                            box-shadow: 0 1px 0 0 var(--gray-200);
                        }

                        /* Карточки/секции/таблицы — мягкое возвышение над фоном */
                        html:not(.dark) .fi-section,
                        html:not(.dark) .fi-wi-stats-overview-stat,
                        html:not(.dark) .fi-ta-ctn,
                        html:not(.dark) .fi-wi-table .fi-section {
                            box-shadow:
                                0 1px 2px 0 rgba(15, 23, 42, 0.04),
                                0 1px 3px 0 rgba(15, 23, 42, 0.05);
                            border-color: var(--gray-200);
                        }

                        /* Sidebar items — заметный hover с акцентным голубым */
                        html:not(.dark) .fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover,
                        html:not(.dark) .fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible {
                            background-color: var(--primary-50);
                        }
                        html:not(.dark) .fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
                        html:not(.dark) .fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover .fi-icon {
                            color: var(--primary-700);
                        }
                        html:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                            background-color: var(--primary-50);
                        }

                        /* Группы навигации — заголовки чуть жирнее и темнее */
                        html:not(.dark) .fi-sidebar-group-label {
                            color: var(--gray-700);
                            font-weight: 600;
                        }

                        /* Строки таблиц — заметный hover */
                        html:not(.dark) .fi-ta-row.fi-clickable:hover,
                        html:not(.dark) .fi-ta-row:hover {
                            background-color: var(--primary-50);
                        }

                        /* Серые кнопки (фильтры, действия) — внятный hover */
                        html:not(.dark) .fi-btn.fi-color-gray:hover {
                            background-color: var(--gray-200);
                        }
                        html:not(.dark) .fi-icon-btn:hover {
                            background-color: var(--gray-200);
                        }

                        /* Поля ввода — чуть контрастнее граница и фокус */
                        html:not(.dark) .fi-input,
                        html:not(.dark) .fi-input-wrp,
                        html:not(.dark) .fi-select-input {
                            border-color: var(--gray-300);
                        }
                        html:not(.dark) .fi-input-wrp:focus-within,
                        html:not(.dark) .fi-fo-field-wrp-input-wrp:focus-within {
                            box-shadow: 0 0 0 3px var(--primary-100);
                            border-color: var(--primary-500);
                        }

                        /* Бейджи статусов — лёгкая обводка для разборчивости */
                        html:not(.dark) .fi-badge {
                            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
                        }

                        /* Пагинация — кнопки чётче */
                        html:not(.dark) .fi-pagination-item:hover {
                            background-color: var(--gray-200);
                        }
                    </style>
                HTML,
            )
            ->navigationGroups([
                NavigationGroup::make('Учёт кадров'),
                NavigationGroup::make('Учёт клиентов'),
                NavigationGroup::make('Справочники')->collapsed(),
                NavigationGroup::make('Администрирование')->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                ContractsTrendChart::class,
                ContractsRevenueChart::class,
                TopCoursesRevenueChart::class,
                EmployeesByDepartmentChart::class,
                QualificationControlWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
