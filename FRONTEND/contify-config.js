/*
 * Konfigurasi koneksi frontend Contify.
 * Ubah apiBaseUrl hanya jika backend dijalankan pada host atau port berbeda.
 */
window.CONTIFY_CONFIG = {
  apiBaseUrl: 'http://127.0.0.1:8000/api',
  storageBaseUrl: 'http://127.0.0.1:8000/storage',
  paymentChannels: {
    bank_transfer: [
      { id: 1, label: 'BCA Virtual Account' },
      { id: 2, label: 'BRI Virtual Account' },
      { id: 3, label: 'Mandiri Virtual Account' },
    ],
    qris: [
      { id: 4, label: 'QRIS' },
    ],
    e_wallet: [
      { id: 5, label: 'GoPay' },
      { id: 6, label: 'OVO' },
    ],
  },
};
