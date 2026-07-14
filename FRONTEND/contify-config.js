/*
 * Konfigurasi koneksi frontend Contify.
 * Secara otomatis mendeteksi host saat ini untuk kemudahan deployment.
 * Jika dijalankan di VPS, akan menggunakan IP/domain VPS secara otomatis.
 */
(function () {
  // Deteksi otomatis: gunakan host yang sama dengan frontend
  // Lokal: http://127.0.0.1:8000 | VPS: http://203.175.10.112:8000
  var apiHost =
    window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost'
      ? 'http://127.0.0.1:8000'
      : 'http://' + window.location.hostname + ':8000';

  window.CONTIFY_CONFIG = {
    apiBaseUrl: apiHost + '/api',
    storageBaseUrl: apiHost + '/storage',
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
})();
