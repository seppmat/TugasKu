(() => {
    const STORAGE_KEY = "tugasku.app.v3";
    const months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    const today = new Date();
    const todayISO = toISO(today);
    const statusMap = {
        belum: { label: "Belum Selesai", chip: "bg-red-100 text-red-700" },
        progress: { label: "On Progress", chip: "bg-amber-100 text-amber-700" },
        selesai: { label: "Selesai", chip: "bg-emerald-100 text-emerald-700" }
    };

    const defaults = {
        activeTab: "dashboard",
        activeFilter: "all",
        activeMonth: today.getMonth(),
        selectedDateISO: todayISO,
        activeScheduleTab: "weekday",
        searchQuery: "",
        taskStatusFilter: "all",
        categories: ["Akademik", "Non Akademik", "Personal"],
        tasks: [
            { id: uid(), title: "Finalisasi notulen pembuatan notes", category: "Akademik", startDate: todayISO, dueDate: todayISO, status: "progress", urgent: true, draft: false },
            { id: uid(), title: "Belajar Bahasa Inggris 20 menit", category: "Akademik", startDate: todayISO, dueDate: toISO(addDays(today, 1)), status: "belum", urgent: false, draft: false },
            { id: uid(), title: "Draft pertanyaan untuk senior kompi", category: "Personal", startDate: todayISO, dueDate: toISO(addDays(today, 2)), status: "belum", urgent: false, draft: true }
        ],
        schedules: {
            weekday: [{ id: uid(), time: "07.00 - 08.00", activity: "Belajar topik sulit" }],
            weekend: [{ id: uid(), time: "08.00 - 10.00", activity: "Evaluasi mingguan" }]
        },
        notesByDate: {}
    };
    const state = load();

    const el = {
        panels: qsa(".panel"),
        tabs: qsa("[data-tab]"),
        mobileTabs: qsa(".mobile-tab"),
        filters: qsa(".filter-link"),
        leftSidebar: id("leftSidebar"),
        rightSidebar: id("rightSidebar"),
        metricCompletion: id("metricCompletion"),
        metricOverdue: id("metricOverdue"),
        metricConsistency: id("metricConsistency"),
        newTaskTop: id("newTaskButtonTop"),
        quickTaskInput: id("quickTaskInput"),
        quickCategorySelect: id("quickCategorySelect"),
        quickDueInput: id("quickDueInput"),
        quickUrgentInput: id("quickUrgentInput"),
        quickAddButton: id("quickAddButton"),
        dashboardTaskList: id("dashboardTaskList"),
        taskSearchInput: id("taskSearchInput"),
        taskStatusFilter: id("taskStatusFilter"),
        taskTableBody: id("taskTableBody"),
        exportButton: id("exportButton"),
        markAllDoneButton: id("markAllDoneButton"),
        archiveList: id("archiveList"),
        monthTabWrapper: id("monthTabWrapper"),
        monthlyTableBody: id("monthlyTableBody"),
        monthlySummaryRow: id("monthlySummaryRow"),
        selectedDateLabelRundown: id("selectedDateLabelRundown"),
        selectedDateLabelCalendar: id("selectedDateLabelCalendar"),
        rundownNoteInput: id("rundownNoteInput"),
        calendarNoteInput: id("calendarNoteInput"),
        saveRundownNoteButton: id("saveRundownNoteButton"),
        deleteRundownNoteButton: id("deleteRundownNoteButton"),
        saveCalendarNoteButton: id("saveCalendarNoteButton"),
        deleteCalendarNoteButton: id("deleteCalendarNoteButton"),
        miniCalendarTitle: id("miniCalendarTitle"),
        miniCalendarGrid: id("miniCalendarGrid"),
        miniPrevMonthButton: id("miniPrevMonthButton"),
        miniNextMonthButton: id("miniNextMonthButton"),
        deadlineList: id("deadlineList"),
        scheduleTabs: qsa(".schedule-tab"),
        scheduleForm: id("scheduleForm"),
        scheduleTimeInput: id("scheduleTimeInput"),
        scheduleActivityInput: id("scheduleActivityInput"),
        scheduleTableBody: id("scheduleTableBody"),
        categoryList: id("categoryList"),
        toggleCategoryFormButton: id("toggleCategoryFormButton"),
        categoryFormWrap: id("categoryFormWrap"),
        categoryNameInput: id("categoryNameInput"),
        saveCategoryButton: id("saveCategoryButton"),
        cancelCategoryButton: id("cancelCategoryButton"),
        mobileQuickAdd: id("mobileQuickAdd"),
        toast: id("toast")
    };

    init();

    function init() {
        bind();
        const allowedFilters = ["all", "today", "urgent", "progress", "overdue", "selesai"];
        if (!allowedFilters.includes(state.activeFilter)) state.activeFilter = "all";
        state.selectedDateISO = fitDateToMonth(state.selectedDateISO);
        renderAll();
    }

    function bind() {
        el.tabs.forEach((b) => b.addEventListener("click", () => { state.activeTab = b.dataset.tab; renderPanels(); renderShellVisibility(); renderTabState(); save(); }));
        el.newTaskTop.addEventListener("click", () => { state.activeTab = "dashboard"; renderPanels(); renderShellVisibility(); renderTabState(); el.quickTaskInput.focus(); });
        el.mobileQuickAdd.addEventListener("click", () => { state.activeTab = "dashboard"; renderPanels(); renderShellVisibility(); renderTabState(); el.quickTaskInput.focus(); });
        el.filters.forEach((b) => b.addEventListener("click", () => { state.activeFilter = b.dataset.filter || "all"; renderFilterState(); renderDashboardTasks(); renderTaskTable(); save(); }));
        el.quickAddButton.addEventListener("click", addQuickTask);
        el.quickTaskInput.addEventListener("keydown", (e) => { if (e.key === "Enter") addQuickTask(); });
        el.taskSearchInput.addEventListener("input", (e) => { state.searchQuery = e.target.value.trim().toLowerCase(); renderTaskTable(); });
        el.taskStatusFilter.addEventListener("change", (e) => { state.taskStatusFilter = e.target.value; renderTaskTable(); });
        el.markAllDoneButton.addEventListener("click", () => { state.tasks = state.tasks.map((t) => ({ ...t, status: "selesai" })); persist(); toast("Semua tugas ditandai selesai."); });
        el.dashboardTaskList.addEventListener("click", onDashboardAction);
        el.taskTableBody.addEventListener("click", onTaskTableClick);
        el.taskTableBody.addEventListener("change", onTaskTableChange);
        el.exportButton.addEventListener("click", exportData);
        el.monthTabWrapper.addEventListener("click", onMonthTabClick);
        el.monthlyTableBody.addEventListener("click", onPickDateFromTable);
        el.saveRundownNoteButton.addEventListener("click", () => saveNote(el.rundownNoteInput.value));
        el.saveCalendarNoteButton.addEventListener("click", () => saveNote(el.calendarNoteInput.value));
        el.deleteRundownNoteButton.addEventListener("click", deleteNote);
        el.deleteCalendarNoteButton.addEventListener("click", deleteNote);
        el.miniPrevMonthButton.addEventListener("click", () => { state.activeMonth = (state.activeMonth + 11) % 12; state.selectedDateISO = fitDateToMonth(state.selectedDateISO); persist(); });
        el.miniNextMonthButton.addEventListener("click", () => { state.activeMonth = (state.activeMonth + 1) % 12; state.selectedDateISO = fitDateToMonth(state.selectedDateISO); persist(); });
        el.miniCalendarGrid.addEventListener("click", onCalendarPick);
        el.scheduleTabs.forEach((b) => b.addEventListener("click", () => { state.activeScheduleTab = b.dataset.scheduleTab || "weekday"; renderSchedule(); save(); }));
        el.scheduleForm.addEventListener("submit", onScheduleSubmit);
        el.scheduleTableBody.addEventListener("click", onScheduleRemove);
        el.toggleCategoryFormButton.addEventListener("click", () => { el.categoryFormWrap.classList.toggle("hidden"); if (!el.categoryFormWrap.classList.contains("hidden")) el.categoryNameInput.focus(); });
        el.cancelCategoryButton.addEventListener("click", () => { el.categoryFormWrap.classList.add("hidden"); el.categoryNameInput.value = ""; });
        el.saveCategoryButton.addEventListener("click", onCategorySave);
        el.categoryList.addEventListener("click", onCategoryRemove);
    }

    function renderAll() {
        renderPanels();
        renderShellVisibility();
        renderTabState();
        renderFilterState();
        renderCategories();
        renderQuickCategoryOptions();
        renderMetrics();
        renderDashboardTasks();
        renderTaskTable();
        renderArchive();
        renderMonthTabs();
        renderMonthlyTable();
        renderSelectedDate();
        renderMiniCalendar();
        renderDeadlines();
        renderSchedule();
        if (!el.quickDueInput.value) el.quickDueInput.value = todayISO;
    }

    function renderPanels() { el.panels.forEach((p) => p.classList.toggle("hidden", p.dataset.panel !== state.activeTab)); }
    function renderShellVisibility() {
        const leftTabs = ["dashboard", "list-tugas"];
        const rightTabs = ["dashboard", "rundown-bulanan"];
        if (el.leftSidebar) el.leftSidebar.style.display = leftTabs.includes(state.activeTab) ? "" : "none";
        if (el.rightSidebar) el.rightSidebar.style.display = rightTabs.includes(state.activeTab) ? "" : "none";
    }
    function renderTabState() {
        el.tabs.forEach((b) => {
            const active = b.dataset.tab === state.activeTab;
            if (b.classList.contains("tab-link")) {
                b.classList.toggle("text-primary", active); b.classList.toggle("border-b-2", active); b.classList.toggle("border-primary", active); b.classList.toggle("font-bold", active); b.classList.toggle("text-slate-500", !active);
            }
            if (b.classList.contains("mobile-tab")) {
                b.classList.toggle("text-primary", active); b.classList.toggle("text-slate-400", !active);
                const icon = b.querySelector(".material-symbols-outlined");
                if (icon) icon.style.fontVariationSettings = active ? "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24" : "'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24";
            }
        });
    }
    function renderFilterState() {
        el.filters.forEach((b) => {
            const active = (b.dataset.filter || "all") === state.activeFilter;
            b.classList.toggle("bg-pink-100/60", active); b.classList.toggle("text-primary", active); b.classList.toggle("font-semibold", active); b.classList.toggle("text-slate-600", !active);
        });
    }

    function renderCategories() {
        el.categoryList.innerHTML = state.categories.map((c) => `
            <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-low text-xs">
                <span class="max-w-[120px] truncate">${esc(c)}</span>
                <button data-remove-category="${esc(c)}" class="text-slate-500 hover:text-red-600"><span class="material-symbols-outlined text-[14px] align-middle">close</span></button>
            </div>
        `).join("");
    }

    function renderQuickCategoryOptions() {
        const selected = el.quickCategorySelect.value || state.categories[0];
        el.quickCategorySelect.innerHTML = state.categories.map((c) => `<option>${esc(c)}</option>`).join("");
        if (state.categories.includes(selected)) el.quickCategorySelect.value = selected;
    }

    function addQuickTask() {
        const title = el.quickTaskInput.value.trim();
        if (!title) return;
        state.tasks.unshift({
            id: uid(),
            title,
            category: el.quickCategorySelect.value || "Personal",
            startDate: todayISO,
            dueDate: el.quickDueInput.value || todayISO,
            status: "belum",
            urgent: Boolean(el.quickUrgentInput.checked),
            draft: false
        });
        el.quickTaskInput.value = "";
        el.quickUrgentInput.checked = false;
        persist();
        toast("Tugas baru berhasil ditambahkan.");
    }

    function renderMetrics() {
        const total = state.tasks.length || 1;
        const done = state.tasks.filter((t) => t.status === "selesai").length;
        const completion = Math.round((done / total) * 100);
        const overdue = state.tasks.filter((t) => isOverdue(t)).length;
        el.metricCompletion.textContent = `${completion}%`;
        el.metricOverdue.textContent = String(overdue);
        el.metricConsistency.textContent = completion >= 75 ? "Solid" : completion >= 50 ? "Stabil" : "Mulai";
    }

    function renderDashboardTasks() {
        const items = filteredBySidebar().slice(0, 8);
        if (!items.length) {
            el.dashboardTaskList.innerHTML = `<div class="rounded-xl border border-dashed border-slate-300 bg-white/60 p-4 text-sm text-on-surface-variant">Tidak ada tugas pada filter ini.</div>`;
            return;
        }
        el.dashboardTaskList.innerHTML = items.map((t) => {
            const done = t.status === "selesai";
            const chip = statusMap[t.status] || statusMap.belum;
            return `
                <article class="rounded-xl border border-slate-200 ${done ? "bg-emerald-50" : "bg-white/80"} p-4 flex items-start gap-3">
                    <button data-action="toggle-done" data-id="${t.id}" class="mt-1 w-6 h-6 rounded-full border-2 ${done ? "bg-emerald-600 border-emerald-600 text-white" : "bg-white border-primary"} shrink-0">${done ? '<span class="material-symbols-outlined text-[16px] align-middle">check</span>' : ""}</button>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-semibold break-words ${done ? "line-through text-on-surface/60" : ""}">${esc(t.title)}</h4>
                        <p class="text-xs text-on-surface-variant mt-1">${esc(t.category)} - ${fmt(t.dueDate)}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button data-action="toggle-urgent" data-id="${t.id}" class="px-2.5 py-1 rounded-full text-[11px] font-bold ${t.urgent ? "bg-primary-container/40 text-on-primary-container" : "bg-slate-200 text-slate-700"}">${t.urgent ? "Urgent" : "Normal"}</button>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold ${chip.chip}">${chip.label}</span>
                        <button data-action="delete" data-id="${t.id}" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-red-100 text-slate-500 hover:text-red-600"><span class="material-symbols-outlined text-[16px] align-middle">close</span></button>
                    </div>
                </article>
            `;
        }).join("");
    }

    function onDashboardAction(e) {
        const btn = e.target.closest("[data-action]");
        if (!btn) return;
        const idVal = btn.getAttribute("data-id");
        const action = btn.getAttribute("data-action");
        const idNum = Number(idVal);
        if (!idNum || !action) return;
        if (action === "toggle-done") state.tasks = state.tasks.map((t) => t.id === idNum ? ({ ...t, status: t.status === "selesai" ? "progress" : "selesai" }) : t);
        if (action === "toggle-urgent") state.tasks = state.tasks.map((t) => t.id === idNum ? ({ ...t, urgent: !t.urgent }) : t);
        if (action === "delete") state.tasks = state.tasks.filter((t) => t.id !== idNum);
        persist();
    }

    function renderTaskTable() {
        const items = filteredBySearchStatus();
        if (!items.length) {
            el.taskTableBody.innerHTML = `<tr><td colspan="9" class="px-4 py-5 text-sm text-on-surface-variant">Data tugas tidak ditemukan.</td></tr>`;
            return;
        }
        el.taskTableBody.innerHTML = items.map((t, i) => `
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3">${i + 1}</td>
                <td class="px-4 py-3 max-w-[340px]"><p class="font-medium break-words">${esc(t.title)}</p></td>
                <td class="px-4 py-3">${esc(t.category)}</td>
                <td class="px-4 py-3">${fmt(t.startDate)}</td>
                <td class="px-4 py-3">${fmt(t.dueDate)}</td>
                <td class="px-4 py-3 text-center"><input data-id="${t.id}" data-field="done" type="checkbox" class="rounded text-primary" ${t.status === "selesai" ? "checked" : ""}></td>
                <td class="px-4 py-3 text-center"><input data-id="${t.id}" data-field="urgent" type="checkbox" class="rounded text-primary" ${t.urgent ? "checked" : ""}></td>
                <td class="px-4 py-3">
                    <select data-id="${t.id}" data-field="status" class="w-full rounded-lg border-0 bg-surface-container-low">
                        ${Object.entries(statusMap).map(([k, v]) => `<option value="${k}" ${t.status === k ? "selected" : ""}>${v.label}</option>`).join("")}
                    </select>
                </td>
                <td class="px-4 py-3"><button data-remove-task="${t.id}" class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Hapus</button></td>
            </tr>
        `).join("");
    }

    function onTaskTableClick(e) {
        const rm = e.target.closest("[data-remove-task]");
        if (!rm) return;
        const idVal = rm.getAttribute("data-remove-task");
        const idNum = Number(idVal);
        state.tasks = state.tasks.filter((t) => t.id !== idNum);
        persist();
    }

    function onTaskTableChange(e) {
        const t = e.target;
        const idVal = t.getAttribute("data-id");
        const field = t.getAttribute("data-field");
        const idNum = Number(idVal);
        if (!idNum || !field) return;
        state.tasks = state.tasks.map((row) => {
            if (row.id !== idNum) return row;
            if (field === "done") return { ...row, status: t.checked ? "selesai" : "progress" };
            if (field === "urgent") return { ...row, urgent: Boolean(t.checked) };
            if (field === "status") return { ...row, status: t.value };
            return row;
        });
        persist();
    }

    function renderArchive() {
        const list = state.tasks.filter((t) => t.status === "selesai");
        if (!list.length) {
            el.archiveList.innerHTML = `<div class="rounded-xl bg-white/80 border border-dashed border-slate-300 p-4 text-sm text-on-surface-variant">Belum ada data archive.</div>`;
            return;
        }
        el.archiveList.innerHTML = list.map((t) => `
            <article class="rounded-xl bg-white/85 border border-slate-200 p-4 space-y-2">
                <p class="text-xs text-on-surface-variant">${fmt(t.dueDate)}</p>
                <h3 class="font-semibold break-words">${esc(t.title)}</h3>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Selesai</span>
                    <button data-restore-task="${t.id}" class="text-xs font-bold text-primary hover:underline">Restore</button>
                </div>
            </article>
        `).join("");
        el.archiveList.querySelectorAll("[data-restore-task]").forEach((b) => b.addEventListener("click", () => {
            const idVal = b.getAttribute("data-restore-task");
            const idNum = Number(idVal);
            state.tasks = state.tasks.map((t) => t.id === idNum ? ({ ...t, status: "progress" }) : t);
            persist();
        }));
    }

    function onMonthTabClick(e) {
        const b = e.target.closest("[data-month]");
        if (!b) return;
        state.activeMonth = Number(b.dataset.month);
        state.selectedDateISO = fitDateToMonth(state.selectedDateISO);
        persist();
    }

    function renderMonthTabs() {
        el.monthTabWrapper.innerHTML = months.map((m, i) => `
            <button data-month="${i}" class="h-8 px-3 rounded-full text-xs font-bold ${i === state.activeMonth ? "bg-primary text-on-primary" : "bg-surface-container-low text-on-surface-variant"}">${m.slice(0, 3)}</button>
        `).join("");
    }

    function renderMonthlyTable() {
        const y = new Date().getFullYear();
        const maxDay = new Date(y, state.activeMonth + 1, 0).getDate();
        let total = 0, belum = 0, progress = 0, selesai = 0;
        const rows = [];
        for (let d = 1; d <= maxDay; d += 1) {
            const iso = toISO(new Date(y, state.activeMonth, d));
            const dayTasks = state.tasks.filter((t) => t.dueDate === iso);
            const b = dayTasks.filter((t) => t.status === "belum").length;
            const p = dayTasks.filter((t) => t.status === "progress").length;
            const s = dayTasks.filter((t) => t.status === "selesai").length;
            const note = state.notesByDate[iso] || "";
            total += dayTasks.length; belum += b; progress += p; selesai += s;
            rows.push(`
                <tr class="border-t border-slate-100 ${iso === state.selectedDateISO ? "bg-primary/5" : ""}">
                    <td class="px-4 py-3">${d} ${months[state.activeMonth]}</td>
                    <td class="px-4 py-3 text-center">${dayTasks.length}</td>
                    <td class="px-4 py-3 text-center">${b}</td>
                    <td class="px-4 py-3 text-center">${p}</td>
                    <td class="px-4 py-3 text-center">${s}</td>
                    <td class="px-4 py-3 max-w-[340px]"><p class="text-xs text-on-surface-variant truncate">${esc(note || "-")}</p></td>
                    <td class="px-4 py-3"><button data-pick-date="${iso}" class="h-8 px-3 rounded-full text-xs font-bold bg-surface-container-low hover:bg-surface-container">Pilih</button></td>
                </tr>
            `);
        }
        el.monthlyTableBody.innerHTML = rows.join("");
        el.monthlySummaryRow.innerHTML = `
            <td class="px-4 py-3">Summary</td>
            <td class="px-4 py-3 text-center">${total}</td>
            <td class="px-4 py-3 text-center">${belum}</td>
            <td class="px-4 py-3 text-center">${progress}</td>
            <td class="px-4 py-3 text-center">${selesai}</td>
            <td class="px-4 py-3">Total catatan: ${monthNoteCount()}</td>
            <td class="px-4 py-3">-</td>
        `;
    }

    function onPickDateFromTable(e) {
        const b = e.target.closest("[data-pick-date]");
        if (!b) return;
        const iso = b.getAttribute("data-pick-date");
        if (!iso) return;
        state.selectedDateISO = iso;
        save();
        renderSelectedDate();
        renderMiniCalendar();
        renderMonthlyTable();
    }

    function renderSelectedDate() {
        const label = fmtLong(state.selectedDateISO);
        const note = state.notesByDate[state.selectedDateISO] || "";
        el.selectedDateLabelRundown.textContent = label;
        el.selectedDateLabelCalendar.textContent = label;
        el.rundownNoteInput.value = note;
        el.calendarNoteInput.value = note;
    }

    function saveNote(raw) {
        const text = raw.trim();
        if (!text) { toast("Catatan kosong, tidak disimpan."); return; }
        state.notesByDate[state.selectedDateISO] = text;
        persist();
        toast("Catatan tanggal tersimpan.");
    }

    function deleteNote() {
        delete state.notesByDate[state.selectedDateISO];
        persist();
        toast("Catatan tanggal dihapus.");
    }

    function onCalendarPick(e) {
        const b = e.target.closest("[data-day-iso]");
        if (!b) return;
        const iso = b.getAttribute("data-day-iso");
        if (!iso) return;
        state.selectedDateISO = iso;
        save();
        renderSelectedDate();
        renderMiniCalendar();
        renderMonthlyTable();
    }

    function renderMiniCalendar() {
        const y = new Date().getFullYear();
        const m = state.activeMonth;
        el.miniCalendarTitle.textContent = `${months[m]} ${y}`;
        const first = new Date(y, m, 1);
        const firstWeekday = first.getDay();
        const total = new Date(y, m + 1, 0).getDate();
        const cells = [];
        for (let i = 0; i < firstWeekday; i += 1) cells.push(`<div class="h-8"></div>`);
        for (let d = 1; d <= total; d += 1) {
            const iso = toISO(new Date(y, m, d));
            const isToday = iso === todayISO;
            const selected = iso === state.selectedDateISO;
            const hasTask = state.tasks.some((t) => t.dueDate === iso);
            const hasNote = Boolean(state.notesByDate[iso]);
            cells.push(`
                <button data-day-iso="${iso}" class="h-8 rounded-lg text-[11px] flex items-center justify-center relative
                    ${selected ? "bg-primary text-on-primary font-bold" : "bg-white/60 hover:bg-surface-container"}
                    ${isToday && !selected ? "ring-1 ring-primary/40" : ""}">
                    ${d}
                    ${hasTask ? '<span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-amber-500"></span>' : ""}
                    ${hasNote ? '<span class="absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-emerald-500"></span>' : ""}
                </button>
            `);
        }
        el.miniCalendarGrid.innerHTML = cells.join("");
    }

    function renderDeadlines() {
        const list = state.tasks
            .filter((t) => t.status !== "selesai")
            .sort((a, b) => parseISODate(a.dueDate) - parseISODate(b.dueDate))
            .slice(0, 5);
        if (!list.length) {
            el.deadlineList.innerHTML = `<p class="text-sm text-on-surface-variant">Tidak ada deadline dekat.</p>`;
            return;
        }
        el.deadlineList.innerHTML = list.map((t) => `
            <article class="rounded-lg bg-surface-container-low p-3">
                <p class="font-semibold text-sm break-words">${esc(t.title)}</p>
                <p class="text-xs ${isOverdue(t) ? "text-error font-semibold" : "text-on-surface-variant"}">${fmt(t.dueDate)}</p>
            </article>
        `).join("");
    }

    function onScheduleSubmit(e) {
        e.preventDefault();
        const time = el.scheduleTimeInput.value.trim();
        const activity = el.scheduleActivityInput.value.trim();
        if (!time || !activity) return;
        state.schedules[state.activeScheduleTab].push({ id: uid(), time, activity });
        el.scheduleTimeInput.value = "";
        el.scheduleActivityInput.value = "";
        persist();
        toast("Jadwal berhasil ditambahkan.");
    }

    function onScheduleRemove(e) {
        const b = e.target.closest("[data-remove-schedule]");
        if (!b) return;
        const idVal = b.getAttribute("data-remove-schedule");
        const idNum = Number(idVal);
        state.schedules[state.activeScheduleTab] = state.schedules[state.activeScheduleTab].filter((x) => x.id !== idNum);
        persist();
    }

    function renderSchedule() {
        el.scheduleTabs.forEach((b) => {
            const active = b.dataset.scheduleTab === state.activeScheduleTab;
            b.classList.toggle("bg-white", active);
            b.classList.toggle("shadow-sm", active);
            b.classList.toggle("text-on-surface-variant", !active);
        });
        const rows = state.schedules[state.activeScheduleTab] || [];
        if (!rows.length) {
            el.scheduleTableBody.innerHTML = `<tr><td colspan="3" class="px-4 py-4 text-sm text-on-surface-variant">Belum ada jadwal. Tambahkan dari form di atas.</td></tr>`;
            return;
        }
        el.scheduleTableBody.innerHTML = rows.map((r) => `
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-semibold">${esc(r.time)}</td>
                <td class="px-4 py-3 break-words">${esc(r.activity)}</td>
                <td class="px-4 py-3"><button data-remove-schedule="${r.id}" class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Hapus</button></td>
            </tr>
        `).join("");
    }

    function onCategorySave() {
        const name = el.categoryNameInput.value.trim();
        if (!name) return;
        if (state.categories.includes(name)) { toast("Kategori sudah ada."); return; }
        state.categories.push(name);
        el.categoryNameInput.value = "";
        el.categoryFormWrap.classList.add("hidden");
        persist();
        toast("Kategori baru ditambahkan.");
    }

    function onCategoryRemove(e) {
        const b = e.target.closest("[data-remove-category]");
        if (!b) return;
        const name = b.getAttribute("data-remove-category");
        if (!name) return;
        if (["Akademik", "Non Akademik", "Personal"].includes(name)) { toast("Kategori default tidak bisa dihapus."); return; }
        state.categories = state.categories.filter((c) => c !== name);
        state.tasks = state.tasks.map((t) => t.category === name ? ({ ...t, category: "Personal" }) : t);
        persist();
    }

    function exportData() {
        const blob = new Blob([JSON.stringify({ exportedAt: new Date().toISOString(), app: "TugasKu", data: state }, null, 2)], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "tugasku-export.json";
        a.click();
        URL.revokeObjectURL(url);
    }

    function filteredBySidebar() {
        return state.tasks.filter((t) => {
            if (state.activeFilter === "all") return true;
            if (state.activeFilter === "today") return t.dueDate === todayISO;
            if (state.activeFilter === "urgent") return t.urgent;
            if (state.activeFilter === "progress") return t.status === "progress";
            if (state.activeFilter === "overdue") return isOverdue(t);
            if (state.activeFilter === "selesai") return t.status === "selesai";
            return true;
        });
    }

    function filteredBySearchStatus() {
        return filteredBySidebar().filter((t) => {
            const okText = !state.searchQuery || `${t.title} ${t.category}`.toLowerCase().includes(state.searchQuery);
            const okStatus = state.taskStatusFilter === "all" || t.status === state.taskStatusFilter;
            return okText && okStatus;
        });
    }

    function monthNoteCount() {
        const y = new Date().getFullYear();
        return Object.keys(state.notesByDate).filter((iso) => {
            const d = parseISODate(iso);
            return d.getFullYear() === y && d.getMonth() === state.activeMonth;
        }).length;
    }

    function fitDateToMonth(iso) {
        const y = new Date().getFullYear();
        const d = iso ? parseISODate(iso) : new Date(y, state.activeMonth, 1);
        if (d.getMonth() === state.activeMonth) return toISO(d);
        return toISO(new Date(y, state.activeMonth, 1));
    }

    function isOverdue(task) {
        if (task.status === "selesai") return false;
        const due = parseISODate(task.dueDate); due.setHours(0, 0, 0, 0);
        const now = new Date(); now.setHours(0, 0, 0, 0);
        return due < now;
    }

    function persist() { save(); renderAll(); }
    function save() { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); }
    function load() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return structuredClone(defaults);
            const parsed = JSON.parse(raw);
            return {
                ...structuredClone(defaults),
                ...parsed,
                schedules: {
                    weekday: parsed?.schedules?.weekday || structuredClone(defaults.schedules.weekday),
                    weekend: parsed?.schedules?.weekend || structuredClone(defaults.schedules.weekend)
                },
                notesByDate: parsed?.notesByDate || {}
            };
        } catch (_) {
            return structuredClone(defaults);
        }
    }

    function fmt(iso) { return new Intl.DateTimeFormat("id-ID", { day: "2-digit", month: "short", year: "numeric" }).format(parseISODate(iso)); }
    function fmtLong(iso) { return new Intl.DateTimeFormat("id-ID", { weekday: "long", day: "numeric", month: "long", year: "numeric" }).format(parseISODate(iso)); }
    function toISO(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, "0");
        const day = String(d.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }
    function parseISODate(iso) {
        const parts = String(iso || "").split("-").map(Number);
        if (parts.length !== 3 || parts.some((x) => Number.isNaN(x))) return new Date();
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }
    function addDays(d, n) { const x = new Date(d); x.setDate(x.getDate() + n); return x; }
    function uid() { return Date.now() + Math.floor(Math.random() * 100000); }
    function id(name) { return document.getElementById(name); }
    function qsa(sel) { return document.querySelectorAll(sel); }
    function esc(v) { return String(v).replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;").replaceAll("'", "&#039;"); }
    function toast(msg) {
        el.toast.textContent = msg;
        el.toast.classList.remove("hidden");
        clearTimeout(toast.timer);
        toast.timer = setTimeout(() => el.toast.classList.add("hidden"), 1800);
    }
})();
