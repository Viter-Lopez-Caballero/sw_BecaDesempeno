<script setup>
import { ref, computed, watch } from 'vue';
import { CheckCircle, Clock, ClipboardCheck, Award } from 'lucide-vue-next';

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
        iconName:   'CheckCircle'
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
        iconName:   'Clock'
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
        iconName:   'ClipboardCheck'
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
        iconName:   'Award'
    }
];

const getIconComponent = (name) => {
    const icons = { CheckCircle, Clock, ClipboardCheck, Award };
    return icons[name];
};

// ─── State ───────────────────────────────────────────────────────────────────
const activePhase    = ref(null);   // key of the phase being configured
const dragStart      = ref(null);   // Date object
const dragEnd        = ref(null);   // Date object
const isDragging     = ref(false);
const hoverDay       = ref(null);
const errMessage     = ref('');

// Local dates - sync from props
const dates = ref({ ...props.modelValue });

watch(() => props.modelValue, (v) => { dates.value = { ...v }; }, { deep: true });

// ─── Calendar navigation ─────────────────────────────────────────────────────
const today = new Date();
const viewYear  = ref(today.getFullYear());
const viewMonth = ref(today.getMonth()); // Base month for the multi-view

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

// Returns year/month for the Nth offset month from viewYear/viewMonth
const getOffsetMonth = (offset) => {
    let m = viewMonth.value + offset;
    let y = viewYear.value;
    while (m > 11) { m -= 12; y++; }
    while (m < 0) { m += 12; y--; }
    return { year: y, month: m };
};

// Build grid of days for a specific year/month
const getDaysInMonth = (y, m) => {
    const firstDay = new Date(y, m, 1).getDay();
    const daysInMonthCount = new Date(y, m + 1, 0).getDate();
    const days = [];
    for (let i = 0; i < firstDay; i++) days.push(null);
    for (let d = 1; d <= daysInMonthCount; d++) {
        days.push(new Date(y, m, d));
    }
    return days;
};

const visibleMonths = computed(() => {
    return [0, 1, 2, 3].map(offset => {
        const { year, month } = getOffsetMonth(offset);
        return {
            year,
            month,
            label: `${MONTHS_ES[month]} ${year}`,
            days: getDaysInMonth(year, month)
        };
    });
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

// ─── Validation Logic ────────────────────────────────────────────────────────
const validateDates = (newDates) => {
    errMessage.value = '';
    
    // Order of sequences to check
    const order = [
        { name: 'Publicación', start: 'publication_start', end: 'publication_start' },
        { name: 'Registro', start: 'registration_start', end: 'registration_end' },
        { name: 'Evaluación', start: 'evaluation_start', end: 'evaluation_end' },
        { name: 'Resultados', start: 'results_start', end: 'results_end' }
    ];

    for (let i = 0; i < order.length; i++) {
        const current = order[i];
        const curStart = newDates[current.start];
        const curEnd = newDates[current.end];

        if (curStart && curEnd && curStart > curEnd) {
            errMessage.value = `Error en ${current.name}: La fecha de inicio no puede ser posterior a la de fin.`;
            return false;
        }

        if (i > 0) {
            const prev = order[i - 1];
            const prevEnd = newDates[prev.end];
            if (prevEnd && curStart && curStart <= prevEnd) {
                errMessage.value = `Advertencia: La etapa de ${current.name} debe iniciar después de que termine la etapa de ${prev.name}.`;
                return false;
            }
        }
    }
    return true;
};

// ─── Interaction ─────────────────────────────────────────────────────────────
const selectPhase = (phaseKey) => {
    activePhase.value = activePhase.value === phaseKey ? null : phaseKey;
    dragStart.value = null;
    dragEnd.value = null;
    isDragging.value = false;
    errMessage.value = '';
};

const onMouseEnter = (day) => {
    if (!day) return;
    hoverDay.value = day;
};

const onDayClick = (day) => {
    if (!day || !activePhase.value) return;
    
    const phase = phases.find(p => p.key === activePhase.value);
    if (!phase) return;

    // If it's a single day phase (Publicación), finalize immediately
    if (!phase.endKey) {
        applyPhaseDates(phase, day, day);
        dragStart.value = null;
        return;
    }

    if (!dragStart.value) {
        // First click: set start
        dragStart.value = day;
    } else {
        // Second click: finalize range
        const lo = dragStart.value <= day ? dragStart.value : day;
        const hi = dragStart.value <= day ? day : dragStart.value;

        if (applyPhaseDates(phase, lo, hi)) {
            dragStart.value = null;
        } else {
            // If validation fails, stay on "waiting for second date" but maybe they clicked a bad date
            // Let's keep the start date they chose so they can try another end date
        }
    }
};

const applyPhaseDates = (phase, start, end) => {
    const updated = { ...dates.value };
    updated[phase.startKey] = toDateStr(start);
    if (phase.endKey) updated[phase.endKey] = toDateStr(end);
    
    if (validateDates(updated)) {
        dates.value = updated;
        emit('update:modelValue', updated);
        errMessage.value = '';
        return true;
    }
    return false;
};

const clearPhase = (phase) => {
    const updated = { ...dates.value };
    updated[phase.startKey] = '';
    if (phase.endKey) updated[phase.endKey] = '';
    dates.value = updated;
    emit('update:modelValue', updated);
    errMessage.value = '';
    dragStart.value = null;
};

// ─── Day style ───────────────────────────────────────────────────────────────
const dayStyle = (day) => {
    if (!day) return {};
    // Drag/Multi-click preview takes priority
    if (activePhase.value && (isDragging.value || dragStart.value) && isInDragPreview(day)) {
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
    <div class="space-y-6 select-none">

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
                    <component 
                        :is="getIconComponent(phase.iconName)"
                        class="w-6 h-6 block"
                        :style="{ color: activePhase === phase.key ? '#fff' : phase.color }"
                    />
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
                            class="flex-shrink-0 text-gray-400 hover:text-red-500 transition cursor-pointer"
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

        <!-- Global Navigation -->
        <div class="flex items-center justify-center gap-8 bg-white py-3 px-6 rounded-xl border border-gray-200 shadow-sm">
            <button type="button" @click="prevMonth"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-[#1B396A] transition font-bold border border-gray-100 hover:border-[#1B396A] cursor-pointer">
                <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </button>
            <div class="h-6 w-px bg-gray-200"></div>
            <button type="button" @click="nextMonth"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-[#1B396A] transition font-bold border border-gray-100 hover:border-[#1B396A] cursor-pointer">
                Siguiente
                <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Error / Warning message (Permanent) -->
        <div v-if="errMessage" 
            class="px-4 py-3 rounded-lg text-sm flex items-center gap-2 font-medium shadow-sm border border-red-200 bg-red-100 text-red-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ errMessage }}</span>
            <button @click="errMessage = ''" class="ml-auto text-red-400 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Instruction banner (Permanent while active) -->
        <div
            v-if="activePhase && !dragStart"
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
                    ? 'Selecciona la fecha inicial y luego la final.'
                    : 'Selecciona el día de publicación.' }}
            </span>
            <button type="button" @click="activePhase = null" class="ml-auto text-current hover:opacity-60 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Multi-Month Calendar Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div v-for="mInfo in visibleMonths" :key="`${mInfo.year}-${mInfo.month}`" 
                class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                
                <!-- Month header -->
                <div class="flex items-center justify-center px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-800">{{ mInfo.label }}</h3>
                </div>

                <!-- Day headers -->
                <div class="grid grid-cols-7 border-b border-gray-100 bg-white">
                    <div v-for="d in DAYS_ES" :key="d"
                        class="py-2 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest"
                    >{{ d }}</div>
                </div>

                <!-- Day grid -->
                <div class="grid grid-cols-7 gap-1 p-3 bg-white flex-1" :class="activePhase ? 'cursor-crosshair' : 'cursor-default'">
                    <div
                        v-for="(day, idx) in mInfo.days"
                        :key="idx"
                        class="relative flex flex-col items-center justify-start pt-1 pb-1 min-h-[56px] rounded-lg transition-all duration-100 group"
                        :class="[
                            !day ? 'invisible' : (activePhase ? 'hover:ring-2 hover:ring-offset-1' : ''),
                            day && sameDay(day, new Date()) && !phaseForDay(day) ? 'ring-1 ring-gray-400' : ''
                        ]"
                        :style="[
                            dayStyle(day),
                            activePhase && day ? { '--tw-ring-color': phases.find(p => p.key === activePhase)?.color } : {}
                        ]"
                        @mouseenter="onMouseEnter(day)"
                        @click="onDayClick(day)"
                    >
                        <!-- Day number -->
                        <span v-if="day" class="text-sm font-bold leading-none"
                            :class="phaseForDay(day) || isInDragPreview(day) ? '' : 'text-gray-700'">
                            {{ day.getDate() }}
                        </span>

                        <!-- Phase label badge -->
                        <span v-if="day && dayLabel(day)" class="mt-1 text-[8px] font-extrabold leading-none tracking-tighter opacity-90">
                            {{ dayLabel(day) }}
                        </span>

                        <!-- Selection helper (dot on hover) -->
                        <div v-if="activePhase && day && !phaseForDay(day) && !isInDragPreview(day)" 
                             class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-2.5 h-2.5 rounded-full shadow-sm animate-bounce" 
                                 :style="{ background: phases.find(p => p.key === activePhase)?.color }"></div>
                        </div>

                        <!-- Active Start Date indicator -->
                        <div v-if="dragStart && sameDay(day, dragStart)" 
                             class="absolute -bottom-1 left-1/2 -translate-x-1/2">
                            <div class="w-1.5 h-1.5 rounded-full bg-white ring-3 ring-black/40"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center justify-center gap-6 px-6 py-4 border border-gray-200 bg-gray-50 rounded-xl shadow-inner">
            <div v-for="phase in phases" :key="phase.key" class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-md shadow-sm" :style="{ background: phase.color }"></div>
                <span class="text-xs font-semibold text-gray-700">{{ phase.label }}</span>
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
