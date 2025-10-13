<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const form = useForm({
    file: null,
})

const previewBooks = ref([]) // data dari Excel preview

function selectFile(event: Event) {
    const target = event.target as HTMLInputElement
    if (target.files?.length) {
        form.file = target.files[0]
        previewExcel(form.file)
    }
}

// Preview Excel (client-side read)
function previewExcel(file: File) {
    const reader = new FileReader()
    reader.onload = async (e) => {
        const data = new Uint8Array(e.target?.result as ArrayBuffer)
        const workbook = await import('xlsx').then(xlsx => xlsx.read(data, { type: 'array' }))
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]]
        const jsonData = await import('xlsx').then(xlsx => xlsx.utils.sheet_to_json(firstSheet, { header: 1 }))

        // Skip header row
        previewBooks.value = jsonData.slice(1).map(row => ({
            isbn: row[0] || '',
            title: row[1] || '',
            author: row[2] || '',
            publisher: row[3] || '',
            year: row[4] || '',
            pages: row[5] || '',
            description: row[6] || '',
            category_id: row[7] || '',
            type: row[8] || 'physical',
            stock: row[9] || 0,
            fee: row[10] || 0,
        }))
    }
    reader.readAsArrayBuffer(file)
}

function submit() {
    form.post(route('books.import.store'), {
        onSuccess: (page) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: page.props.flash.success || 'Data berhasil diimport',
            })
            form.reset()
            previewBooks.value = []
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errors.file || 'Terjadi kesalahan saat mengimport',
            })
        }
    })
}

// Tombol download template
function downloadTemplate() {
    const link = document.createElement('a')
    link.href = '/TemplateBooksEmpty.xlsx'   // pastikan file ada di folder public
    link.download = 'TemplateBooksEmpty.xlsx' // nama file saat di-download
    document.body.appendChild(link)          // perlu append dulu
    link.click()                             // trigger click
    document.body.removeChild(link)          // hapus elemen setelah klik
}

</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold">Import Buku</h1>

      <div class="flex flex-wrap items-center gap-4">
        <input
          type="file"
          @change="selectFile"
          accept=".xlsx,.xls,.csv"
          class="border p-2 rounded"
        />
        <button
          @click="downloadTemplate"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        >
          Download Template
        </button>
      </div>

      <button
        @click="submit"
        :disabled="form.processing || !form.file"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
      >
        Import
      </button>

      <div v-if="previewBooks.length" class="overflow-x-auto mt-4">
        <table class="table-auto border border-gray-300 w-full text-sm">
          <thead>
            <tr class="bg-gray-100">
              <th class="border px-2 py-1">ISBN</th>
              <th class="border px-2 py-1">Title</th>
              <th class="border px-2 py-1">Author</th>
              <th class="border px-2 py-1">Publisher</th>
              <th class="border px-2 py-1">Year</th>
              <th class="border px-2 py-1">Pages</th>
              <th class="border px-2 py-1">Category ID</th>
              <th class="border px-2 py-1">Type</th>
              <th class="border px-2 py-1">Stock</th>
              <th class="border px-2 py-1">Fee</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(book, i) in previewBooks" :key="i" class="hover:bg-gray-50">
              <td class="border px-2 py-1">{{ book.isbn }}</td>
              <td class="border px-2 py-1">{{ book.title }}</td>
              <td class="border px-2 py-1">{{ book.author }}</td>
              <td class="border px-2 py-1">{{ book.publisher }}</td>
              <td class="border px-2 py-1">{{ book.year }}</td>
              <td class="border px-2 py-1">{{ book.pages }}</td>
              <td class="border px-2 py-1">{{ book.category_id }}</td>
              <td class="border px-2 py-1">{{ book.type }}</td>
              <td class="border px-2 py-1">{{ book.stock }}</td>
              <td class="border px-2 py-1">{{ book.fee }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
