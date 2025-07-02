<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import { onMounted, ref } from "vue";

const open = ref(false);
const inputNama = ref("");
const inputTanggal = ref("");
const inputJenis = ref("");
const inputNominal = ref("");
const inputDeskripsi = ref("");
const url = "http://localhost:8000/api";
const dataTransaksi = ref([]);
const isUpdate = ref(false);
const idTransaksi = ref("");

const getTransaksi = async () => {
    try {
        const response = await axios.get(url + "/transaksi");
        dataTransaksi.value = response.data.data.data;
    } catch (error) {
        console.log(error);
    }
};

const clearInput = () => {
    inputNama.value = "";
    inputTanggal.value = "";
    inputJenis.value = "";
    inputNominal.value = "";
    inputDeskripsi.value = "";
    isUpdate.value = false;
};

const onHandleSubmit = async () => {
    if (isUpdate.value == false) {
        await axios.post(url + "/transaksi", {
            name: inputNama.value,
            tanggal: inputTanggal.value,
            jenis: inputJenis.value,
            nominal: inputNominal.value,
            deskripsi: inputDeskripsi.value,
        });
        open.value = false;
        alert("Berhasil Tambah data Transaksi");
    } else {
        await axios.put(url + "/transaksi/" + idTransaksi.value, {
            name: inputNama.value,
            tanggal: inputTanggal.value,
            jenis: inputJenis.value,
            nominal: inputNominal.value,
            deskripsi: inputDeskripsi.value,
        });
        open.value = false;
        alert("Update Berhasil");
    }
    clearInput();
    getTransaksi();
};

const getDataTransaksi = async (objTransaksi) => {
    inputNama.value = objTransaksi.name;
    inputTanggal.value = objTransaksi.tanggal;
    inputJenis.value = objTransaksi.jenis;
    inputNominal.value = objTransaksi.nominal;
    inputDeskripsi.value = objTransaksi.deskripsi;
    idTransaksi.value = objTransaksi.id;
    isUpdate.value = true;
};
onMounted(() => {
    getTransaksi();
});
</script>

<template>
    <AppLayout title="Transaksi">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Halaman Transaksi
            </h2>
        </template>

        <div class="py-6 px-6">
            <span
                class="inline-flex divide-x divide-gray-300 overflow-hidden rounded-[2vw] shadow-sm py-5"
            >
                <a
                    @click="
                        open = true;
                        isUpdate = false;
                    "
                    class="inline-block rounded-sm border border-indigo-600 bg-indigo-600 px-12 py-3 text-sm font-medium text-white hover:bg-transparent hover:text-indigo-600 focus:ring-3 focus:outline-hidden"
                >
                    Tambah Data
                </a>
            </span>
            <span>
                <a
                    class="inline-block rounded-sm border border-red-600 bg-red-600 px-12 py-3 text-sm font-medium text-white hover:bg-transparent hover:text-indigo-600 focus:ring-3 focus:outline-hidden"
                    >Hapus</a
                >
            </span>
            <div class="w-full mx-auto">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div
                        class="overflow-x-auto rounded border border-gray-300 shadow-sm"
                    >
                        <table class="w-full divide-y-2 divide-gray-200">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr class="*:font-medium *:text-gray-900">
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        No
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Nama
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Tanggal
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Jenis
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Nominal
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Deskripsi
                                    </th>
                                    <th class="px-3 py-2 whitespace-nowrap">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 text-center">
                                <template v-if="dataTransaksi.length > 0">
                                    <tr
                                        v-for="transaksi in dataTransaksi"
                                        :key="transaksi.id"
                                        class="*:text-gray-900 *:first:font-medium"
                                    >
                                        <td>{{ transaksi.id }}</td>
                                        <td>{{ transaksi.name }}</td>
                                        <td>{{ transaksi.tanggal }}</td>
                                        <td class="text-center">
                                            <div
                                                v-if="
                                                    transaksi.jenis ==
                                                    'Pemasukan'
                                                "
                                            >
                                                <td
                                                    class="text-green-600 text-center"
                                                >
                                                    {{ transaksi.jenis }}
                                                </td>
                                            </div>
                                            <div v-else>
                                                <td class="text-red-600">
                                                    {{ transaksi.jenis }}
                                                </td>
                                            </div>
                                        </td>

                                        <td>{{ transaksi.nominal }}</td>
                                        <td>{{ transaksi.deskripsi }}</td>
                                        <td>
                                            <span
                                                class="inline-flex divide-x divide-gray-300 overflow-hidden rounded border border-gray-300 bg-white shadow-sm"
                                            >
                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 focus:relative"
                                                    aria-label="Edit"
                                                    @click="
                                                        getDataTransaksi(
                                                            transaksi
                                                        );
                                                        open = true;
                                                    "
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
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-else>
                                    <td colspan="7">
                                        Tidak Ada Transaksi Yang Tersedia.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <Teleport to="body">
                            <div v-if="open" class="modal">
                                <div
                                    class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4"
                                    role="dialog"
                                    aria-modal="true"
                                    aria-labelledby="modalTitle"
                                >
                                    <div
                                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg"
                                    >
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <h2
                                                id="modalTitle"
                                                class="text-xl font-bold text-gray-900 sm:text-2xl"
                                            >
                                                Tambah Transaksi
                                            </h2>

                                            <button
                                                type="button"
                                                class="me-4 -mt-4 rounded-full p-2 text-gray-400 transition-colors hover:bg-gray-50 hover:text-gray-600 focus:outline-none"
                                                aria-label="Close"
                                                @click="
                                                    open = false;
                                                    clearInput();
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="size-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"
                                                    />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-4">
                                            <form
                                                @submit.prevent="onHandleSubmit"
                                            >
                                                <label for="Nama">
                                                    <span
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Nama
                                                    </span>

                                                    <input
                                                        type="text"
                                                        id="nama"
                                                        class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                                        placeholder="Roko, Makan, Jajan"
                                                        v-model="inputNama"
                                                    />
                                                </label>
                                                <label for="Tanggal">
                                                    <span
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Tanggal
                                                    </span>

                                                    <input
                                                        type="date"
                                                        id="tanggal"
                                                        class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                                        v-model="inputTanggal"
                                                    />
                                                </label>
                                                <label for="Jenis">
                                                    <span
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Jenis
                                                    </span>

                                                    <select
                                                        name="Jenis"
                                                        id="Jenis"
                                                        class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                                        v-model="inputJenis"
                                                    >
                                                        <option
                                                            value=""
                                                            disabled
                                                            selected
                                                        >
                                                            Please select
                                                        </option>
                                                        <option
                                                            value="Pemasukan"
                                                        >
                                                            Pemasukan
                                                        </option>
                                                        <option
                                                            value="Pengeluaran"
                                                        >
                                                            Pengeluaran
                                                        </option>
                                                    </select>
                                                </label>
                                                <label for="Nominal">
                                                    <span
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Nominal
                                                    </span>

                                                    <input
                                                        type="number"
                                                        id="nominal"
                                                        class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm"
                                                        placeholder="100.000"
                                                        v-model="inputNominal"
                                                    />
                                                </label>
                                                <label for="Deskripsi">
                                                    <span
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Deskripsi
                                                    </span>

                                                    <textarea
                                                        id="Deskripsi"
                                                        class="mt-0.5 w-full resize-none rounded border-gray-300 shadow-sm sm:text-sm"
                                                        rows="4"
                                                        placeholder="Djarum Coklat Extra 16 Batang"
                                                        v-model="inputDeskripsi"
                                                    ></textarea>
                                                </label>
                                                <div
                                                    class="mt-6 flex justify-end gap-2"
                                                >
                                                    <button
                                                        type="submit"
                                                        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                                        :disabled="
                                                            inputNama == ''
                                                        "
                                                        :class="
                                                            isUpdate
                                                                ? 'bg-green-500'
                                                                : 'bg-blue-600'
                                                        "
                                                    >
                                                        {{
                                                            isUpdate
                                                                ? "Update"
                                                                : "Tambah"
                                                        }}
                                                    </button>
                                                    <button
                                                        v-if="isUpdate"
                                                        type="button"
                                                        @click="clearInput"
                                                        class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white"
                                                    >
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Teleport>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.modal {
    position: fixed;
    z-index: 999;
    top: 20%;
    left: 50%;
    width: 300px;
    margin-left: -150px;
}
</style>
