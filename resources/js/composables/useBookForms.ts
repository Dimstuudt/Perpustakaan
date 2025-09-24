import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import Tesseract from 'tesseract.js'

export interface BookForm {
  isbn: string
  title: string
  author: string
  publisher: string
  year: string
  pages: string
  description: string
  category_id: string | number
  file: File | null              // File buku (PDF/EPUB)
  cover: File | null             // Sampul
  coverPreview: string
  ocrFile: File | null           // Gambar hasil OCR
  ocrPreview: string | null
  ocrResult: string
  loadingOCR: boolean
}

const defaultCover = '/images/dummy-cover.png'

export default function useBooksForms() {
  const forms = ref<BookForm[]>([])
  const activeIndex = ref(0)
  const MAX_FORMS = 5

  const addFormManual = () => {
    if (forms.value.length >= MAX_FORMS) {
      Swal.fire('Maksimal hanya 5 form!', '', 'warning')
      return
    }
    forms.value.push({
      isbn: '',
      title: '',
      author: '',
      publisher: '',
      year: '',
      pages: '',
      description: '',
      category_id: '',
      file: null,
      cover: null,
      coverPreview: defaultCover,
      ocrFile: null,
      ocrPreview: null,
      ocrResult: '',
      loadingOCR: false,
    })
    activeIndex.value = forms.value.length - 1
  }

  // OCR Parser
  const parseToForm = (text: string, form: BookForm) => {
    const lines = text.split('\n').map(l => l.trim()).filter(l => l)
    lines.forEach((line, index) => {
      if (/isbn/i.test(line)) form.isbn = line.replace(/isbn[: ]?/i, '').trim()
      else if (/judul/i.test(line)) form.title = line.replace(/judul[: ]?/i, '').trim()
      else if (/penulis/i.test(line)) form.author = line.replace(/penulis[: ]?/i, '').trim()
      else if (/penerbit/i.test(line)) form.publisher = line.replace(/penerbit[: ]?/i, '').trim()
      else if (/tahun/i.test(line)) form.year = line.replace(/tahun[: ]?/i, '').trim()
      else if (/halaman/i.test(line)) form.pages = line.replace(/halaman[: ]?/i, '').trim()
      else if (!form.title && index === 0) form.title = line.trim()
    })

    const descMatch = text.match(/Deskripsi[:\s]+([\s\S]+)/i)
    if (descMatch) form.description = descMatch[1].trim()
  }

  // Tambah file OCR
  const addFileToQueue = (file: File) => {
    if (forms.value.length >= MAX_FORMS) {
      Swal.fire('Maksimal hanya 5 form!', '', 'warning')
      return
    }
    forms.value.push({
      isbn: '',
      title: '',
      author: '',
      publisher: '',
      year: '',
      pages: '',
      description: '',
      category_id: '',
      file: null,
      cover: null,               // tetap null, OCR tidak menimpa cover
      coverPreview: defaultCover,
      ocrFile: file,
      ocrPreview: URL.createObjectURL(file),
      ocrResult: '',
      loadingOCR: false,
    })
    activeIndex.value = forms.value.length - 1
  }

  const handleMultipleFiles = (e: Event) => {
    const files = (e.target as HTMLInputElement).files
    if (!files) return
    Array.from(files).forEach(file => addFileToQueue(file))
    ;(e.target as HTMLInputElement).value = ''
  }

  // Proses OCR
  const processOCR = async (form: BookForm) => {
    if (!form.ocrFile) return
    form.loadingOCR = true
    form.ocrResult = '⏳ Membaca teks...'
    try {
      const { data: { text } } = await Tesseract.recognize(form.ocrFile, 'eng+ind')
      form.ocrResult = text
      parseToForm(text, form)
    } catch (err: any) {
      form.ocrResult = '❌ Gagal OCR: ' + err.message
    } finally {
      form.loadingOCR = false
    }
  }

  const processAllOCR = async () => {
    for (const form of forms.value) {
      if (form.ocrFile && !form.ocrResult) {
        await processOCR(form)
      }
    }
  }

  const resetForms = () => {
    forms.value.forEach(f => {
      if (f.ocrPreview) URL.revokeObjectURL(f.ocrPreview)
      if (f.coverPreview && f.coverPreview !== defaultCover) URL.revokeObjectURL(f.coverPreview)
    })
    forms.value = []
    activeIndex.value = 0
  }

  // Submit semua buku
  const submitAll = async () => {
    if (!forms.value.length) {
      Swal.fire('Belum ada form buku!', '', 'warning')
      return
    }

    await processAllOCR()

    for (let i = 0; i < forms.value.length; i++) {
      const f = forms.value[i]
      if (!f.isbn || !f.title || !f.author || !f.publisher || !f.year || !f.pages || !f.category_id) {
        Swal.fire({
          icon: 'error',
          title: 'Form Tidak Lengkap',
          text: `Buku ke-${i + 1} masih ada field wajib yang kosong!`,
        })
        activeIndex.value = i
        return
      }
    }

    const fd = new FormData()
    forms.value.forEach((form, i) => {
      fd.append(`books[${i}][isbn]`, form.isbn)
      fd.append(`books[${i}][title]`, form.title)
      fd.append(`books[${i}][author]`, form.author)
      fd.append(`books[${i}][publisher]`, form.publisher)
      fd.append(`books[${i}][year]`, form.year)
      fd.append(`books[${i}][pages]`, form.pages)
      fd.append(`books[${i}][description]`, form.description)
      fd.append(`books[${i}][category_id]`, form.category_id.toString())

      // 🔹 Kirim cover & file hanya kalau user pilih
      if (form.cover instanceof File) fd.append(`books[${i}][cover]`, form.cover)
      if (form.file instanceof File) fd.append(`books[${i}][file]`, form.file)
    })

    router.post(route('books.storeMultiple'), fd, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Semua buku berhasil disimpan.',
          timer: 2000,
          showConfirmButton: false,
        })
        resetForms()
      },
      onError: () => {
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Terjadi kesalahan saat menyimpan data.',
        })
      },
    })
  }

  const nextForm = () => {
    if (activeIndex.value < forms.value.length - 1) activeIndex.value++
  }
  const prevForm = () => {
    if (activeIndex.value > 0) activeIndex.value--
  }

  return {
    forms,
    activeIndex,
    MAX_FORMS,
    addFormManual,
    handleMultipleFiles,
    processOCR,
    processAllOCR,
    submitAll,
    nextForm,
    prevForm,
    addFileToQueue,
  }
}
