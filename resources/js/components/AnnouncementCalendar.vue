<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
        // Expected shape:
        // { publication_start, registration_start, registration_end,
        //   evaluation_start, evaluation_end, results_start, results_end }
    }
});

const emit = defineEmits(['update:modelValue']);

// ─── Phase configuration ────────────────────────────────────────────────────
const phases = [
    {
        key:        'publicacion',
        label:      'Publicación',
        number:     '01',
        desc:       'Publicación de la Convocatoria.',
        color:      '#1B396A',
        colorLight: '#dce6f5',
        startKey:   'publication_start',
        endKey:     null,           // single day
        icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>`
    },
    {
        key:        'registro',
        label:      'Registro',
        number:     '02',
        desc:       'Periodo para Inscribirse.',
        color:      '#10A558',
        colorLight: '#d0f2e3',
        startKey:   'registration_start',
        endKey:     'registration_end',
        icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>`
    },
    {
        key:        'evaluacion',
        label:      'Evaluación',
        number:     '03',
        desc:       'Periodo de Revisión.',
        color:      '#C9A800',
        colorLight: '#fdf6d0',
        startKey:   'evaluation_start',
        endKey:     'evaluation_end',
        icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
               </svg>`
    },
    {
        key:        'resultados',
        label:      'Resultados',
        number:     '04',
        desc:       'Publicación de Resultados.',
        color:      '#2B6CB0',
        colorLight: '#dbeafe',
        startKey:   'results_start',
        endKey:     'results_end',
        icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                   d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
               </svg>`
    }
];

// ─── State ───────────────────────────────────────────────────────────────────
const activePhase    = ref(null);   // key of the phase being configured
const dragStart      = ref(null);   // Date object
const dragEnd        = ref(null);   // Date object
const isDragging     = ref(false);
const hoverDay       = ref(null);

// Local dates - sync from props
const dates = ref({ ...props.modelValue });

watch(() => props.modelValue, (v) => { dates.value = { ...v }; }, { deep: true });

// ─── Calendar navigation ─────────────────────────────────────────────────────
const today = new Date();
const viewYear  = ref(today.getFullYear());
const viewMonth = ref(today.getMonth()); // 0-indexed

const MONTHS_ES = [
    'Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];
const DAYS_ES = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

const prevMonth = () => {
    if (viewMonth.value === 0) { viewMonth.value = 11; viewYear.value--; }
    else viewMonth.value--;
};
const nextMonth = () => {
    if (viewMonth.value === 11) { viewMonth.value = 0; viewYear.value++; }
    else viewMonth.value++;
};

// Build grid of days for the current month view (padded to start on Sunday)
const calendarDays = computed(() => {
    const firstDay = new Date(viewYear.value, viewMonth.value, 1).getDay();
    const daysInMonth = new Date(viewYear.value, viewMonth.value + 1, 0).getDate();
    const days = [];
    for (let i = 0; i < firstDay; i++) days.push(null);
    for (let d = 1; d <= daysInMonth; d++) {
        days.push(new Date(viewYear.value, viewMonth.value, d));
    }
    return days;
});

// ─── Helpers ─────────────────────────────────────────────────────────────────
const toDateStr = (d) => {
    if (!d) return '';
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const parseDate = (s) => {
    if (!s) return null;
    const [y, m, d] = s.split('-').map(Number);
    return new Date(y, m - 1, d);
};

const sameDay = (a, b) => a && b && toDateStr(a) === toDateStr(b);

// Which phase (if any) covers this date?
const phaseForDay = (day) => {
    if (!day) return null;
    const ds = toDateStr(day);
    for (const ph of phases) {
        const s = dates.value[ph.startKey];
        const e = ph.endKey ? dates.value[ph.endKey] : s;
        if (s && ds >= s && ds <= (e || s)) return ph;
    }
    return null;
};

// During active drag, highlight preview range
const isInDragPreview = (day) => {
    if (!day || !activePhase.value || !dragStart.value) return false;
    const end = isDragging.value ? dragEnd.value : hoverDay.value;
    if (!end) return sameDay(day, dragStart.value);
    const lo = dragStart.value <= end ? dragStart.value : end;
    const hi = dragStart.value <= end ? end : dragStart.value;
    return day >= lo && day <= hi;
};

const formatRange = (phase) => {
    const s = dates.value[phase.startKey];
    if (!s) return 'Sin configurar';
    const start = parseDate(s);
    const startStr = start.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
    if (!phase.endKey) return startStr;
    const e = dates.value[phase.endKey];
    if (!e) return startStr;
    const end = parseDate(e);
    return `${startStr} – ${end.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })}`;
};

const phaseConfigured = (phase) => !!dates.value[phase.startKey];

// ─── Interaction ─────────────────────────────────────────────────────────────
const selectPhase = (phaseKey) => {
    activePhase.value = activePhase.value === phaseKey ? null : phaseKey;
    dragStart.value = null;
    dragEnd.value = null;
    isDragging.value = false;
};

const onMouseDown = (day) => {
    if (!day || !activePhase.value) return;
    isDragging.value = true;
    dragStart.value = day;
    dragEnd.value = day;
};

const onMouseEnter = (day) => {
    if (!day) return;
    hoverDay.value = day;
    if (isDragging.value) dragEnd.value = day;
};

const onMouseUp = (day) => {
    if (!isDragging.value || !dragStart.value || !day || !activePhase.value) {
        isDragging.value = false;
        return;
    }
    isDragging.value = false;

    const phase = phases.find(p => p.key === activePhase.value);
    if (!phase) return;

    const lo = dragStart.value <= day ? dragStart.value : day;
    const hi = dragStart.value <= day ? day : dragStart.value;

    const updated = { ...dates.value };
    updated[phase.startKey] = toDateStr(lo);
    if (phase.endKey) updated[phase.endKey] = toDateStr(hi);

    dates.value = updated;
    emit('update:modelValue', updated);

    dragStart.value = null;
    dragEnd.value = null;
};

// Click without drag (single day click)
const onDayClick = (day) => {
    if (!day || !activePhase.value || isDragging.value) return;
    const phase = phases.find(p => p.key === activePhase.value);
    if (!phase) return;
    const updated = { ...dates.value };
    updated[phase.startKey] = toDateStr(day);
    if (phase.endKey) updated[phase.endKey] = toDateStr(day);
    dates.value = updated;
    emit('update:modelValue', updated);
};

const clearPhase = (phase) => {
    const updated = { ...dates.value };
    updated[phase.startKey] = '';
    if (phase.endKey) updated[phase.endKey] = '';
    dates.value = updated;
    emit('update:modelValue', updated);
};

// ─── Day style ───────────────────────────────────────────────────────────────
const dayStyle = (day) => {
    if (!day) return {};
    // Drag preview takes priority
    if (activePhase.value && isInDragPreview(day)) {
        const ph = phases.find(p => p.key === activePhase.value);
        return { background: ph.colorLight, color: ph.color, borderRadius: '6px', fontWeight: '700' };
    }
    const ph = phaseForDay(day);
    if (ph) {
        return { background: ph.color, color: '#fff', borderRadius: '6px', fontWeight: '700' };
    }
    return {};
};

const dayLabel = (day) => {
    if (!day) return '';
    const ph = phaseForDay(day);
    if (!ph) return '';
    if (ph.endKey) {
        const s = dates.value[ph.startKey];
        const e = dates.value[ph.endKey];
        if (s === toDateStr(day)) return ph.label.slice(0, 3).toUpperCase();
        if (e === toDateStr(day)) return 'FIN';
    } else {
        return ph.label.slice(0, 3).toUpperCase();
    }
    return '';
};
</script>

<template>
    <div class="space-y-6 select-none" @mouseup.prevent="onMouseUp(hoverDay)">

        <!-- Phase selector cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button
                v-for="phase in phases"
                :key="phase.key"
                type="button"
                @click="selectPhase(phase.key)"
                class="relative flex flex-col items-start gap-2 p-4 rounded-xl border-2 text-left transition-all duration-200 focus:outline-none"
                :class="[
                    activePhase === phase.key
                        ? 'shadow-lg scale-105'
                        : 'hover:shadow-md hover:scale-[1.02] bg-white border-gray-200'
                ]"
                :style="activePhase === phase.key
                    ? { borderColor: phase.color, background: phase.colorLight }
                    : {}"
            >
                <!-- Number badge -->
                <span
                    class="absolute top-3 right-3 text-xs font-bold px-2 py-0.5 rounded-full"
                    :style="{ background: phase.color, color: '#fff' }"
                >{{ phase.number }}</span>

                <!-- Icon -->
                <div class="p-2 rounded-lg" :style="{ background: activePhase === phase.key ? phase.color : '#F3F4F6' }">
                    <span
                        v-html="phase.icon"
                        class="block"
                        :style="{ color: activePhase === phase.key ? '#fff' : phase.color }"
                    ></span>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-900">{{ phase.label }}</p>
                    <p class="text-xs text-gray-500 leading-tight">{{ phase.desc }}</p>
                </div>

                <!-- Date display -->
                <div class="w-full mt-1">
                    <div
                        v-if="phaseConfigured(phase)"
                        class="flex items-center justify-between gap-1"
                    >
                        <p class="text-xs font-semibold truncate" :style="{ color: phase.color }">
                            {{ formatRange(phase) }}
                        </p>
                        <button
                            type="button"
                            @click.stop="clearPhase(phase)"
                            class="flex-shrink-0 text-gray-400 hover:text-red-500 transition"
                            title="Limpiar"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <p v-else class="text-xs text-gray-400 italic">Sin configurar</p>
                </div>

                <!-- Active indicator ring -->
                <div
                    v-if="activePhase === phase.key"
                    class="absolute inset-0 rounded-xl pointer-events-none"
                    :style="{ boxShadow: `0 0 0 3px ${phase.color}` }"
                ></div>
            </button>
        </div>

        <!-- Instruction banner -->
        <div
            v-if="activePhase"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium"
            :style="{ background: phases.find(p => p.key === activePhase)?.colorLight, color: phases.find(p => p.key === activePhase)?.color }"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                Configurando: <strong>{{ phases.find(p => p.key === activePhase)?.label }}</strong> —
                {{ phases.find(p => p.key === activePhase)?.endKey
                    ? 'Arrastra para seleccionar un rango o haz clic en un día.'
                    : 'Haz clic en el día de publicación.' }}
            </span>
            <button type="button" @click="activePhase = null" class="ml-auto text-current hover:opacity-60 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Calendar -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Month navigation -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <button
                    type="button"
                    @click="prevMonth"
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <h3 class="text-lg font-bold text-gray-900">
                    {{ MONTHS_ES[viewMonth] }} {{ viewYear }}
                </h3>
                <button
                    type="button"
                    @click="nextMonth"
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Day headers -->
            <div class="grid grid-cols-7 border-b border-gray-100">
                <div
                    v-for="d in DAYS_ES"
                    :key="d"
                    class="py-2 text-center text-xs font-semibold text-gray-500 uppercase"
                >{{ d }}</div>
            </div>

            <!-- Day grid -->
            <div
                class="grid grid-cols-7 gap-1 p-3"
                :class="activePhase ? 'cursor-crosshair' : 'cursor-default'"
            >
                <div
                    v-for="(day, idx) in calendarDays"
                    :key="idx"
                    class="relative flex flex-col items-center justify-start pt-1 pb-1 min-h-[56px] rounded-lg transition-all duration-100"
                    :class="[
                        !day ? '' : (activePhase ? 'hover:ring-2 hover:ring-offset-1' : ''),
                        day && sameDay(day, new Date()) && !phaseForDay(day) ? 'ring-1 ring-gray-400' : ''
                    ]"
                    :style="[
                        dayStyle(day),
                        activePhase && day ? { '--tw-ring-color': phases.find(p => p.key === activePhase)?.color } : {}
                    ]"
                    @mousedown.prevent="onMouseDown(day)"
                    @mouseenter="onMouseEnter(day)"
                    @click="onDayClick(day)"
                >
                    <!-- Day number -->
                    <span
                        v-if="day"
                        class="text-sm font-semibold leading-none"
                        :class="phaseForDay(day) || isInDragPreview(day) ? '' : 'text-gray-700'"
                    >
                        {{ day.getDate() }}
                    </span>

                    <!-- Phase label badge -->
                    <span
                        v-if="day && dayLabel(day)"
                        class="mt-1 text-[9px] font-bold leading-none tracking-wider opacity-90"
                    >
                        {{ dayLabel(day) }}
                    </span>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap items-center justify-center gap-4 px-6 py-3 border-t border-gray-100 bg-gray-50">
                <div v-for="phase in phases" :key="phase.key" class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded" :style="{ background: phase.color }"></div>
                    <span class="text-xs text-gray-600">{{ phase.label }}</span>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for form compatibility -->
        <div class="hidden">
            <input v-for="phase in phases" :key="phase.startKey"
                :name="phase.startKey" :value="dates[phase.startKey]" type="text" readonly>
            <input v-for="phase in phases.filter(p => p.endKey)" :key="phase.endKey"
                :name="phase.endKey" :value="dates[phase.endKey]" type="text" readonly>
        </div>
    </div>
</template>

<style scoped>
.select-none {
    user-select: none;
    -webkit-user-select: none;
}
</style>
