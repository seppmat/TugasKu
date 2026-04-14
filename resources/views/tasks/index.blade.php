<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TugasKu</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
        }
        .paper-texture {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.03;
            pointer-events: none;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        background: "#f8f6f2",
                        surface: "#f8f6f2",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f2f1ec",
                        "surface-container": "#e9e8e4",
                        "surface-container-high": "#e4e2de",
                        "surface-container-highest": "#deddd8",
                        "on-surface": "#2e2f2d",
                        "on-surface-variant": "#5b5c59",
                        primary: "#a7295a",
                        "primary-container": "#ff709f",
                        "on-primary": "#ffeff1",
                        "on-primary-container": "#4c0022",
                        secondary: "#0d6661",
                        "secondary-container": "#a4f0e9",
                        "on-secondary-container": "#005c57",
                        tertiary: "#635d15",
                        "tertiary-container": "#f7ee96",
                        "on-tertiary-container": "#5e5810",
                        error: "#b41340",
                        "error-container": "#f74b6d",
                        "on-error": "#ffefef",
                        outline: "#777774",
                        "outline-variant": "#aeadaa"
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                        lg: "1.5rem",
                        xl: "2rem",
                        full: "9999px"
                    },
                    fontFamily: {
                        headline: ["Plus Jakarta Sans", "sans-serif"],
                        body: ["Be Vietnam Pro", "sans-serif"]
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-background text-on-surface font-body min-h-screen selection:bg-primary-container selection:text-on-primary-container">
<div class="fixed inset-0 paper-texture z-0"></div>

<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200/80">
    <div class="max-w-[1480px] mx-auto px-4 md:px-6 py-3 flex items-center justify-between gap-3">
        <div class="flex items-center gap-4 lg:gap-8 min-w-0">
            <span class="text-2xl font-black tracking-tight text-primary font-headline shrink-0">TugasKu</span>
            <nav class="hidden md:flex items-center gap-4 lg:gap-6 text-sm font-semibold min-w-0">
                <button data-tab="dashboard" class="tab-link text-primary border-b-2 border-primary pb-1">Dashboard</button>
                <button data-tab="list-tugas" class="tab-link text-slate-500 hover:text-primary pb-1">List Tugas</button>
                <button data-tab="rundown-bulanan" class="tab-link text-slate-500 hover:text-primary pb-1">Rundown Bulanan</button>
                <button data-tab="jadwal-harian" class="tab-link text-slate-500 hover:text-primary pb-1">Jadwal Harian</button>
                <button data-tab="archive" class="tab-link text-slate-500 hover:text-primary pb-1">Archive</button>
                <button data-tab="settings" class="tab-link text-slate-500 hover:text-primary pb-1">Settings</button>
            </nav>
        </div>
        <div class="flex items-center gap-2 md:gap-3 shrink-0">
            <button id="newTaskButtonTop" class="h-10 px-4 md:px-5 rounded-full bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold text-xs md:text-sm shadow-md shadow-primary/20">
                Tugas Baru
            </button>
            <button data-tab="settings" class="w-10 h-10 rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">settings</span>
            </button>
            <div class="w-10 h-10 rounded-full border-2 border-primary/20 bg-pink-100 text-primary font-bold flex items-center justify-center">
                F
            </div>
        </div>
    </div>
</header>

<div class="max-w-[1480px] mx-auto relative flex min-h-[calc(100vh-64px)]">
    <aside id="leftSidebar" class="hidden lg:flex w-[280px] shrink-0 sticky top-[64px] h-[calc(100vh-64px)] border-r border-slate-200 bg-slate-50/80 backdrop-blur-sm flex-col p-4 gap-4 overflow-y-auto">
        <section class="px-3 py-2">
            <h2 class="text-lg font-headline font-bold">Filter Tugas</h2>
            <p class="text-xs text-on-surface-variant">Kontrol prioritas harian kamu.</p>
        </section>

        <nav class="space-y-1 text-sm">
            <button data-filter="all" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full bg-pink-100/60 text-primary font-semibold">
                <span class="material-symbols-outlined text-[20px]">list_alt</span> Semua Tugas
            </button>
            <button data-filter="today" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full text-slate-600 hover:bg-slate-200/50">
                <span class="material-symbols-outlined text-[20px]">today</span> Hari Ini
            </button>
            <button data-filter="urgent" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full text-slate-600 hover:bg-slate-200/50">
                <span class="material-symbols-outlined text-[20px]">priority_high</span> Urgent
            </button>
            <button data-filter="progress" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full text-slate-600 hover:bg-slate-200/50">
                <span class="material-symbols-outlined text-[20px]">pending</span> Sedang Dikerjakan
            </button>
            <button data-filter="overdue" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full text-slate-600 hover:bg-slate-200/50">
                <span class="material-symbols-outlined text-[20px]">event_busy</span> Terlambat
            </button>
            <button data-filter="selesai" class="filter-link w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-full text-slate-600 hover:bg-slate-200/50">
                <span class="material-symbols-outlined text-[20px]">check_circle</span> Selesai
            </button>
        </nav>

        <section class="rounded-xl border border-slate-200 bg-white/70 p-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-sm font-bold">Kategori</h3>
                <button id="toggleCategoryFormButton" class="text-xs font-semibold text-primary hover:underline">Tambah</button>
            </div>
            <div id="categoryFormWrap" class="hidden mt-3 space-y-2">
                <input id="categoryNameInput" class="w-full h-10 rounded-lg border-0 bg-surface-container-low focus:ring-2 focus:ring-primary/20" placeholder="Nama kategori">
                <div class="flex gap-2">
                    <button id="saveCategoryButton" class="h-9 px-3 rounded-full bg-primary text-on-primary text-xs font-bold">Simpan</button>
                    <button id="cancelCategoryButton" class="h-9 px-3 rounded-full bg-slate-200 text-slate-600 text-xs font-bold">Batal</button>
                </div>
            </div>
            <div id="categoryList" class="mt-3 flex flex-wrap gap-2"></div>
        </section>

        <button id="markAllDoneButton" class="h-11 rounded-xl bg-secondary-container text-on-secondary-container text-sm font-semibold hover:brightness-95 transition-all">
            Mark All Done
        </button>
    </aside>

    <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8 xl:p-10 pb-28 md:pb-10 z-10">
        <div class="space-y-8">
            <section data-panel="dashboard" class="panel space-y-6">
                <div>
                    <h1 class="font-headline font-black tracking-tight text-[clamp(1.75rem,4vw,2.75rem)] break-words">Tugas Hari Ini</h1>
                    <p class="text-sm md:text-base text-on-surface-variant mt-1">Playful, soft, dan tetap fokus produktif.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 md:gap-4">
                    <article class="rounded-xl bg-secondary-container p-4 md:p-5 min-h-[132px] flex flex-col justify-between">
                        <span class="material-symbols-outlined self-end text-on-secondary-container">auto_graph</span>
                        <div>
                            <p id="metricCompletion" class="font-headline font-black text-3xl md:text-4xl text-on-secondary-container">0%</p>
                            <p class="text-sm text-on-secondary-container/80">Penyelesaian hari ini</p>
                        </div>
                    </article>
                    <article class="rounded-xl bg-error-container p-4 md:p-5 min-h-[132px] flex flex-col justify-between">
                        <span class="material-symbols-outlined self-end text-on-error">priority_high</span>
                        <div>
                            <p id="metricOverdue" class="font-headline font-black text-3xl md:text-4xl text-on-error">0</p>
                            <p class="text-sm text-on-error/80">Tugas terlambat</p>
                        </div>
                    </article>
                    <article class="rounded-xl bg-tertiary-container p-4 md:p-5 min-h-[132px] flex flex-col justify-between sm:col-span-2 xl:col-span-1">
                        <span class="material-symbols-outlined self-end text-on-tertiary-container">workspace_premium</span>
                        <div>
                            <p id="metricConsistency" class="font-headline font-black text-3xl md:text-4xl text-on-tertiary-container">Solid</p>
                            <p class="text-sm text-on-tertiary-container/80">Konsistensi mingguan</p>
                        </div>
                    </article>
                </div>

                <section class="rounded-xl bg-white/80 border border-slate-200 p-4 md:p-5 space-y-3">
                    <h2 class="text-lg font-headline font-bold">Tambah Tugas Cepat</h2>
                    <div class="space-y-2">
                        <input id="quickTaskInput" class="h-11 w-full rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20" placeholder="Contoh: Kerjakan revisi laporan meteorologi">
                        <div class="flex flex-wrap items-center gap-2">
                            <select id="quickCategorySelect" class="h-11 min-w-[170px] grow rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20"></select>
                            <input id="quickDueInput" type="date" class="h-11 min-w-[170px] rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20">
                            <label class="h-11 px-4 rounded-full bg-surface-container-low flex items-center justify-center gap-2 text-sm">
                                <input id="quickUrgentInput" type="checkbox" class="rounded text-primary"> Urgent
                            </label>
                            <button id="quickAddButton" class="h-11 px-5 rounded-full bg-primary text-on-primary font-bold text-sm">Tambah</button>
                        </div>
                    </div>
                </section>

                <section class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-headline font-bold">Task Feed</h3>
                        <span class="text-xs text-on-surface-variant">Filter aktif akan berpengaruh</span>
                    </div>
                    <div id="dashboardTaskList" class="space-y-3"></div>
                </section>
            </section>

            <section data-panel="list-tugas" class="panel hidden space-y-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h2 class="text-3xl font-headline font-black">List Tugas</h2>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input id="taskSearchInput" class="h-10 rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20" placeholder="Cari tugas">
                        <select id="taskStatusFilter" class="h-10 rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20">
                            <option value="all">Semua Status</option>
                            <option value="belum">Belum Selesai</option>
                            <option value="progress">On Progress</option>
                            <option value="selesai">Selesai</option>
                        </select>
                        <button id="exportButton" class="h-10 px-4 rounded-full bg-surface-container text-sm font-semibold hover:bg-surface-container-high">Export Data</button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white/80">
                    <table class="min-w-[980px] w-full text-sm">
                        <thead class="bg-surface-container-low text-left">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tugas</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Mulai</th>
                            <th class="px-4 py-3">Deadline</th>
                            <th class="px-4 py-3">Selesai</th>
                            <th class="px-4 py-3">Urgent</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="taskTableBody"></tbody>
                    </table>
                </div>
            </section>

            <section data-panel="rundown-bulanan" class="panel hidden space-y-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h2 class="text-3xl font-headline font-black">Rundown Bulanan</h2>
                    <div id="monthTabWrapper" class="flex flex-wrap gap-2"></div>
                </div>

                <section class="rounded-xl border border-slate-200 bg-white/80 p-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div>
                            <p class="text-xs text-on-surface-variant">Tanggal aktif</p>
                            <h3 id="selectedDateLabelRundown" class="font-headline font-bold text-xl">-</h3>
                        </div>
                        <div class="text-xs text-on-surface-variant">Catatan di sini otomatis tampil sebagai marker di kalender.</div>
                    </div>
                    <div class="mt-3 grid gap-2">
                        <textarea id="rundownNoteInput" rows="3" class="w-full rounded-xl border-0 bg-surface-container-low focus:ring-2 focus:ring-primary/20 px-4 py-3 resize-y" placeholder="Catatan tanggal ini..."></textarea>
                        <div class="flex gap-2">
                            <button id="saveRundownNoteButton" class="h-10 px-4 rounded-full bg-primary text-on-primary text-sm font-bold">Simpan Catatan</button>
                            <button id="deleteRundownNoteButton" class="h-10 px-4 rounded-full bg-slate-200 text-slate-700 text-sm font-bold">Hapus Catatan</button>
                        </div>
                    </div>
                </section>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white/80">
                    <table class="min-w-[980px] w-full text-sm">
                        <thead class="bg-surface-container-low text-left">
                        <tr>
                            <th class="px-4 py-3">Hari</th>
                            <th class="px-4 py-3 text-center">Total Tugas</th>
                            <th class="px-4 py-3 text-center text-red-600">Belum</th>
                            <th class="px-4 py-3 text-center text-amber-600">Progress</th>
                            <th class="px-4 py-3 text-center text-emerald-600">Selesai</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="monthlyTableBody"></tbody>
                        <tfoot class="bg-surface-container-low">
                        <tr id="monthlySummaryRow" class="font-bold"></tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <section data-panel="jadwal-harian" class="panel hidden space-y-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h2 class="text-3xl font-headline font-black">Jadwal Harian</h2>
                    <div class="inline-flex bg-surface-container-low p-1 rounded-full">
                        <button data-schedule-tab="weekday" class="schedule-tab h-9 px-4 rounded-full bg-white shadow-sm text-sm font-semibold">Weekday</button>
                        <button data-schedule-tab="weekend" class="schedule-tab h-9 px-4 rounded-full text-sm font-semibold text-on-surface-variant">Weekend</button>
                    </div>
                </div>

                <section class="rounded-xl border border-slate-200 bg-white/80 p-4 space-y-3">
                    <h3 class="font-bold">Input Jadwal</h3>
                    <form id="scheduleForm" class="grid grid-cols-1 md:grid-cols-[170px_minmax(0,1fr)_auto] gap-2">
                        <input id="scheduleTimeInput" type="text" class="h-10 rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20" placeholder="07.00 - 08.00" required>
                        <input id="scheduleActivityInput" type="text" class="h-10 rounded-full border-0 bg-surface-container-low px-4 focus:ring-2 focus:ring-primary/20" placeholder="Aktivitas" required>
                        <button class="h-10 px-4 rounded-full bg-primary text-on-primary text-sm font-bold">Tambah</button>
                    </form>
                </section>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white/80">
                    <table class="min-w-[700px] w-full text-sm">
                        <thead class="bg-surface-container-low text-left">
                        <tr>
                            <th class="px-4 py-3 w-48">Waktu</th>
                            <th class="px-4 py-3">Aktivitas</th>
                            <th class="px-4 py-3 w-24">Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="scheduleTableBody"></tbody>
                    </table>
                </div>
            </section>

            <section data-panel="archive" class="panel hidden space-y-4">
                <h2 class="text-3xl font-headline font-black">Archive</h2>
                <div id="archiveList" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
            </section>

            <section data-panel="settings" class="panel hidden space-y-4">
                <h2 class="text-3xl font-headline font-black">Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <article class="rounded-xl border border-slate-200 bg-white/80 p-5">
                        <h3 class="font-bold mb-3">Notifikasi</h3>
                        <label class="flex items-center justify-between py-2 text-sm">
                            <span>Reminder deadline H-1</span>
                            <input type="checkbox" class="rounded text-primary" checked>
                        </label>
                        <label class="flex items-center justify-between py-2 text-sm">
                            <span>Reminder urgent</span>
                            <input type="checkbox" class="rounded text-primary" checked>
                        </label>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white/80 p-5">
                        <h3 class="font-bold mb-3">Preferensi</h3>
                        <label class="block text-sm mb-2">Bahasa</label>
                        <select class="w-full rounded-lg border-0 bg-surface-container-low">
                            <option>Indonesia</option>
                            <option>English</option>
                        </select>
                    </article>
                </div>
            </section>
        </div>
    </main>

    <aside id="rightSidebar" class="hidden xl:flex w-[330px] shrink-0 sticky top-[64px] h-[calc(100vh-64px)] border-l border-slate-200 bg-surface-container-low/60 backdrop-blur-sm p-5 flex-col gap-5 overflow-y-auto">
        <section class="rounded-xl bg-white/85 border border-slate-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <button id="miniPrevMonthButton" class="w-8 h-8 rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container">&lt;</button>
                <h3 id="miniCalendarTitle" class="font-headline font-bold">-</h3>
                <button id="miniNextMonthButton" class="w-8 h-8 rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container">&gt;</button>
            </div>
            <div class="grid grid-cols-7 text-[11px] text-center font-semibold text-on-surface-variant mb-2">
                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>
            <div id="miniCalendarGrid" class="grid grid-cols-7 gap-1.5 text-xs"></div>
        </section>

        <section class="rounded-xl bg-white/85 border border-slate-200 p-4 space-y-3">
            <div>
                <p class="text-xs text-on-surface-variant">Catatan Kalender</p>
                <h4 id="selectedDateLabelCalendar" class="font-bold">-</h4>
            </div>
            <textarea id="calendarNoteInput" rows="4" class="w-full rounded-xl border-0 bg-surface-container-low focus:ring-2 focus:ring-primary/20 px-3 py-2" placeholder="Tulis note untuk tanggal ini..."></textarea>
            <div class="flex gap-2">
                <button id="saveCalendarNoteButton" class="h-9 px-3 rounded-full bg-primary text-on-primary text-xs font-bold">Simpan</button>
                <button id="deleteCalendarNoteButton" class="h-9 px-3 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">Hapus</button>
            </div>
        </section>

        <section class="rounded-xl bg-white/85 border border-slate-200 p-4 space-y-3">
            <h3 class="font-headline font-bold">Deadline Mendatang</h3>
            <div id="deadlineList" class="space-y-2"></div>
        </section>
    </aside>
</div>

<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/85 backdrop-blur-xl border-t border-slate-200 z-50 flex justify-around items-center px-2 py-2.5">
    <button data-tab="dashboard" class="mobile-tab flex flex-col items-center gap-1 text-primary">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
        <span class="text-[10px] font-semibold">Dashboard</span>
    </button>
    <button data-tab="list-tugas" class="mobile-tab flex flex-col items-center gap-1 text-slate-400">
        <span class="material-symbols-outlined">list_alt</span>
        <span class="text-[10px] font-semibold">Tugas</span>
    </button>
    <button id="mobileQuickAdd" class="-mt-7 w-14 h-14 rounded-full bg-primary text-on-primary shadow-lg shadow-primary/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-3xl">add</span>
    </button>
    <button data-tab="rundown-bulanan" class="mobile-tab flex flex-col items-center gap-1 text-slate-400">
        <span class="material-symbols-outlined">calendar_month</span>
        <span class="text-[10px] font-semibold">Rundown</span>
    </button>
    <button data-tab="settings" class="mobile-tab flex flex-col items-center gap-1 text-slate-400">
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-semibold">Profil</span>
    </button>
</nav>

<div id="toast" class="fixed right-4 top-20 z-[60] hidden rounded-xl bg-on-surface text-white text-sm px-4 py-2 shadow-lg"></div>

<script src="{{ asset('js/tugasku.js') }}"></script>
</body>
</html>
