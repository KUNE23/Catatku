<template>
    <AppLayout>
        <div>
            <Head title="Calendar -" />

            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6 text-center">
                    Event Calendar
                </h1>

                <FullCalendar :options="calendarOptions" />

                <div
                    v-if="showModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white p-6 rounded-lg w-96">
                        <h2 class="text-xl font-bold mb-4">
                            {{ editingEvent ? "Edit Event" : "Add Event" }}
                        </h2>

                        <form @submit.prevent="saveEvent">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2"
                                    >Title</label
                                >
                                <input
                                    v-model="eventForm.title"
                                    type="text"
                                    class="w-full border rounded px-3 py-2"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2"
                                    >Start Date</label
                                >
                                <input
                                    v-model="eventForm.start"
                                    type="datetime-local"
                                    class="w-full border rounded px-3 py-2"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2"
                                    >End Date (Optional)</label
                                >
                                <input
                                    v-model="eventForm.end"
                                    type="datetime-local"
                                    class="w-full border rounded px-3 py-2"
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2"
                                    >Description</label
                                >
                                <textarea
                                    v-model="eventForm.description"
                                    class="w-full border rounded px-3 py-2"
                                    rows="3"
                                ></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2"
                                    >Color</label
                                >
                                <input
                                    v-model="eventForm.color"
                                    type="color"
                                    class="w-full border rounded px-3 py-2"
                                />
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-100"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                >
                                    Save
                                </button>
                                <button
                                    v-if="editingEvent"
                                    type="button"
                                    @click="deleteEvent(editingEvent.id)"
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                >
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import idLocale from "@fullcalendar/core/locales/id";
import { useToast } from "vue-toastification";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    events: Array,
});

const toast = useToast();

const page = usePage();

const showModal = ref(false);
const editingEvent = ref(null);
const events = ref(props.events);

const eventForm = reactive({
    title: "",
    start: "",
    end: "",
    description: "",
    color: "#3788d8",
});

watch(
    () => props.events,
    (newEvents) => {
        events.value = newEvents;
    },
    { deep: true }
);

const formatDateForInput = (date) => {
    if (!date) return "";
    return new Date(date).toISOString().slice(0, 16);
};

const resetForm = () => {
    eventForm.title = "";
    eventForm.start = "";
    eventForm.end = "";
    eventForm.description = "";
    eventForm.color = "#3788d8";
};

const openModal = (startDate, endDate = null) => {
    resetForm();
    eventForm.start = startDate;
    if (endDate) eventForm.end = endDate;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingEvent.value = null;
    resetForm();
};

const createEvent = (data) => {
    router.post("/events", data, {
        onSuccess: (page) => {
            const newEvent = {
                id: Date.now(),
                title: data.title,
                start: data.start,
                end: data.end || null,
                color: data.color || "#3788d8",
                description: data.description || "",
            };

            router.reload({
                only: ["events"],
                onSuccess: (page) => {
                    events.value = page.props.events;
                },
            });

            closeModal();
            toast.success("Jadwal berhasil diTambahkan!");
        },
        onError: (errors) => {
            console.error("Error creating event:", errors);
        },
    });
};

const updateEvent = (id, data) => {
    const oldEventIndex = events.value.findIndex((e) => e.id == id);
    const oldEvent =
        oldEventIndex !== -1 ? { ...events.value[oldEventIndex] } : null;

    if (oldEventIndex !== -1) {
        events.value[oldEventIndex] = {
            ...events.value[oldEventIndex],
            title: data.title,
            start: data.start,
            end: data.end || null,
            color: data.color || "#3788d8",
            description: data.description || "",
        };
    }

    router.put(`/events/${id}`, data, {
        onSuccess: (page) => {
            router.reload({
                only: ["events"],
                onSuccess: (page) => {
                    events.value = page.props.events;
                },
            });
            closeModal();
            toast.success("Jadwal berhasil diperbarui!");
        },
        onError: (errors) => {
            if (oldEvent && oldEventIndex !== -1) {
                events.value[oldEventIndex] = oldEvent;
            }
            console.error("Error updating event:", errors);
            toast.error("error", "Terjadi kesalahan saat mengupdate event");
        },
    });
};

const deleteEvent = (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus event ini?")) {
        const eventToDelete = events.value.find((e) => e.id == id);

        events.value = events.value.filter((e) => e.id != id);

        router.delete(`/events/${id}`, {
            onSuccess: () => {
                closeModal();
                toast.success("Jadwal berhasil diHapus!");
            },
            onError: (errors) => {
                if (eventToDelete) {
                    events.value.push(eventToDelete);
                }
                console.error("Error deleting event:", errors);
                toast.error("Terjadi kesalahan saat menghapus event");
            },
        });
    }
};

const saveEvent = () => {
    if (editingEvent.value) {
        updateEvent(editingEvent.value.id, eventForm);
    } else {
        createEvent(eventForm);
    }
};

const handleDateClick = (arg) => {
    openModal(arg.dateStr);
};

const handleDateSelect = (arg) => {
    openModal(arg.startStr, arg.endStr);
};

const handleEventClick = (arg) => {
    editingEvent.value = arg.event;
    eventForm.title = arg.event.title;
    eventForm.start = formatDateForInput(arg.event.start);
    eventForm.end = arg.event.end ? formatDateForInput(arg.event.end) : "";
    eventForm.description = arg.event.extendedProps.description || "";
    eventForm.color = arg.event.color || "#3788d8";
    showModal.value = true;
};

const handleEventDrop = (arg) => {
    const eventIndex = events.value.findIndex((e) => e.id == arg.event.id);
    if (eventIndex !== -1) {
        events.value[eventIndex] = {
            ...events.value[eventIndex],
            start: arg.event.start.toISOString(),
            end: arg.event.end ? arg.event.end.toISOString() : null,
        };
    }

    const eventData = {
        title: arg.event.title,
        start: arg.event.start.toISOString(),
        end: arg.event.end ? arg.event.end.toISOString() : null,
        description: arg.event.extendedProps.description || "",
        color: arg.event.color || "#3788d8",
    };

    router.put(`/events/${arg.event.id}`, eventData, {
        onError: (errors) => {
            console.error("Error updating event:", errors);
            toast.error("error", "Terjadi kesalahan saat mengupdate event");

            arg.revert();
        },
    });
};

const handleEventResize = (arg) => {
    const eventIndex = events.value.findIndex((e) => e.id == arg.event.id);
    if (eventIndex !== -1) {
        events.value[eventIndex] = {
            ...events.value[eventIndex],
            start: arg.event.start.toISOString(),
            end: arg.event.end ? arg.event.end.toISOString() : null,
        };
    }

    const eventData = {
        title: arg.event.title,
        start: arg.event.start.toISOString(),
        end: arg.event.end ? arg.event.end.toISOString() : null,
        description: arg.event.extendedProps.description || "",
        color: arg.event.color || "#3788d8",
    };

    router.put(`/events/${arg.event.id}`, eventData, {
        onError: (errors) => {
            console.error("Error updating event:", errors);
            toast.error("error", "Terjadi kesalahan saat mengupdate event");

            arg.revert();
        },
    });
};

const calendarOptions = reactive({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    initialView: "dayGridMonth",
    locale: idLocale,
    events: computed(() => events.value),
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    dateClick: handleDateClick,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
    select: handleDateSelect,
});
</script>

<style scoped>
.fc {
    font-family: "Arial", sans-serif;
}

.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
}

.fc-daygrid-event {
    font-size: 13px;
}
</style>
