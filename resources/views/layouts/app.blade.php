<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @livewireStyles

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                color-scheme: light;
                --bg: #f3f4f6;
                --surface: #ffffff;
                --surface-alt: #f9fafb;
                --text: #111827;
                --muted: #6b7280;
                --border: #d1d5db;
                --primary: #1d4ed8;
                --primary-hover: #1e40af;
                --danger: #b91c1c;
                --danger-hover: #991b1b;
                --success-bg: #ecfdf5;
                --success-text: #166534;
                --success-border: #a7f3d0;
                --pending-bg: #fef3c7;
                --pending-text: #92400e;
                --approved-bg: #dcfce7;
                --approved-text: #166534;
                --rejected-bg: #fee2e2;
                --rejected-text: #991b1b;
                --paid-bg: #dbeafe;
                --paid-text: #1d4ed8;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: linear-gradient(180deg, #eef2ff 0%, var(--bg) 240px);
                color: var(--text);
            }

            a { color: inherit; text-decoration: none; }

            .app-shell {
                width: min(1180px, calc(100% - 2rem));
                margin: 0 auto;
                padding: 2rem 0 3rem;
            }

            .page-card,
            .section-card,
            .payment-card,
            .form-card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }

            .page-card,
            .form-card { padding: 1.5rem; }

            .page-header,
            .section-header,
            .form-header,
            .card-header,
            .page-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .page-title,
            .form-title,
            .section-title,
            .card-title {
                margin: 0;
                font-size: 1.5rem;
                line-height: 1.2;
            }

            .page-subtitle,
            .muted { color: var(--muted); }

            .page-actions,
            .actions-inline,
            .responsive-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.45rem;
                border: 1px solid transparent;
                border-radius: 12px;
                padding: 0.7rem 1rem;
                font-weight: 600;
                font-size: 0.95rem;
                cursor: pointer;
                transition: background-color 0.15s ease, border-color 0.15s ease, opacity 0.15s ease;
            }

            .btn:disabled { opacity: 0.65; cursor: wait; }

            .btn-primary { background: var(--primary); color: white; }
            .btn-primary:hover { background: var(--primary-hover); }

            .btn-secondary {
                background: white;
                color: var(--text);
                border-color: var(--border);
            }

            .btn-secondary:hover { background: var(--surface-alt); }

            .btn-danger { background: var(--danger); color: white; }
            .btn-danger:hover { background: var(--danger-hover); }

            .alert-success {
                margin-bottom: 1rem;
                padding: 0.9rem 1rem;
                border: 1px solid var(--success-border);
                border-radius: 14px;
                background: var(--success-bg);
                color: var(--success-text);
                font-weight: 600;
            }

            .grid-form { display: grid; gap: 1rem; }

            .field { display: grid; gap: 0.45rem; }
            .label { font-weight: 600; font-size: 0.95rem; }

            .input,
            .select {
                width: 100%;
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 0.8rem 0.9rem;
                font: inherit;
                background: white;
                color: var(--text);
            }

            .input:focus,
            .select:focus {
                outline: 2px solid rgba(29, 78, 216, 0.15);
                border-color: var(--primary);
            }

            .input-error,
            .select-error { border-color: var(--danger); }

            .error-message { color: var(--danger); font-size: 0.9rem; }

            .table-wrap {
                overflow-x: auto;
                border: 1px solid var(--border);
                border-radius: 16px;
                background: white;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                min-width: 760px;
            }

            .table th,
            .table td {
                padding: 0.95rem 1rem;
                text-align: left;
                vertical-align: top;
                border-bottom: 1px solid #e5e7eb;
            }

            .table th {
                background: var(--surface-alt);
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--muted);
            }

            .table tr:last-child td { border-bottom: 0; }

            .row-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.35rem 0.7rem;
                border-radius: 999px;
                font-size: 0.82rem;
                font-weight: 700;
                line-height: 1;
            }

            .badge-pending { background: var(--pending-bg); color: var(--pending-text); }
            .badge-approved { background: var(--approved-bg); color: var(--approved-text); }
            .badge-rejected { background: var(--rejected-bg); color: var(--rejected-text); }
            .badge-paid { background: var(--paid-bg); color: var(--paid-text); }

            .empty-state {
                padding: 1.25rem;
                border: 1px dashed var(--border);
                border-radius: 14px;
                color: var(--muted);
                background: var(--surface-alt);
            }

            .section-card,
            .payment-card { padding: 1.25rem; }
            .payment-card + .payment-card { margin-top: 1rem; }

            .detail-grid { display: grid; gap: 0.8rem; }

            .detail-item {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
                align-items: baseline;
            }

            .detail-label { font-weight: 700; }

            .subsection-title {
                margin: 1rem 0 0.75rem;
                font-size: 1.05rem;
            }

            .stack { display: grid; gap: 1rem; }

            @media (max-width: 720px) {
                .app-shell { width: min(100% - 1rem, 1180px); padding-top: 1rem; }

                .page-card,
                .section-card,
                .payment-card,
                .form-card { border-radius: 14px; padding: 1rem; }

                .page-header,
                .section-header,
                .form-header,
                .card-header,
                .page-top { flex-direction: column; }

                .table th,
                .table td { padding: 0.8rem; }
            }
        </style>
    </head>
    <body>
        <main class="app-shell">
            @yield('content')
        </main>

        @livewireScripts
    </body>
</html>