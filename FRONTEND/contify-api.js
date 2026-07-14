/*
 * Contify API Integration
 * Menghubungkan frontend statis dengan API Laravel yang sudah tersedia.
 * Base URL default: http://127.0.0.1:8000/api
 */

(() => {
  const CONFIG = window.CONTIFY_CONFIG || {};
  const API_BASE_URL = (
    localStorage.getItem('contify_api_base')
    || CONFIG.apiBaseUrl
    || 'http://127.0.0.1:8000/api'
  ).replace(/\/$/, '');
  const API_ORIGIN = API_BASE_URL.replace(/\/api$/, '');
  const STORAGE_BASE_URL = (CONFIG.storageBaseUrl || `${API_ORIGIN}/storage`).replace(/\/$/, '');
  const TOKEN_KEY = 'contify_api_token';
  const USER_KEY = 'contify_api_user';

  const PAYMENT_CHANNELS = CONFIG.paymentChannels || {
    bank_transfer: [{ id: 1, label: 'BCA Virtual Account' }],
    qris: [{ id: 4, label: 'QRIS' }],
    e_wallet: [{ id: 5, label: 'GoPay' }],
  };

  let apiUser = readJson(USER_KEY);
  let selectedOrderFiles = [];
  let freelancerJobs = [];
  let freelancerTasks = [];
  let adminRevisions = [];
  let pendingPayment = null;

  const legacyUpdateNavForRole = updateNavForRole;
  const legacyCalSelect = calSelect;

  class ApiError extends Error {
    constructor(message, status = 0, errors = null) {
      super(message);
      this.name = 'ApiError';
      this.status = status;
      this.errors = errors;
    }
  }

  function readJson(key) {
    try {
      return JSON.parse(localStorage.getItem(key) || 'null');
    } catch (_) {
      return null;
    }
  }

  function getToken() {
    return localStorage.getItem(TOKEN_KEY) || '';
  }

  function saveSession(token, user) {
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
    apiUser = user;
  }

  function clearSession() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    apiUser = null;
  }

  async function apiRequest(path, options = {}) {
    const headers = new Headers(options.headers || {});
    headers.set('Accept', 'application/json');

    const token = getToken();
    if (token) headers.set('Authorization', `Bearer ${token}`);

    const isFormData = options.body instanceof FormData;
    if (options.body && !isFormData && !headers.has('Content-Type')) {
      headers.set('Content-Type', 'application/json');
    }

    let response;
    try {
      response = await fetch(`${API_BASE_URL}${path}`, {
        ...options,
        headers,
      });
    } catch (error) {
      throw new ApiError(
        'Backend tidak dapat dihubungi. Pastikan php artisan serve berjalan di http://127.0.0.1:8000.',
        0,
        null
      );
    }

    const contentType = response.headers.get('content-type') || '';
    let payload = null;

    if (contentType.includes('application/json')) {
      payload = await response.json();
    } else {
      const text = await response.text();
      payload = { message: text || `HTTP ${response.status}` };
    }

    if (!response.ok) {
      if (response.status === 401) clearSession();
      throw new ApiError(
        getValidationMessage(payload) || payload?.message || `Permintaan gagal (HTTP ${response.status}).`,
        response.status,
        payload?.errors || null
      );
    }

    return payload;
  }

  function getValidationMessage(payload) {
    if (!payload?.errors || typeof payload.errors !== 'object') return '';
    for (const value of Object.values(payload.errors)) {
      if (Array.isArray(value) && value[0]) return value[0];
      if (typeof value === 'string') return value;
    }
    return '';
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function rupiah(value) {
    return `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
  }

  function formatDate(value, withTime = false) {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value);
    return new Intl.DateTimeFormat('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      ...(withTime ? { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' } : {}),
    }).format(date);
  }

  function fileUrl(path) {
    if (!path) return '#';
    if (/^https?:\/\//i.test(path)) return path;
    return `${STORAGE_BASE_URL}/${String(path).replace(/^\/+/, '')}`;
  }

  function fileIcon(mime = '', name = '') {
    const source = `${mime} ${name}`.toLowerCase();
    if (source.includes('video') || /\.(mp4|mov)$/i.test(name)) return '🎬';
    if (source.includes('image') || /\.(jpg|jpeg|png)$/i.test(name)) return '🖼️';
    if (source.includes('pdf') || /\.pdf$/i.test(name)) return '📄';
    if (/\.zip$/i.test(name)) return '📦';
    return '📎';
  }

  function uiRole(role) {
    return role === 'client' ? 'user' : role;
  }

  function categoryIcon(packageData) {
    const text = `${packageData?.category?.name || ''} ${packageData?.name || ''}`.toLowerCase();
    if (text.includes('foto')) return '📷';
    if (text.includes('video')) return '🎬';
    if (text.includes('copy')) return '✍️';
    return '🗺️';
  }

  function speedLabel(speed, packageData = null) {
    const days = packageData
      ? (speed === 'fast'
        ? packageData.fast_days
        : speed === 'express'
          ? packageData.express_days
          : packageData.regular_days)
      : null;

    const label = speed === 'fast' ? 'Cepat' : speed === 'express' ? 'Kilat' : 'Reguler';
    return days ? `${label} (${days} hari)` : label;
  }

  function normalizePackage(data) {
    return {
      id: String(data.id),
      apiId: Number(data.id),
      name: data.name,
      desc: data.description || '',
      price: Number(data.base_price || 0),
      time: `${data.regular_days || 1} hari`,
      revision: `${data.revision_limit || 0}x`,
      totalSlot: Number(data.total_slot || 0),
      usedSlot: 0,
      isActive: Boolean(data.is_active),
      category: data.category,
      raw: data,
    };
  }

  function submissionFiles(orderData) {
    const submission = orderData.current_submission;
    if (!submission?.files) return [];
    return submission.files.map((file) => ({
      type: fileIcon(file.mime_type, file.original_name),
      label: `Versi ${submission.version}`,
      name: file.original_name,
      path: file.file_path,
      url: fileUrl(file.file_path),
      isFinal: Boolean(file.is_final),
    }));
  }

  function normalizeOrder(data) {
    const packageData = data.package || {};
    const payment = data.latest_payment || null;
    const remaining = Math.max(0, Number(data.revision_limit || 0) - Number(data.revision_used || 0));

    return {
      id: data.order_code || `#${data.id}`,
      apiId: Number(data.id),
      client: data.client?.name || 'Saya (UMKM)',
      pkg: packageData.name || 'Paket',
      packageId: Number(data.service_package_id || packageData.id || 0),
      title: data.title,
      price: Number(data.total_amount || 0),
      status: data.status,
      date: formatDate(data.deadline_at || data.booking_date, Boolean(data.deadline_at)),
      method: payment?.channel?.name || payment?.channel?.code || 'Belum dipilih',
      paymentStatus: payment?.status || 'pending',
      paymentId: payment?.id || null,
      time: formatDate(data.created_at, true),
      initials: (data.client?.name || 'UM').split(/\s+/).map((part) => part[0]).join('').slice(0, 2).toUpperCase(),
      speed: speedLabel(data.speed_type, packageData),
      revisions: remaining,
      revisionLimit: Number(data.revision_limit || 0),
      revisionUsed: Number(data.revision_used || 0),
      files: submissionFiles(data),
      submissions: data.submissions || [],
      assets: data.assets || [],
      activeRevision: data.active_revision || null,
      freelancer: data.freelancer || null,
      raw: data,
    };
  }

  function setButtonBusy(button, busy, text = 'Memproses...') {
    if (!button) return;
    if (busy) {
      button.dataset.originalText = button.textContent;
      button.textContent = text;
      button.disabled = true;
      button.style.opacity = '0.65';
    } else {
      button.textContent = button.dataset.originalText || button.textContent;
      button.disabled = false;
      button.style.opacity = '1';
    }
  }

  function showError(error) {
    console.error(error);
    alert(error?.message || 'Terjadi kesalahan. Silakan coba lagi.');
  }

  async function loadPackages() {
    const response = await apiRequest('/packages');
    packages = (response.data || []).map(normalizePackage);

    if (packages.length > 0) {
      const selected = packages.find((item) => item.apiId === Number(orderState.packageId)) || packages[0];
      orderState.pkg = selected.name;
      orderState.packageId = selected.apiId;
      orderState.price = selected.price;
      orderState.speedType = orderState.speedType || 'regular';
    }

    renderOrderPackages();
    renderAdminCMSPackages();
    updatePublicPricingCards();
    updatePrice();
  }

  async function loadClientOrders() {
    const response = await apiRequest('/orders');
    orders = (response.data || []).map(normalizeOrder);
    renderUserDashboard();
  }

  async function loadFreelancerData() {
    const [jobsResponse, tasksResponse] = await Promise.all([
      apiRequest('/freelancer/jobs'),
      apiRequest('/freelancer/tasks'),
    ]);

    freelancerJobs = (jobsResponse.data || []).map(normalizeOrder);
    freelancerTasks = (tasksResponse.data || []).map(normalizeOrder);
    renderFreelancerDashboard();
  }

  async function loadAdminData() {
    const [ordersResponse, revisionsResponse] = await Promise.all([
      apiRequest('/orders'),
      apiRequest('/admin/revisions'),
    ]);

    orders = (ordersResponse.data || []).map(normalizeOrder);
    adminRevisions = revisionsResponse.data || [];
    renderAdminKanban();
    renderAdminVerification();
    renderAdminStats();
    renderAdminRevisions();
  }

  async function loadRoleData() {
    await loadPackages();

    if (apiUser?.role === 'client') await loadClientOrders();
    if (apiUser?.role === 'freelancer') await loadFreelancerData();
    if (apiUser?.role === 'admin') await loadAdminData();
  }

  function updateLoggedUserNav() {
    if (!apiUser) return;
    const role = uiRole(apiUser.role);
    currentRole = role;
    legacyUpdateNavForRole(role);
    const badgeName = document.getElementById('userBadgeName');
    if (badgeName) badgeName.textContent = apiUser.name || apiUser.username || role;
  }

  doLoginUnified = async function doLoginUnifiedApi() {
    const login = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const errorElement = document.getElementById('loginError');
    const button = document.querySelector('#unifiedLoginForm .login-btn');

    if (!login || !password) {
      errorElement.textContent = 'Username dan password wajib diisi.';
      errorElement.style.display = 'block';
      return;
    }

    setButtonBusy(button, true, 'Menghubungkan...');

    try {
      const response = await apiRequest('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ login, password }),
      });

      saveSession(response.token, response.user);
      updateLoggedUserNav();
      await loadRoleData();
      errorElement.style.display = 'none';
      document.getElementById('loginOverlay').classList.add('hidden');
      window.scrollTo({ top: 0, behavior: 'instant' });
    } catch (error) {
      errorElement.textContent = error.message;
      errorElement.style.display = 'block';
    } finally {
      setButtonBusy(button, false);
    }
  };

  doLogout = async function doLogoutApi() {
    try {
      if (getToken()) await apiRequest('/auth/logout', { method: 'POST' });
    } catch (error) {
      console.warn('Logout API gagal, sesi lokal tetap dibersihkan.', error);
    }

    clearSession();
    currentRole = null;
    orders = [];
    freelancerJobs = [];
    freelancerTasks = [];
    adminRevisions = [];

    document.getElementById('userBadge').style.display = 'none';
    document.getElementById('navLoginBtn').style.display = '';
    document.getElementById('navAdminLink').style.display = 'none';
    document.getElementById('navFreelancerLink').style.display = 'none';
    document.getElementById('navUserLink').style.display = 'none';
    document.getElementById('loginUsername').value = '';
    document.getElementById('loginPassword').value = '';
    document.getElementById('loginOverlay').classList.remove('hidden');
    applyVisibilityForRole(null);
    window.scrollTo({ top: 0, behavior: 'instant' });
  };

  renderOrderPackages = function renderOrderPackagesApi() {
    const container = document.getElementById('orderPkgGrid');
    if (!container) return;

    if (!packages.length) {
      container.innerHTML = '<div style="color:var(--text3);padding:20px">Belum ada paket aktif.</div>';
      return;
    }

    container.innerHTML = packages.map((item) => `
      <div class="pkg-card ${Number(orderState.packageId) === item.apiId ? 'selected' : ''}"
           onclick="selectApiPackage(this, ${item.apiId})">
        <div class="pkg-icon">${categoryIcon(item.raw)}</div>
        <div class="pkg-name">${escapeHtml(item.name)}</div>
        <div class="pkg-price">${rupiah(item.price)}</div>
      </div>
    `).join('');
  };

  window.selectApiPackage = function selectApiPackage(element, packageId) {
    const selected = packages.find((item) => item.apiId === Number(packageId));
    if (!selected) return;

    document.querySelectorAll('.pkg-card').forEach((card) => card.classList.remove('selected'));
    element.classList.add('selected');
    orderState.pkg = selected.name;
    orderState.packageId = selected.apiId;
    orderState.price = selected.price;
    document.getElementById('sumPkg').textContent = selected.name;
    updatePrice();
  };

  setDeadline = function setDeadlineApi(element, multiplier, label, days) {
    document.querySelectorAll('.deadline-opt').forEach((item) => item.classList.remove('selected'));
    element.classList.add('selected');
    orderState.multi = multiplier;
    orderState.speedType = label === 'Cepat' ? 'fast' : label === 'Kilat' ? 'express' : 'regular';
    orderState.speedLbl = `${label} (${days} hari)`;
    document.getElementById('sumSpeed').textContent = orderState.speedLbl;
    updatePrice();
  };

  function selectedPackage() {
    return packages.find((item) => item.apiId === Number(orderState.packageId))
      || packages.find((item) => item.name === orderState.pkg)
      || packages[0];
  }

  function previewPrice() {
    const item = selectedPackage();
    if (!item) return { subtotal: 0, discount: 0, total: 0 };

    const raw = item.raw;
    const percent = orderState.speedType === 'fast'
      ? Number(raw.fast_fee_percent || 0)
      : orderState.speedType === 'express'
        ? Number(raw.express_fee_percent || 0)
        : 0;

    const subtotal = item.price + Math.round(item.price * (percent / 100));
    let discount = 0;

    if (orderState.voucherCode === 'WELCOME10' && subtotal >= 50000) {
      discount = Math.min(Math.round(subtotal * 0.1), 50000);
    }
    if (orderState.voucherCode === 'UMKM50000' && subtotal >= 300000) {
      discount = 50000;
    }

    return { subtotal, discount, total: Math.max(0, subtotal - discount) };
  }

  updatePrice = function updatePriceApi() {
    const quote = previewPrice();
    const priceElement = document.getElementById('priceDisplay');
    if (priceElement) {
      priceElement.textContent = rupiah(quote.total);
      priceElement.classList.add('pop');
      setTimeout(() => priceElement.classList.remove('pop'), 350);
    }
    const totalElement = document.getElementById('sumTotal');
    if (totalElement) totalElement.textContent = rupiah(quote.total);
    const discountElement = document.getElementById('sumDiscount');
    if (discountElement) discountElement.textContent = quote.discount ? `−${rupiah(quote.discount)}` : '—';
  };

  handleFiles = function handleFilesApi(fileList) {
    const allowed = ['jpg', 'jpeg', 'png', 'pdf', 'zip', 'mp4', 'mov', 'doc', 'docx'];

    for (const file of Array.from(fileList || [])) {
      const extension = file.name.split('.').pop().toLowerCase();
      if (!allowed.includes(extension)) {
        alert(`File ${file.name} tidak didukung.`);
        continue;
      }
      if (file.size > 50 * 1024 * 1024) {
        alert(`File ${file.name} melebihi batas 50 MB.`);
        continue;
      }
      if (selectedOrderFiles.length >= 10) {
        alert('Maksimal 10 file aset untuk satu pesanan.');
        break;
      }
      if (!selectedOrderFiles.some((item) => item.name === file.name && item.size === file.size)) {
        selectedOrderFiles.push(file);
      }
    }

    renderSelectedFiles();
  };

  function renderSelectedFiles() {
    const container = document.getElementById('fileList');
    if (!container) return;
    container.innerHTML = selectedOrderFiles.map((file, index) => `
      <div class="file-pill">📎 ${escapeHtml(file.name)}
        <span class="remove" onclick="removeOrderFile(${index})">×</span>
      </div>
    `).join('');
  }

  window.removeOrderFile = function removeOrderFile(index) {
    selectedOrderFiles.splice(index, 1);
    renderSelectedFiles();
  };

  calSelect = function calSelectApi(element, day) {
    legacyCalSelect(element, day);
    orderState.bookingDate = `${calY}-${String(calM + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
  };

  selectPay = function selectPayApi(element) {
    element.closest('.pay-method-grid').querySelectorAll('.pay-method').forEach((item) => item.classList.remove('selected'));
    element.classList.add('selected');
    orderState.paymentMethod = element.dataset.method || 'bank_transfer';
    renderPaymentChannels();
  };

  function renderPaymentChannels() {
    const select = document.getElementById('paymentChannelSelect');
    if (!select) return;
    const method = orderState.paymentMethod || document.querySelector('.pay-method.selected')?.dataset.method || 'bank_transfer';
    orderState.paymentMethod = method;
    const channels = PAYMENT_CHANNELS[method] || PAYMENT_CHANNELS.bank_transfer;
    select.innerHTML = channels.map((channel) => `<option value="${channel.id}">${escapeHtml(channel.label)}</option>`).join('');
  }

  applyVoucher = function applyVoucherApi() {
    const code = document.getElementById('voucherInput').value.trim().toUpperCase();
    const message = document.getElementById('voucherMsg');
    message.style.display = 'block';

    if (!code) {
      orderState.voucherCode = '';
      message.style.color = 'var(--text2)';
      message.textContent = 'Voucher tidak digunakan.';
      updatePrice();
      return;
    }

    if (!['WELCOME10', 'UMKM50000'].includes(code)) {
      orderState.voucherCode = code;
      message.style.color = 'var(--amber)';
      message.textContent = 'Kode akan divalidasi oleh backend saat checkout.';
      updatePrice();
      return;
    }

    orderState.voucherCode = code;
    const quote = previewPrice();
    message.style.color = quote.discount ? 'var(--green)' : 'var(--accent)';
    message.textContent = quote.discount
      ? `✓ Voucher diterapkan. Hemat ${rupiah(quote.discount)}.`
      : 'Voucher belum memenuhi minimum transaksi.';
    updatePrice();
  };

  function orderPlatform(packageData) {
    const text = `${packageData?.category?.name || ''} ${packageData?.name || ''}`.toLowerCase();
    if (text.includes('video')) return { platform: 'TikTok / Instagram Reels', size: '9:16' };
    if (text.includes('foto')) return { platform: 'Instagram / Marketplace', size: '1:1' };
    if (text.includes('copy')) return { platform: 'Instagram', size: 'Caption' };
    return { platform: 'Instagram', size: 'Content Plan' };
  }

  submitOrder = async function submitOrderApi(button) {
    if (!apiUser || apiUser.role !== 'client') {
      alert('Silakan login menggunakan akun klien untuk membuat pesanan.');
      showLogin();
      return;
    }

    const packageData = selectedPackage();
    const businessName = document.getElementById('orderBusinessName')?.value.trim();
    const description = document.getElementById('orderProductDescription')?.value.trim();
    const visualReference = document.getElementById('orderVisualReference')?.value.trim();
    const channelId = Number(document.getElementById('paymentChannelSelect')?.value || 0);

    if (!packageData) return alert('Pilih paket terlebih dahulu.');
    if (!businessName) return alert('Nama bisnis wajib diisi.');
    if (!description) return alert('Deskripsi produk wajib diisi.');
    if (!orderState.bookingDate) return alert('Pilih tanggal pengerjaan terlebih dahulu.');
    if (!channelId) return alert('Pilih channel pembayaran terlebih dahulu.');

    setButtonBusy(button, true, 'Membuat Pesanan...');

    try {
      const platform = orderPlatform(packageData.raw);
      const formData = new FormData();
      formData.append('service_package_id', String(packageData.apiId));
      formData.append('title', `${packageData.name} — ${businessName}`);
      formData.append('business_name', businessName);
      formData.append('product_description', description);
      formData.append('target_audience', description);
      formData.append('visual_reference', visualReference || 'Menyesuaikan identitas brand');
      formData.append('brief', description);
      formData.append('platform', platform.platform);
      formData.append('content_size', platform.size);
      formData.append('quantity', '1');
      formData.append('speed_type', orderState.speedType || 'regular');
      formData.append('booking_date', orderState.bookingDate);
      formData.append('payment_channel_id', String(channelId));
      if (orderState.voucherCode) formData.append('voucher_code', orderState.voucherCode);
      selectedOrderFiles.forEach((file) => formData.append('assets[]', file));

      const response = await apiRequest('/orders', {
        method: 'POST',
        body: formData,
      });

      const payment = response.payment || response.data?.latest_payment;
      if (!payment?.id) throw new ApiError('Pesanan dibuat, tetapi data pembayaran tidak ditemukan.');

      pendingPayment = {
        id: payment.id,
        orderId: response.data.id,
        amount: payment.amount || response.data.total_amount,
        method: payment.channel?.name || 'Pembayaran',
      };

      document.getElementById('paymentModalMethod').textContent = `Menunggu pembayaran melalui ${pendingPayment.method}`;
      document.getElementById('paymentModalAmount').textContent = rupiah(pendingPayment.amount);
      document.getElementById('paymentModal').classList.remove('hidden');
    } catch (error) {
      showError(error);
    } finally {
      setButtonBusy(button, false);
    }
  };

  window.closePaymentModal = function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
  };

  window.confirmPaymentAction = async function confirmPaymentAction() {
    if (!pendingPayment) return;
    const button = document.querySelector('#paymentModal .btn-primary');
    setButtonBusy(button, true, 'Memverifikasi...');

    try {
      await apiRequest(`/payments/${pendingPayment.id}/verify`, { method: 'POST' });
      closePaymentModal();
      pendingPayment = null;
      selectedOrderFiles = [];
      renderSelectedFiles();
      document.getElementById('fileInput').value = '';
      document.getElementById('orderBusinessName').value = '';
      document.getElementById('orderProductDescription').value = '';
      document.getElementById('orderVisualReference').value = '';
      await loadClientOrders();
      alert('🎉 Pembayaran berhasil diverifikasi. Pesanan masuk ke Job Board freelancer.');
      switchTab(document.querySelector('#dashboard .tab'), 'dt1');
      document.getElementById('dashboard').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
      showError(error);
    } finally {
      setButtonBusy(button, false);
    }
  };

  renderUserDashboard = function renderUserDashboardApi() {
    const activeContainer = document.getElementById('dt1');
    const historyContainer = document.getElementById('dt2');
    const resultsContainer = document.getElementById('dt3');

    const activeOrders = orders.filter((order) => !['done', 'cancelled'].includes(order.status));
    const doneOrders = orders.filter((order) => order.status === 'done');
    const resultOrders = orders.filter((order) => order.submissions?.length || order.files?.length);

    if (activeContainer) {
      activeContainer.innerHTML = activeOrders.length
        ? activeOrders.map(renderClientOrderCard).join('')
        : '<div style="text-align:center;padding:40px;color:var(--text3)">Tidak ada pesanan aktif saat ini.</div>';
    }

    if (historyContainer) {
      historyContainer.innerHTML = doneOrders.length
        ? doneOrders.map((order) => `
          <div class="order-card">
            <div class="order-hdr">
              <div><div class="order-id-lbl">${escapeHtml(order.id)}</div><div class="order-card-title">${escapeHtml(order.title)}</div></div>
              <span class="status-pill sp-done">✓ Selesai</span>
            </div>
            <p style="font-size:14px;color:var(--text2);margin-bottom:14px">Selesai: ${escapeHtml(order.date)} · Total: ${rupiah(order.price)}</p>
            <button class="btn-xs bx-pri" onclick="switchTab(document.querySelectorAll('#dashboard .tab')[2],'dt3')">⬇ Lihat Hasil</button>
          </div>
        `).join('')
        : '<div style="text-align:center;padding:40px;color:var(--text3)">Belum ada riwayat pesanan selesai.</div>';
    }

    if (resultsContainer) {
      resultsContainer.innerHTML = resultOrders.length
        ? resultOrders.map(renderClientResults).join('')
        : '<div style="text-align:center;padding:40px;color:var(--text3)">Belum ada hasil konten dari freelancer.</div>';
    }
  };

  function statusPresentation(status) {
    const map = {
      pending_payment: ['⏳ Menunggu Pembayaran', 'sp-queue', 1],
      queue: ['⏳ Dalam Antrean', 'sp-queue', 2],
      process: ['⚡ Sedang Diproses', 'sp-process', 3],
      revision: ['✏️ Sedang Direvisi', 'sp-process', 3],
      revision_requested: ['📝 Revisi Menunggu Admin', 'sp-process', 4],
      review: ['👀 Menunggu Keputusan Anda', 'sp-process', 4],
      problem: ['⚠️ Bermasalah', 'sp-queue', 1],
      done: ['✓ Selesai', 'sp-done', 5],
    };
    return map[status] || [escapeHtml(status), 'sp-queue', 1];
  }

  function renderClientOrderCard(order) {
    const [statusText, statusClass, stepIndex] = statusPresentation(order.status);
    const canReview = order.status === 'review' && order.files.length > 0;
    const reviewFiles = canReview ? order.files.map((file) => `
      <a class="btn-xs bx-sec" href="${file.url}" target="_blank" rel="noopener">⬇ ${escapeHtml(file.name)}</a>
    `).join('') : '';

    return `
      <div class="order-card">
        <div class="order-hdr">
          <div><div class="order-id-lbl">${escapeHtml(order.id)}</div><div class="order-card-title">${escapeHtml(order.title)}</div></div>
          <span class="status-pill ${statusClass}">${statusText}</span>
        </div>
        <div class="progress-track">
          ${[1, 2, 3, 4, 5].map((step) => `
            <div class="prog-step ${stepIndex >= step ? 'done' : (stepIndex + 1 === step ? 'active' : '')}">
              <div class="prog-dot">${stepIndex > step ? '✓' : step}</div>
              <div class="prog-lbl">${['Pembayaran<br>Diterima', 'Dalam<br>Antrean', 'Sedang<br>Diproses', 'Review<br>Klien', 'Selesai &<br>Dikirim'][step - 1]}</div>
            </div>
          `).join('')}
        </div>
        ${canReview ? `<div style="display:flex;gap:8px;flex-wrap:wrap;margin:12px 0">${reviewFiles}</div>` : ''}
        ${order.activeRevision ? `<div class="revision-notice">Catatan revisi aktif: ${escapeHtml(order.activeRevision.notes || '')}</div>` : ''}
        <div class="order-footer">
          <span>Estimasi selesai: <strong style="color:var(--text)">${escapeHtml(order.date)}</strong></span>
          ${canReview ? `
            <div style="display:flex;gap:8px">
              <button class="btn-outline" style="padding:6px 12px;font-size:12px" onclick="userMintaRevisi(${order.apiId})" ${order.revisions <= 0 ? 'disabled' : ''}>Minta Revisi (${order.revisions}x)</button>
              <button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="userTerimaHasil(${order.apiId})">Terima Hasil</button>
            </div>
          ` : `<span style="color:var(--accent);font-weight:600">${rupiah(order.price)}</span>`}
        </div>
      </div>
    `;
  }

  function renderClientResults(order) {
    const submissions = [...(order.submissions || [])].sort((a, b) => Number(b.version) - Number(a.version));
    const submissionHtml = submissions.map((submission) => {
      const files = (submission.files || []).map((file) => `
        <div class="content-card">
          <div class="content-thumb">${fileIcon(file.mime_type, file.original_name)}</div>
          <div class="content-meta">
            <div class="content-type-lbl">Versi ${submission.version} · ${escapeHtml(submission.submission_type || 'draft')}</div>
            <div class="content-fname">${escapeHtml(file.original_name)}</div>
            <div class="content-btns"><a class="btn-xs bx-pri" href="${fileUrl(file.file_path)}" target="_blank" rel="noopener">⬇ Unduh</a></div>
          </div>
        </div>
      `).join('');

      return files;
    }).join('');

    return `
      <div class="order-card">
        <div class="order-hdr">
          <div><div class="order-id-lbl">Pesanan ${escapeHtml(order.id)}</div><div class="order-card-title">Riwayat Hasil Freelancer</div></div>
          <span class="status-pill ${order.status === 'done' ? 'sp-done' : 'sp-process'}">${order.status === 'done' ? '✓ Selesai' : 'Review Hasil'}</span>
        </div>
        <div class="content-grid">${submissionHtml || '<div style="color:var(--text3)">Belum ada file hasil.</div>'}</div>
        <div class="revision-notice">Sisa revisi: <strong>${order.revisions}x</strong> dari ${order.revisionLimit}x.</div>
        ${order.status === 'review' ? `
          <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
            <button class="btn-outline" onclick="userMintaRevisi(${order.apiId})" ${order.revisions <= 0 ? 'disabled' : ''}>Minta Revisi Lagi</button>
            <button class="btn-primary" onclick="userTerimaHasil(${order.apiId})">Terima Hasil</button>
          </div>
        ` : ''}
      </div>
    `;
  }

  userMintaRevisi = async function userMintaRevisiApi(orderId) {
    const order = orders.find((item) => item.apiId === Number(orderId));
    if (!order) return;
    if (order.revisions <= 0) return alert('Kuota revisi untuk pesanan ini sudah habis.');

    const notes = prompt('Tuliskan bagian yang perlu direvisi secara jelas:');
    if (!notes?.trim()) return;

    try {
      await apiRequest(`/client/orders/${order.apiId}/revision`, {
        method: 'POST',
        body: JSON.stringify({ notes: notes.trim() }),
      });
      await loadClientOrders();
      alert('Permintaan revisi berhasil dikirim dan menunggu admin meneruskannya ke freelancer.');
    } catch (error) {
      showError(error);
    }
  };

  requestRevisionPrompt = userMintaRevisi;

  userTerimaHasil = async function userTerimaHasilApi(orderId) {
    const order = orders.find((item) => item.apiId === Number(orderId));
    if (!order) return;
    if (!confirm('Terima hasil dan selesaikan pesanan ini?')) return;

    try {
      await apiRequest(`/client/orders/${order.apiId}/approve`, { method: 'POST' });
      await loadClientOrders();
      alert('🎉 Hasil diterima dan pesanan selesai.');
    } catch (error) {
      showError(error);
    }
  };

  renderFreelancerDashboard = function renderFreelancerDashboardApi() {
    const jobsContainer = document.getElementById('jobBoardList');
    const tasksContainer = document.getElementById('myTasksList');
    if (!jobsContainer || !tasksContainer) return;

    jobsContainer.innerHTML = freelancerJobs.length
      ? freelancerJobs.map((order) => `
        <div class="cms-row" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);padding:16px;margin-bottom:12px">
          <div>
            <div style="font-size:12px;color:var(--text3);margin-bottom:4px">Klien: ${escapeHtml(order.client)} · ${escapeHtml(order.id)}</div>
            <div class="cms-row-name">${escapeHtml(order.title)}</div>
            <div style="font-size:12px;color:var(--text2);margin-top:4px">Paket: ${escapeHtml(order.pkg)} · Deadline: ${escapeHtml(order.date)}</div>
            ${renderAssetLinks(order.assets)}
          </div>
          <div style="text-align:right">
            <div style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:8px">+ ${rupiah(order.raw.freelancer_earning)}</div>
            <button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="takeJob(${order.apiId})">Ambil Pekerjaan</button>
          </div>
        </div>
      `).join('')
      : '<div style="text-align:center;padding:40px;color:var(--text3)">Belum ada pekerjaan sesuai keahlianmu.</div>';

    const input = '<input type="file" id="freelancerUpload" style="display:none" accept=".jpg,.jpeg,.png,.pdf,.zip,.mp4,.mov,.doc,.docx" onchange="handleUploadWork(this)">';
    tasksContainer.innerHTML = input + (freelancerTasks.length
      ? freelancerTasks.map(renderFreelancerTask).join('')
      : '<div style="text-align:center;padding:40px;color:var(--text3)">Anda belum mengambil pekerjaan.</div>');
  };

  function renderAssetLinks(assets) {
    if (!assets?.length) return '<div style="font-size:11px;color:var(--text3);margin-top:8px">Klien tidak mengunggah aset.</div>';
    return `<div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px">${assets.map((asset) => `
      <a class="btn-outline" style="padding:4px 8px;font-size:11px" href="${fileUrl(asset.file_path)}" target="_blank" rel="noopener">📁 ${escapeHtml(asset.original_name)}</a>
    `).join('')}</div>`;
  }

  function renderFreelancerTask(order) {
    const revision = order.activeRevision;
    const canUpload = ['process', 'revision'].includes(order.status);
    const statusColor = order.status === 'revision' ? 'var(--accent)' : order.status === 'review' ? 'var(--green)' : 'var(--blue)';
    const statusText = order.status === 'revision'
      ? '🚨 REVISI DARI KLIEN'
      : order.status === 'review'
        ? 'MENUNGGU REVIEW KLIEN'
        : order.status === 'done'
          ? 'SELESAI'
          : 'SEDANG DIPROSES';

    return `
      <div class="cms-row" style="background:var(--surface);border:1px solid var(--border);border-left:4px solid ${statusColor};border-radius:var(--r-md);padding:16px;margin-bottom:12px">
        <div>
          <div style="font-size:12px;color:${statusColor};font-weight:bold;margin-bottom:4px">${statusText} · ${escapeHtml(order.id)}</div>
          <div class="cms-row-name">${escapeHtml(order.title)}</div>
          ${revision ? `<div class="revision-notice"><strong>Catatan revisi:</strong> ${escapeHtml(revision.notes || '')}${revision.admin_notes ? `<br><strong>Catatan admin:</strong> ${escapeHtml(revision.admin_notes)}` : ''}</div>` : ''}
          ${renderAssetLinks(order.assets)}
        </div>
        <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end">
          <div style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:8px">+ ${rupiah(order.raw.freelancer_earning)}</div>
          ${canUpload ? `<button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="uploadWork(${order.apiId})">${order.status === 'revision' ? 'Upload Hasil Revisi' : 'Upload Hasil'}</button>` : '<span style="font-size:12px;color:var(--text2)">File sudah dikirim.</span>'}
        </div>
      </div>
    `;
  }

  takeJob = async function takeJobApi(orderId) {
    if (!confirm('Ambil pekerjaan ini?')) return;
    try {
      await apiRequest(`/freelancer/jobs/${orderId}/take`, { method: 'POST' });
      await loadFreelancerData();
      switchFreelancerTab(document.querySelectorAll('#freelancer .tab')[1], 'ft2');
      alert('Pekerjaan berhasil diambil.');
    } catch (error) {
      showError(error);
    }
  };

  uploadWork = function uploadWorkApi(orderId) {
    currentUploadOrderId = Number(orderId);
    document.getElementById('freelancerUpload').click();
  };

  handleUploadWork = async function handleUploadWorkApi(input) {
    const file = input.files?.[0];
    if (!file || !currentUploadOrderId) return;
    const notes = prompt('Tambahkan catatan hasil untuk klien (opsional):') || '';
    const formData = new FormData();
    formData.append('result_file', file);
    if (notes.trim()) formData.append('notes', notes.trim());

    try {
      await apiRequest(`/freelancer/tasks/${currentUploadOrderId}/submit`, {
        method: 'POST',
        body: formData,
      });
      input.value = '';
      currentUploadOrderId = null;
      await loadFreelancerData();
      alert('Hasil berhasil dikirim dan sekarang dapat dilihat oleh klien.');
    } catch (error) {
      input.value = '';
      showError(error);
    }
  };

  renderAdminKanban = function renderAdminKanbanApi() {
    const columns = {
      queue: document.getElementById('kanbanQueue'),
      process: document.getElementById('kanbanProcess'),
      review: document.getElementById('kanbanReview'),
      done: document.getElementById('kanbanDone'),
    };
    if (!columns.queue) return;

    const groups = {
      queue: orders.filter((order) => order.status === 'queue'),
      process: orders.filter((order) => ['process', 'revision'].includes(order.status)),
      review: orders.filter((order) => ['review', 'revision_requested'].includes(order.status)),
      done: orders.filter((order) => order.status === 'done'),
    };

    document.getElementById('kanbanCountQueue').textContent = groups.queue.length;
    document.getElementById('kanbanCountProcess').textContent = groups.process.length;
    document.getElementById('kanbanCountReview').textContent = groups.review.length;
    document.getElementById('kanbanCountDone').textContent = groups.done.length;

    Object.entries(columns).forEach(([key, container]) => {
      container.innerHTML = groups[key].map((order) => `
        <div class="kitem">
          <div class="kitem-hdr"><span class="kitem-id">${escapeHtml(order.id.slice(-4))}</span><span class="kitem-pri kp-low">${escapeHtml(order.speed.split(' ')[0].toUpperCase())}</span></div>
          <div class="kitem-name">${escapeHtml(order.title)}</div>
          <div class="kitem-pkg">${escapeHtml(order.pkg)} · <strong style="color:var(--accent)">${rupiah(order.price)}</strong></div>
          <div style="font-size:11px;color:var(--text2);margin-top:8px">Freelancer: ${escapeHtml(order.freelancer?.name || 'Belum diambil')}</div>
          ${renderAssetLinks(order.assets)}
          <div class="kitem-footer" style="margin-top:10px"><span class="kitem-date">📅 ${escapeHtml(order.date)}</span><div class="kitem-av">${escapeHtml(order.initials)}</div></div>
        </div>
      `).join('') || '<div style="color:var(--text3);font-size:12px;padding:12px">Tidak ada pesanan.</div>';
    });
  };

  renderAdminVerification = function renderAdminVerificationApi() {
    const container = document.getElementById('verificationTableBody');
    if (!container) return;
    const paymentOrders = orders.filter((order) => ['pending_payment', 'problem'].includes(order.status));

    container.innerHTML = paymentOrders.length
      ? paymentOrders.map((order) => `
        <tr>
          <td><strong>${escapeHtml(order.id.slice(-4))}</strong></td>
          <td>${escapeHtml(order.client)}</td>
          <td>${escapeHtml(order.pkg)}</td>
          <td>${rupiah(order.price)}</td>
          <td>${escapeHtml(order.method)}</td>
          <td>${escapeHtml(order.paymentStatus)}</td>
          <td><span style="color:var(--text3);font-size:11px">Diproses melalui payment gateway</span></td>
        </tr>
      `).join('')
      : '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text3)">Tidak ada transaksi pending atau bermasalah.</td></tr>';
  };

  renderAdminCMSPackages = function renderAdminCMSPackagesApi() {
    const container = document.getElementById('cmsPackageRows');
    if (!container) return;
    container.innerHTML = packages.map((item) => `
      <div class="cms-row">
        <div>
          <div class="cms-row-name">${escapeHtml(item.name)}</div>
          <div class="cms-row-meta">${escapeHtml(item.desc)} · Revisi: ${escapeHtml(item.revision)} · ${item.isActive ? 'Aktif' : 'Nonaktif'}</div>
          <div style="font-size:11px;color:var(--text3);margin-top:3px">Slot per hari: ${item.totalSlot}</div>
        </div>
        <div style="display:flex;align-items:center;gap:16px">
          <div class="cms-price">${rupiah(item.price)}</div>
          <div style="display:flex;gap:6px">
            <button class="btn-edit" onclick="editPackagePrompt(${item.apiId})">Edit Harga</button>
            <button class="btn-delete" onclick="deletePackage(${item.apiId})">Hapus</button>
          </div>
        </div>
      </div>
    `).join('');
  };

  deletePackage = async function deletePackageApi(id) {
    if (!confirm('Hapus paket ini? Paket yang sudah dipakai pesanan dapat ditolak oleh database.')) return;
    try {
      const response = await apiRequest(`/packages/${id}`, { method: 'DELETE' });
      await loadPackages();
      alert(response.message || 'Paket berhasil diproses.');
    } catch (error) {
      showError(error);
    }
  };

  editPackagePrompt = async function editPackagePromptApi(id) {
    const item = packages.find((packageItem) => packageItem.apiId === Number(id));
    if (!item) return;
    const price = prompt(`Harga baru untuk ${item.name}:`, item.price);
    if (price === null) return;
    if (!/^\d+$/.test(price) || Number(price) < 0) return alert('Harga harus berupa angka bulat positif.');

    const raw = item.raw;
    const payload = {
      service_category_id: raw.service_category_id,
      code: raw.code,
      name: raw.name,
      slug: raw.slug,
      description: raw.description,
      includes: raw.includes || [],
      base_price: Number(price),
      regular_days: raw.regular_days,
      fast_days: raw.fast_days,
      express_days: raw.express_days,
      fast_fee_percent: Number(raw.fast_fee_percent),
      express_fee_percent: Number(raw.express_fee_percent),
      revision_limit: raw.revision_limit,
      total_slot: raw.total_slot,
      freelancer_fee_percent: Number(raw.freelancer_fee_percent),
      is_active: Boolean(raw.is_active),
    };

    try {
      await apiRequest(`/packages/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
      await loadPackages();
      alert('Harga paket berhasil diperbarui.');
    } catch (error) {
      showError(error);
    }
  };

  addNewPackagePrompt = async function addNewPackagePromptApi() {
    const categories = [...new Map(packages.map((item) => [item.raw.service_category_id, item.raw.category])).entries()];
    const categoryHelp = categories.map(([id, category]) => `${id}: ${category?.name || 'Kategori'}`).join('\n');
    const categoryId = Number(prompt(`Pilih ID kategori:\n${categoryHelp}`));
    if (!categoryId || !categories.some(([id]) => Number(id) === categoryId)) return;
    const name = prompt('Nama paket baru:');
    if (!name?.trim()) return;
    const price = Number(prompt('Harga paket (angka tanpa titik):'));
    if (!Number.isInteger(price) || price < 0) return alert('Harga tidak valid.');

    const code = `${String(Date.now()).slice(-8)}`;
    const slug = `${name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')}-${code}`;
    const payload = {
      service_category_id: categoryId,
      code,
      name: name.trim(),
      slug,
      description: 'Paket layanan baru Contify.',
      includes: ['Konsultasi kebutuhan', 'File hasil sesuai paket'],
      base_price: price,
      regular_days: 3,
      fast_days: 2,
      express_days: 1,
      fast_fee_percent: 30,
      express_fee_percent: 60,
      revision_limit: 1,
      total_slot: 5,
      freelancer_fee_percent: 80,
      is_active: true,
    };

    try {
      await apiRequest('/packages', { method: 'POST', body: JSON.stringify(payload) });
      await loadPackages();
      alert('Paket baru berhasil ditambahkan.');
    } catch (error) {
      showError(error);
    }
  };

  function injectRevisionAdminPanel() {
    const adminSection = document.getElementById('admin');
    if (!adminSection || document.getElementById('at5')) return;
    const tabBar = adminSection.querySelector('.tab-bar');
    if (!tabBar) return;

    const tab = document.createElement('div');
    tab.className = 'tab';
    tab.textContent = '✏️ Revisi';
    tab.onclick = () => switchAdminTab(tab, 'at5');
    tabBar.appendChild(tab);

    const panel = document.createElement('div');
    panel.id = 'at5';
    panel.style.display = 'none';
    panel.innerHTML = '<div id="adminRevisionList"></div>';
    tabBar.parentElement.appendChild(panel);
  }

  switchAdminTab = function switchAdminTabApi(element, id) {
    element.parentElement.querySelectorAll('.tab').forEach((tab) => tab.classList.remove('active'));
    element.classList.add('active');
    ['at1', 'at2', 'at3', 'at4', 'at5'].forEach((panelId) => {
      const panel = document.getElementById(panelId);
      if (panel) panel.style.display = panelId === id ? 'block' : 'none';
    });
    if (id === 'at5' && apiUser?.role === 'admin') renderAdminRevisions();
  };

  function renderAdminRevisions() {
    const container = document.getElementById('adminRevisionList');
    if (!container) return;
    container.innerHTML = `
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);padding:24px">
        <div class="subheading" style="font-size:18px;color:var(--text);margin-bottom:16px">Permintaan Revisi Klien</div>
        ${adminRevisions.length ? adminRevisions.map((revision) => `
          <div class="cms-row" style="align-items:flex-start">
            <div>
              <div class="cms-row-name">${escapeHtml(revision.order?.order_code || `Order ${revision.order_id}`)} · Revisi #${revision.revision_number}</div>
              <div class="cms-row-meta">Status: ${escapeHtml(revision.status)} · Klien: ${escapeHtml(revision.requester?.name || '-')}</div>
              <div class="revision-notice">${escapeHtml(revision.notes || '')}</div>
              ${revision.admin_notes ? `<div style="font-size:12px;color:var(--text2)">Catatan admin: ${escapeHtml(revision.admin_notes)}</div>` : ''}
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
              ${revision.status === 'pending_admin' ? `
                <button class="btn-edit" onclick="forwardAdminRevision(${revision.id})">Teruskan</button>
                <button class="btn-delete" onclick="rejectAdminRevision(${revision.id})">Tolak</button>
              ` : '<span style="font-size:12px;color:var(--text3)">Sudah diproses</span>'}
            </div>
          </div>
        `).join('') : '<div style="color:var(--text3);padding:24px;text-align:center">Belum ada permintaan revisi.</div>'}
      </div>
    `;
  }

  window.forwardAdminRevision = async function forwardAdminRevision(id) {
    const adminNotes = prompt('Tambahkan catatan untuk freelancer (opsional):') || '';
    try {
      await apiRequest(`/admin/revisions/${id}/forward`, {
        method: 'POST',
        body: JSON.stringify({ admin_notes: adminNotes.trim() || null }),
      });
      await loadAdminData();
      alert('Revisi berhasil diteruskan ke freelancer.');
    } catch (error) {
      showError(error);
    }
  };

  window.rejectAdminRevision = async function rejectAdminRevision(id) {
    const adminNotes = prompt('Alasan penolakan revisi:');
    if (!adminNotes?.trim()) return;
    try {
      await apiRequest(`/admin/revisions/${id}/reject`, {
        method: 'POST',
        body: JSON.stringify({ admin_notes: adminNotes.trim() }),
      });
      await loadAdminData();
      alert('Permintaan revisi ditolak.');
    } catch (error) {
      showError(error);
    }
  };

  renderAdminStats = function renderAdminStatsApi() {
    const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date());
    const todayOrders = orders.filter((order) => String(order.raw.created_at || '').startsWith(today));
    const pending = orders.filter((order) => ['pending_payment', 'problem'].includes(order.status));
    const production = orders.filter((order) => ['queue', 'process', 'revision', 'review', 'revision_requested'].includes(order.status));
    const done = orders.filter((order) => order.status === 'done');
    const revenue = orders.filter((order) => order.paymentStatus === 'paid').reduce((total, order) => total + order.price, 0);

    const setText = (id, value) => {
      const element = document.getElementById(id);
      if (element) element.textContent = value;
    };
    setText('statPesananCount', todayOrders.length || orders.length);
    setText('statVerifikasiCount', pending.length);
    setText('statProduksiCount', production.length);
    setText('statSelesaiCount', done.length);
    setText('statRevenueVal', revenue >= 1000000 ? `${(revenue / 1000000).toFixed(1).replace('.', ',')} Jt` : rupiah(revenue));
  };

  function updatePublicPricingCards() {
    const cards = document.querySelectorAll('#pricing .price-card');
    packages.filter((item) => item.isActive).slice(0, cards.length).forEach((item, index) => {
      const card = cards[index];
      const name = card.querySelector('.price-name');
      const desc = card.querySelector('.price-desc');
      const amount = card.querySelector('.price-amount');
      const period = card.querySelector('.price-period');
      const features = card.querySelector('.price-features');
      const button = card.querySelector('.price-btn');
      if (name) name.textContent = item.name;
      if (desc) desc.textContent = item.desc;
      if (amount) amount.textContent = rupiah(item.price);
      if (period) period.textContent = `${item.raw.regular_days} hari · revisi ${item.raw.revision_limit}x`;
      if (features) features.innerHTML = (item.raw.includes || []).map((feature) => `<li class="price-feat"><span class="feat-check">✓</span>${escapeHtml(feature)}</li>`).join('');
      if (button) button.onclick = () => handleOrderBtn(item.name);
    });
  }

  reorderPackage = function reorderPackageApi(packageName) {
    const item = packages.find((packageItem) => packageItem.name === packageName);
    if (!item) return;
    orderState.pkg = item.name;
    orderState.packageId = item.apiId;
    orderState.price = item.price;
    renderOrderPackages();
    updatePrice();
    goStep(1);
    document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
  };

  renderAllDynamicData = function renderAllDynamicDataApi() {
    renderOrderPackages();
    renderUserDashboard();
    renderAdminKanban();
    renderAdminVerification();
    renderAdminCMSPackages();
    renderAdminCMSVouchers();
    renderAdminCMSTeam();
    renderAdminStats();
    renderFreelancerDashboard();
    renderPendingFreelancers();
    renderKuotaBidang();
    renderAdminRevisions();
  };

  async function bootstrapApi() {
    injectRevisionAdminPanel();
    renderPaymentChannels();
    orderState.speedType = 'regular';
    orderState.paymentMethod = 'bank_transfer';

    if (!getToken()) return;

    try {
      const response = await apiRequest('/auth/me');
      apiUser = response.user;
      localStorage.setItem(USER_KEY, JSON.stringify(apiUser));
      updateLoggedUserNav();
      document.getElementById('loginOverlay').classList.add('hidden');
      await loadRoleData();
    } catch (error) {
      clearSession();
      document.getElementById('loginOverlay').classList.remove('hidden');
      console.warn('Sesi API tidak dapat dipulihkan.', error);
    }
  }

  bootstrapApi();
})();
