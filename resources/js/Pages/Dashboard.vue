<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, watchEffect } from "vue";
import BarChart from "../Components/Barchart.vue";

const props = defineProps({
    pemasukans: {
        type: Number,
        default: 0,
    },
    pengeluarans: {
        type: Number,
        default: 0,
    },
    saldos: {
        type: Number,
        default: 0,
    },
    pemasukanFinals: {
        type: Number,
        default: 0,
    },
    pengeluaranFinals: {
        type: Number,
        default: 0,
    },
});


const value = ref(false);

const financialData = ref({
    labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "Mei",
        "Jun",
        "Jul",
        "Agu",
        "Sep",
        "Okt",
        "Nov",
        "Des",
    ],
    datasets: [],
});

watchEffect(() => {
    financialData.value.datasets = [
        {
            label: "Pemasukan",
            backgroundColor: "#4ade80",
            borderColor: "#22c55e",
            borderWidth: 1,
            data: props.pemasukanFinals,
        },
        {
            label: "Pengeluaran",
            backgroundColor: "#ef4444",
            borderColor: "#dc2626",
            borderWidth: 1,
            data: props.pengeluaranFinals,
        },
    ];
});
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) {
                        label += ": ";
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        }).format(context.parsed.y);
                    }
                    return label;
                },
            },
        },
        title: {
            display: true,
            text: "Pemasukan vs Pengeluaran Bulanan",
            font: { size: 18 },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function (value, index, values) {
                    return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        maximumFractionDigits: 0,
                    }).format(value);
                },
            },
        },
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 leading-tight text-center"
            >
                Halaman Dashboard
            </h2>
            <button
                type="button"
                class="inline-block rounded-sm border border-current px-8 py-3 text-sm font-medium text-indigo-600 transition hover:scale-110 hover:shadow-xl focus:ring-3 focus:outline-hidden"
                @click="value = !value"
            >
                Toggle
            </button>
        </template>
        <div class="pl-3 pt-6 grid grid-cols-3 gap-x-8 gap-y-4">
            <article
                class="w-64 gap-4 rounded-lg border border-gray-100 bg-white p-6"
            >
                <div>
                    <strong class="block text-sm font-medium text-gray-500">
                        Saldo
                    </strong>

                    <p v-if="value">
                        <span class="text-2xl font-medium text-gray-900">
                            {{
                                props.saldos.toLocaleString("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2,
                                })
                            }}
                        </span>
                    </p>
                    <p v-else>
                        <span class="text-2xl font-medium text-gray-900"
                            >******</span
                        >
                    </p>
                </div>
            </article>
            <article
                class="w-64 gap-4 rounded-lg border border-gray-100 bg-white p-6"
            >
                <div>
                    <strong class="block text-sm font-medium text-gray-500">
                        Pemasukan
                    </strong>

                    <p v-if="value">
                        <span class="text-2xl font-medium text-gray-900">
                            {{
                                pemasukans.toLocaleString("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2,
                                })
                            }}
                        </span>
                    </p>
                    <p v-else>
                        <span class="text-2xl font-medium text-gray-900"
                            >******</span
                        >
                    </p>
                </div>
            </article>

            <article
                class="w-64 gap-4 rounded-lg border border-gray-100 bg-white p-6"
            >
                <div>
                    <strong class="block text-sm font-medium text-gray-500">
                        Pengeluaran
                    </strong>

                    <p v-if="value">
                        <span class="text-2xl font-medium text-gray-900">
                            {{
                                pengeluarans.toLocaleString("id-ID", {
                                    style: "currency",
                                    currency: "IDR",
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2,
                                })
                            }}
                        </span>
                    </p>
                    <p v-else>
                        <span class="text-2xl font-medium text-gray-90"
                            >******</span
                        >
                    </p>
                </div>
            </article>
        </div>
        <div
            class="mt-4"
            style="position: relative; height: 400px; width: 100%"
        >
            <BarChart :chartData="financialData" :chartOptions="chartOptions" />
        </div>
    </AppLayout>
</template>
