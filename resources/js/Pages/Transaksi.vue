<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Vue3EasyDataTable from "vue3-easy-data-table";
import "vue3-easy-data-table/dist/style.css";
import { useToast } from "vue-toastification";

const props = defineProps({
    transaksis: {
        type: Array,
        default: () => [],
    },
});

const toast = useToast();

const headers = [
    { text: "Nama", value: "name" },
    { text: "Tanggal", value: "tanggal" },
    { text: "Jenis", value: "jenis" },
    { text: "Nominal", value: "nominal" },
    { text: "Deskripsi", value: "deskripsi" },
    { text: "Aksi", value: "actions" },
];

const search = ref("");
const open = ref(false);
const selectedTransaksi = ref(null);

const isEdit = computed(() => !!selectedTransaksi.value);

const form = useForm({
    id: null,
    name: "",
    tanggal: "",
    jenis: "",
    nominal: "",
    deskripsi: "",
});

watch(
    selectedTransaksi,
    (newTransaksi) => {
        if (newTransaksi) {
            form.id = newTransaksi.id;
            form.name = newTransaksi.name;
            form.tanggal = newTransaksi.tanggal;
            form.jenis = newTransaksi.jenis;
            form.nominal = newTransaksi.nominal;
            form.deskripsi = newTransaksi.deskripsi;
        } else {
            form.reset();
            form.id = null;
        }
    },
    { immediate: true }
);

const openAddModal = () => {
    open.value = true;
};

const openEditModal = (transaksi) => {
    selectedTransaksi.value = transaksi;
    open.value = true;
};

const closemodalAndReset = () => {
    open.value = false;
    selectedTransaksi.value = null;
    form.reset();
    form.clearErrors();
    form.id = null;
};

const submit = () => {
    if (isEdit.value) {
        form.put(`/transaksi/edit/${form.id}`, {
            onSuccess: () => {
                closemodalAndReset();

                router.reload({
                    only: ["transaksis"],
                });
                toast.success("Transaksi berhasil diperbarui!");
            },
            onError: (errors) => {
                console.error("Error updating transaksi:", errors);
            },
        });
    } else {
        form.post("/transaksi", {
            onSuccess: () => {
                closemodalAndReset();

                router.reload({
                    only: ["transaksis"],
                });
                toast.success("Transaksi berhasil ditambahkan!");
            },
            onError: (errors) => {
                console.error("Error adding transaksi:", errors);
            },
        });
    }
};

const deleteTransaksi = (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus transaksi ini?")) {
        router.delete(route("transaksi.destroy", id), {
            onSuccess: () => {
                router.reload({
                    only: ["transaksis"],
                });
                toast.success("Transaksi berhasil dihapus!");
            },
            onError: (errors) => {
                console.error("Error deleting transaksi:", errors);
            },
        });
    }
};
</script>

<template>
    <AppLayout title="Transaksi">
        <template #header>
            <h2
                class="font-semibold text-4xl text-gray-800 leading-tight text-center w-full"
            >
                Halaman Data Transaksi
            </h2>
            <div class="pb-3">
                <hr />
                <hr />
                <hr />
                <hr />
                <hr />
            </div>
        </template>
        <div
            class="overflow-x-auto rounded border border-gray-300 shadow-sm py-3 px-3"
        >
            <div class="flex justify-between">
                <input
                    v-model="search"
                    placeholder="Cari Data..."
                    class="mb-2 border px-2 py-1 rounded"
                />
                <button
                    type="trigger"
                    class="inline-block rounded-sm border border-current px-8 py-3 text-sm font-medium text-indigo-600 transition hover:scale-110 hover:shadow-xl focus:ring-3 focus:outline-hidden"
                    @click="openAddModal()"
                >
                    Tambah Data
                </button>
            </div>
            <Vue3EasyDataTable
                :headers="headers"
                :items="props.transaksis"
                :search-value="search"
                :pagination="true"
                :items-per-page="10"
                show-index
                index-column="No"
                table-class-name="table-auto w-full"
            >
                <template #item-jenis="transaksi">
                    <p
                        v-if="transaksi.jenis == 'Pemasukan'"
                        class="text-green-600"
                    >
                        {{ transaksi.jenis }}
                    </p>
                    <p v-else class="text-red-600">{{ transaksi.jenis }}</p>
                </template>
                <template #item-nominal="transaksi">
                    {{
                        Number(transaksi.nominal).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2,
                        })
                    }}
                </template>

                <template #item-tanggal="transaksi">
                    {{
                        new Date(transaksi.tanggal).toLocaleDateString(
                            "id-ID",
                            {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }
                        )
                    }}
                </template>
                <template #item-actions="transaksi">
                    <button
                        @click="openEditModal(transaksi)"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                            />
                        </svg>
                    </button>
                    <button
                        @click="deleteTransaksi(transaksi.id)"
                        class="tpx-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                            />
                        </svg>
                    </button>
                </template>
            </Vue3EasyDataTable>
            <div
                v-if="open"
                class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4 modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modalTitle"
            >
                <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                    <h2
                        id="modalTitle"
                        class="text-xl font-bold text-gray-900 sm:text-2xl"
                    >
                        {{
                            isEdit
                                ? "Edit Data Transaksi"
                                : "Tambah Data Transaksi"
                        }}
                    </h2>

                    <div class="mt-4">
                        <form @submit.prevent="submit">
                            <label for="Confirm" class="mt-4 block">
                                <span class="text-sm font-medium text-gray-700">
                                    Name :
                                </span>

                                <input
                                    type="text"
                                    id="name"
                                    class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                    v-model="form.name"
                                />
                                <div
                                    v-if="form.errors.name"
                                    class="text-red-600"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </label>
                            <label for="Confirm" class="mt-4 block">
                                <span class="text-sm font-medium text-gray-700">
                                    Tanggal :
                                </span>

                                <input
                                    type="date"
                                    id="tanggal"
                                    class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                    v-model="form.tanggal"
                                />
                                <div
                                    v-if="form.errors.tanggal"
                                    class="text-red-600"
                                >
                                    {{ form.errors.tanggal }}
                                </div>
                            </label>
                            <label for="Headline" class="mt-4 block">
                                <span class="text-sm font-medium text-gray-700">
                                    Jenis :
                                </span>

                                <select
                                    name="Headline"
                                    id="jenis"
                                    class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                    v-model="form.jenis"
                                >
                                    <option value="" disabled selected>
                                        Please select
                                    </option>
                                    <option value="Pemasukan">Pemasukan</option>
                                    <option value="Pengeluaran">
                                        Pengeluaran
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.jenis"
                                    class="text-red-600"
                                >
                                    {{ form.errors.jenis }}
                                </div>
                            </label>
                            <label for="Confirm" class="mt-4 block">
                                <span class="text-sm font-medium text-gray-700">
                                    Nominal :
                                </span>

                                <input
                                    type="number"
                                    id="nominal"
                                    class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                    v-model="form.nominal"
                                />
                                <div
                                    v-if="form.errors.nominal"
                                    class="text-red-600"
                                >
                                    {{ form.errors.nominal }}
                                </div>
                            </label>
                            <label for="Notes" class="mt-4 block">
                                <span class="text-sm font-medium text-gray-700">
                                    Notes :
                                </span>

                                <textarea
                                    id="deskripsi"
                                    class="mt-0.5 w-full resize-none rounded border-gray-300 shadow-sm sm:text-sm"
                                    rows="4"
                                    v-model="form.deskripsi"
                                >
                                </textarea>
                                <div
                                    v-if="form.errors.deskripsi"
                                    class="text-red-600"
                                >
                                    {{ form.errors.deskripsi }}
                                </div>
                            </label>
                            <footer class="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200"
                                    @click="closemodalAndReset()"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    {{ isEdit ? "Update" : "Save" }}
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
button {
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-right: 5px;
}
</style>
