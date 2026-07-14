/* ===== DYNAMIC MOCK DATA ===== */
let packages = [
  { id: 'pkg-foto', name: 'Edit Foto', desc: 'Foto produk bersih, cerah, dan siap upload.', price: 45000, time: '1–3 hari', revision: '1x', totalSlot: 10, usedSlot: 7 },
  { id: 'pkg-video', name: 'Video TikTok/Reels', desc: 'Video viral siap posting dengan musik trending.', price: 150000, time: '2–3 hari', revision: '2x', totalSlot: 5, usedSlot: 5 },
  { id: 'pkg-copy', name: 'Copywriting', desc: 'Caption, story, dan teks iklan yang convert.', price: 35000, time: '1 hari', revision: '1x', totalSlot: 10, usedSlot: 4 },
  { id: 'pkg-strategi', name: 'Strategi Konten', desc: 'Content plan 1 bulan penuh dengan jadwal dan konsep.', price: 350000, time: '3–5 hari', revision: '2x', totalSlot: 5, usedSlot: 3 }
];

let orders = [
  // --- CLIENT-REVIEW (5 items) ---
  { id: '#CNT-20260604-100', client: 'Saya (UMKM)', pkg: 'Video TikTok/Reels', title: 'Video TikTok — Skincare Glow', price: 195000, status: 'client-review', date: 'Hari ini', method: 'QRIS', time: 'Baru saja', initials: 'UM', speed: 'Cepat (2 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-101', client: 'Rian Hidayat', pkg: 'Edit Foto', title: 'Edit Foto × 5 — Baju Anak', price: 225000, status: 'client-review', date: 'Hari ini', method: 'Transfer BCA', time: '1 Jam yang lalu', initials: 'RH', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260604-102', client: 'Dewi Kartika', pkg: 'Copywriting', title: 'Caption × 10 — Sepatu Kulit', price: 350000, status: 'client-review', date: 'Hari ini', method: 'GoPay', time: '3 Jam yang lalu', initials: 'DK', speed: 'Kilat (1 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-103', client: 'Fajar Nugraha', pkg: 'Strategi Konten', title: 'Content Plan — Klinik Kecantikan', price: 350000, status: 'client-review', date: 'Kemarin', method: 'QRIS', time: 'Kemarin', initials: 'FN', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260604-104', client: 'Gita Savitri', pkg: 'Video TikTok/Reels', title: 'Video Promosi — Cafe', price: 240000, status: 'client-review', date: 'Kemarin', method: 'Transfer Mandiri', time: 'Kemarin', initials: 'GS', speed: 'Kilat (1 hari)', revisions: 2, files: [] },

  // --- PENDING (5 items) ---
  { id: '#CNT-20260604-105', client: 'Saya (UMKM)', pkg: 'Edit Foto', title: 'Edit Foto × 3 — Hijab', price: 135000, status: 'pending', date: '4 Jun 2026', method: 'Transfer BCA', time: 'Hari ini', initials: 'UM', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260604-106', client: 'Budi Santoso', pkg: 'Video TikTok/Reels', title: 'Video Reels — Gadget', price: 195000, status: 'pending', date: '4 Jun 2026', method: 'QRIS', time: 'Hari ini', initials: 'BS', speed: 'Cepat (2 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-107', client: 'Citra Kirana', pkg: 'Copywriting', title: 'Artikel SEO — Travel', price: 200000, status: 'pending', date: '4 Jun 2026', method: 'GoPay', time: 'Hari ini', initials: 'CK', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260604-108', client: 'Deni Setiawan', pkg: 'Strategi Konten', title: 'Branding Kit — Resto', price: 350000, status: 'pending', date: '4 Jun 2026', method: 'Transfer Mandiri', time: 'Hari ini', initials: 'DS', speed: 'Reguler (3 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-109', client: 'Eko Patrio', pkg: 'Video TikTok/Reels', title: 'Video TikTok — Jajanan', price: 240000, status: 'pending', date: '4 Jun 2026', method: 'QRIS', time: 'Hari ini', initials: 'EP', speed: 'Kilat (1 hari)', revisions: 2, files: [] },

  // --- QUEUE (5 items) ---
  { id: '#CNT-20260603-091', client: 'Saya (UMKM)', pkg: 'Video TikTok/Reels', title: 'Video TikTok — Kopi Nusantara', price: 150000, status: 'queue', date: '3 Jun 2026', method: 'Transfer BCA', time: '3 Jun 2026', initials: 'UM', speed: 'Kilat (1 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-092', client: 'Farhan', pkg: 'Edit Foto', title: 'Edit Foto × 5 — Make Up', price: 225000, status: 'queue', date: '4 Jun 2026', method: 'QRIS', time: '4 Jun 2026', initials: 'FR', speed: 'Cepat (2 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260603-093', client: 'Hendra Wijaya', pkg: 'Strategi Konten', title: 'Strategi Konten — Bakso Aci', price: 350000, status: 'queue', date: '5 Jun 2026', method: 'GoPay', time: '3 Jun 2026', initials: 'HW', speed: 'Reguler (3 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260604-094', client: 'Iwan Fals', pkg: 'Copywriting', title: 'Landing Page Text — Gitar', price: 105000, status: 'queue', date: '4 Jun 2026', method: 'Transfer Mandiri', time: '4 Jun 2026', initials: 'IF', speed: 'Cepat (2 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260603-095', client: 'Joko Anwar', pkg: 'Video TikTok/Reels', title: 'Video Reels — Film Pendek', price: 240000, status: 'queue', date: '6 Jun 2026', method: 'QRIS', time: '3 Jun 2026', initials: 'JA', speed: 'Kilat (1 hari)', revisions: 2, files: [] },

  // --- PROCESS (5 items) ---
  { id: '#CNT-20260602-081', client: 'Saya (UMKM)', pkg: 'Video TikTok/Reels', title: 'Review Unboxing — Gadget', price: 240000, status: 'process', date: '3 Jun 2026', method: 'QRIS', time: '2 Jun 2026', initials: 'UM', speed: 'Kilat (1 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260602-082', client: 'Guntur Pradana', pkg: 'Edit Foto', title: 'Foto Catalog — Kebaya Modern', price: 450000, status: 'process', date: '5 Jun 2026', method: 'Transfer BCA', time: '2 Jun 2026', initials: 'GP', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260602-083', client: 'Lina Marlina', pkg: 'Copywriting', title: 'Caption IG × 15 — Butik', price: 450000, status: 'process', date: '5 Jun 2026', method: 'GoPay', time: '2 Jun 2026', initials: 'LM', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260602-084', client: 'Maman Abdurahman', pkg: 'Strategi Konten', title: 'Content Plan — Bola', price: 350000, status: 'process', date: '4 Jun 2026', method: 'Transfer Mandiri', time: '2 Jun 2026', initials: 'MA', speed: 'Reguler (3 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260602-085', client: 'Nia Ramadhani', pkg: 'Video TikTok/Reels', title: 'Video TikTok — Tas Branded', price: 195000, status: 'process', date: '3 Jun 2026', method: 'QRIS', time: '2 Jun 2026', initials: 'NR', speed: 'Cepat (2 hari)', revisions: 2, files: [] },

  // --- REVIEW (Internal Admin) (5 items) ---
  { id: '#CNT-20260601-071', client: 'Hasan Basri', pkg: 'Video TikTok/Reels', title: 'TikTok Ads — Parfum Cowok', price: 195000, status: 'review', date: '2 Jun 2026', method: 'QRIS', time: '1 Jun 2026', initials: 'HB', speed: 'Cepat (2 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260601-072', client: 'Irmawati', pkg: 'Copywriting', title: 'Caption × 10 — Hijab Kids', price: 350000, status: 'review', date: '3 Jun 2026', method: 'GoPay', time: '1 Jun 2026', initials: 'IR', speed: 'Reguler (3 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260601-073', client: 'Oka Antara', pkg: 'Edit Foto', title: 'Edit Foto × 5 — Menu Resto', price: 225000, status: 'review', date: '2 Jun 2026', method: 'Transfer BCA', time: '1 Jun 2026', initials: 'OA', speed: 'Cepat (2 hari)', revisions: 1, files: [] },
  { id: '#CNT-20260601-074', client: 'Pita', pkg: 'Strategi Konten', title: 'Branding Kit — Spa', price: 350000, status: 'review', date: '3 Jun 2026', method: 'Transfer Mandiri', time: '1 Jun 2026', initials: 'PT', speed: 'Reguler (3 hari)', revisions: 2, files: [] },
  { id: '#CNT-20260601-075', client: 'Qori', pkg: 'Video TikTok/Reels', title: 'Video Reels — Alat Musik', price: 240000, status: 'review', date: '2 Jun 2026', method: 'QRIS', time: '1 Jun 2026', initials: 'QR', speed: 'Kilat (1 hari)', revisions: 2, files: [] },

  // --- DONE (5 items) ---
  { id: '#CNT-20260531-060', client: 'Saya (UMKM)', pkg: 'Video TikTok/Reels', title: 'Video TikTok — Mukena Anak', price: 240000, status: 'done', date: '3 Jun 2026', method: 'QRIS', time: '31 Mei 2026', initials: 'UM', speed: 'Kilat (1 hari)', revisions: 2, files: [{ type: '🎬', label: 'Video TikTok', name: 'mukena_anak.mp4' }] },
  { id: '#CNT-20260531-061', client: 'Rossa', pkg: 'Edit Foto', title: 'Edit Foto × 10 — Sepatu', price: 450000, status: 'done', date: '3 Jun 2026', method: 'Transfer BCA', time: '31 Mei 2026', initials: 'RS', speed: 'Reguler (3 hari)', revisions: 1, files: [{ type: '🖼️', label: 'Hasil Edit', name: 'sepatu.zip' }] },
  { id: '#CNT-20260531-062', client: 'Sinta', pkg: 'Copywriting', title: 'Artikel SEO — Kecantikan', price: 200000, status: 'done', date: '2 Jun 2026', method: 'GoPay', time: '31 Mei 2026', initials: 'SN', speed: 'Reguler (3 hari)', revisions: 1, files: [{ type: '📄', label: 'Dokumen', name: 'artikel_seo.docx' }] },
  { id: '#CNT-20260531-063', client: 'Tono', pkg: 'Strategi Konten', title: 'Content Plan — Otomotif', price: 350000, status: 'done', date: '3 Jun 2026', method: 'Transfer Mandiri', time: '31 Mei 2026', initials: 'TN', speed: 'Reguler (3 hari)', revisions: 2, files: [{ type: '🗺️', label: 'Content Plan', name: 'otomotif.pdf' }] },
  { id: '#CNT-20260531-064', client: 'Umar', pkg: 'Video TikTok/Reels', title: 'Video Reels — Mainan', price: 150000, status: 'done', date: '2 Jun 2026', method: 'QRIS', time: '31 Mei 2026', initials: 'UM', speed: 'Kilat (1 hari)', revisions: 2, files: [{ type: '🎬', label: 'Video Reels', name: 'mainan.mp4' }] }
];

let vouchers = [
  { code: 'CONTIFY20', discount: 0.2, used: 42 },
  { code: 'UMKM10', discount: 0.1, used: 88 },
  { code: 'SATSET15', discount: 0.15, used: 15 }
];

let teamMembers = [
  { name: 'Sari Rahayu (Editor Foto)', skills: 'Retouching, BG Removal', done: 42, active: 2, status: 'Online' },
  { name: 'Nila Agustina (Videographer)', skills: 'TikTok/Reels Editing, Audio Sync', done: 56, active: 5, status: 'Online' },
  { name: 'Dewi Lestari (Copywriter)', skills: 'Caption, CTA, Hashtag Optimization', done: 78, active: 8, status: 'Sibuk' },
  { name: 'Fadjroel Rachman (Strategist)', skills: 'Content Plan, Riset Kompetitor', done: 30, active: 3, status: 'Online' }
];


let divisions = [
  {
    name: 'Fotografer / Editor Foto',
    total: 10,
    members: [
      { name: 'Sari Rahayu', status: 'Aktif (2 Pesanan)', color: 'var(--green)' },
      { name: 'Ivan Gunawan', status: 'Aktif (4 Pesanan)', color: 'var(--green)' },
      { name: 'Ruben Onsu', status: 'Aktif (1 Pesanan)', color: 'var(--green)' },
      { name: 'Darwis Triadi', status: 'Sibuk (9 Pesanan)', color: 'var(--amber)' },
      { name: 'Rio Motret', status: 'Aktif (3 Pesanan)', color: 'var(--green)' },
      { name: 'Diera Bachir', status: 'Aktif (2 Pesanan)', color: 'var(--green)' },
      { name: 'Mario Ardi', status: 'Senggang (0 Pesanan)', color: 'var(--green)' },
      { name: 'Winston Gomez', status: 'Aktif (5 Pesanan)', color: 'var(--green)' }
    ]
  },
  {
    name: 'Video Editor (TikTok/Reels)',
    total: 10,  // increase to 10 so it's not "Penuh" initially or maybe it was 5/5? I'll set total to 10 to allow it to grow without breaking UI
    members: [
      { name: 'Nila Agustina', status: 'Aktif (5 Pesanan)', color: 'var(--green)' },
      { name: 'Gilang Dirga', status: 'Aktif (3 Pesanan)', color: 'var(--green)' },
      { name: 'Rina Nose', status: 'Aktif (4 Pesanan)', color: 'var(--green)' },
      { name: 'Sule Sutisna', status: 'Aktif (2 Pesanan)', color: 'var(--green)' },
      { name: 'Andre Taulany', status: 'Sibuk (8 Pesanan)', color: 'var(--amber)' }
    ]
  },
  {
    name: 'Copywriter',
    total: 10,
    members: [
      { name: 'Dewi Lestari', status: 'Sibuk (8 Pesanan)', color: 'var(--amber)' },
      { name: 'Raditya Dika', status: 'Aktif (1 Pesanan)', color: 'var(--green)' },
      { name: 'Pidi Baiq', status: 'Aktif (2 Pesanan)', color: 'var(--green)' },
      { name: 'Tere Liye', status: 'Senggang (0 Pesanan)', color: 'var(--green)' }
    ]
  },
  {
    name: 'Content Strategist',
    total: 5,
    members: [
      { name: 'Fadjroel Rachman', status: 'Aktif (3 Pesanan)', color: 'var(--green)' },
      { name: 'Arief Muhammad', status: 'Sibuk (6 Pesanan)', color: 'var(--amber)' },
      { name: 'Jerome Polin', status: 'Aktif (1 Pesanan)', color: 'var(--green)' }
    ]
  }
];

function renderKuotaBidang() {
  const container = document.getElementById('kuotaBidangContainer');
  if (!container) return;

  container.innerHTML = divisions.map(d => {
    const isFull = d.members.length >= d.total;
    const quotaText = isFull ? `<span style="color:var(--accent);font-weight:bold">Kapasitas: ${d.members.length}/${d.total} Aktif (Penuh)</span>` : `Kapasitas: ${d.members.length}/${d.total} Aktif`;
    return `
      <div class="cms-row">
        <div>
          <div class="cms-row-name">${d.name}</div>
          <div style="font-size:12px;color:var(--text3);margin-top:4px">${quotaText}</div>
        </div>
        <button class="btn-outline" style="padding:6px 12px;font-size:12px" onclick="openAdminStatDetail('division', '${d.name}')">Lihat Detail</button>
      </div>
    `;
  }).join('');
}

let pendingFreelancers = [
  { name: 'Budi Santoso', role: 'Video Editor (TikTok/Reels)', exp: '2', porto: 'https://behance.net/budisantoso' },
  { name: 'Rina Putri', role: 'Copywriter', exp: '1', porto: 'https://drive.google.com' },
  { name: 'Ahmad Wijaya', role: 'Fotografer / Editor Foto', exp: '3', porto: 'https://instagram.com/ahmadfoto' },
  { name: 'Tirta Mandira', role: 'Content Strategist', exp: '4', porto: 'https://linkedin.com/in/tirtamandira' }
];

let currentRole = null;

/* ===== AUTH ===== */
function doLoginUnified() {
  const u = document.getElementById('loginUsername').value.trim();
  const p = document.getElementById('loginPassword').value;
  const errEl = document.getElementById('loginError');

  if (u === 'a' || p === 'a' || (u === 'user' && p === 'user123')) {
    errEl.style.display = 'none';
    currentRole = 'user';
    document.getElementById('loginOverlay').classList.add('hidden');
    updateNavForRole('user');
    window.scrollTo({ top: 0, behavior: 'instant' });
    return;
  }
  if (u === 'b' || p === 'b' || (u === 'admin' && p === 'admin123')) {
    errEl.style.display = 'none';
    currentRole = 'admin';
    document.getElementById('loginOverlay').classList.add('hidden');
    updateNavForRole('admin');
    window.scrollTo({ top: 0, behavior: 'instant' });
    return;
  }
  if (u === 'f' || p === 'f' || (u === 'freelancer' && p === 'freelancer123')) {
    errEl.style.display = 'none';
    currentRole = 'freelancer';
    document.getElementById('loginOverlay').classList.add('hidden');
    updateNavForRole('freelancer');
    window.scrollTo({ top: 0, behavior: 'instant' });
    return;
  }

  errEl.style.display = 'block';
}

function applyVisibilityForRole(role) {
  const sections = {
    hero: document.getElementById('hero'),
    marquee: document.querySelector('.marquee-section'),
    beforeAfter: document.getElementById('before-after'),
    pricing: document.getElementById('pricing'),
    testi: document.querySelector('.testi-section'),
    order: document.getElementById('order'),
    dashboard: document.getElementById('dashboard'),
    admin: document.getElementById('admin'),
    freelancer: document.getElementById('freelancer'),
    navHome: document.getElementById('navHomeLink'),
    navPricing: document.getElementById('navPricingLink'),
    navOrder: document.getElementById('navOrderLink'),
    navOrderBtn: document.getElementById('navOrderBtn')
  };

  if (role === 'admin') {
    if (sections.hero) sections.hero.classList.add('hidden-role-section');
    if (sections.marquee) sections.marquee.classList.add('hidden-role-section');
    if (sections.beforeAfter) sections.beforeAfter.classList.add('hidden-role-section');
    if (sections.pricing) sections.pricing.classList.add('hidden-role-section');
    if (sections.testi) sections.testi.classList.add('hidden-role-section');
    if (sections.order) sections.order.classList.add('hidden-role-section');
    if (sections.dashboard) sections.dashboard.classList.add('hidden-role-section');
    if (sections.freelancer) sections.freelancer.classList.add('hidden-role-section');
    if (sections.navHome) sections.navHome.style.display = 'none';
    if (sections.navPricing) sections.navPricing.style.display = 'none';
    if (sections.navOrder) sections.navOrder.style.display = 'none';
    if (sections.navOrderBtn) sections.navOrderBtn.style.display = 'none';
    if (sections.admin) sections.admin.classList.remove('hidden-role-section');
  } else if (role === 'freelancer') {
    if (sections.hero) sections.hero.classList.add('hidden-role-section');
    if (sections.marquee) sections.marquee.classList.add('hidden-role-section');
    if (sections.beforeAfter) sections.beforeAfter.classList.add('hidden-role-section');
    if (sections.pricing) sections.pricing.classList.add('hidden-role-section');
    if (sections.testi) sections.testi.classList.add('hidden-role-section');
    if (sections.order) sections.order.classList.add('hidden-role-section');
    if (sections.dashboard) sections.dashboard.classList.add('hidden-role-section');
    if (sections.admin) sections.admin.classList.add('hidden-role-section');
    if (sections.navHome) sections.navHome.style.display = 'none';
    if (sections.navPricing) sections.navPricing.style.display = 'none';
    if (sections.navOrder) sections.navOrder.style.display = 'none';
    if (sections.navOrderBtn) sections.navOrderBtn.style.display = 'none';
    if (sections.freelancer) sections.freelancer.classList.remove('hidden-role-section');
  } else if (role === 'user') {
    if (sections.hero) sections.hero.classList.remove('hidden-role-section');
    if (sections.marquee) sections.marquee.classList.remove('hidden-role-section');
    if (sections.beforeAfter) sections.beforeAfter.classList.remove('hidden-role-section');
    if (sections.pricing) sections.pricing.classList.remove('hidden-role-section');
    if (sections.testi) sections.testi.classList.remove('hidden-role-section');
    if (sections.order) sections.order.classList.remove('hidden-role-section');
    if (sections.dashboard) sections.dashboard.classList.remove('hidden-role-section');
    if (sections.navHome) sections.navHome.style.display = '';
    if (sections.navPricing) sections.navPricing.style.display = '';
    if (sections.navOrder) sections.navOrder.style.display = '';
    if (sections.navOrderBtn) sections.navOrderBtn.style.display = '';
    if (sections.admin) sections.admin.classList.add('hidden-role-section');
    if (sections.freelancer) sections.freelancer.classList.add('hidden-role-section');
  } else {
    if (sections.hero) sections.hero.classList.remove('hidden-role-section');
    if (sections.marquee) sections.marquee.classList.remove('hidden-role-section');
    if (sections.beforeAfter) sections.beforeAfter.classList.remove('hidden-role-section');
    if (sections.pricing) sections.pricing.classList.remove('hidden-role-section');
    if (sections.testi) sections.testi.classList.remove('hidden-role-section');
    if (sections.order) sections.order.classList.remove('hidden-role-section');
    if (sections.dashboard) sections.dashboard.classList.add('hidden-role-section');
    if (sections.admin) sections.admin.classList.add('hidden-role-section');
    if (sections.freelancer) sections.freelancer.classList.add('hidden-role-section');
    if (sections.navHome) sections.navHome.style.display = '';
    if (sections.navPricing) sections.navPricing.style.display = '';
    if (sections.navOrder) sections.navOrder.style.display = '';
    if (sections.navOrderBtn) sections.navOrderBtn.style.display = '';
  }
}

function updateNavForRole(role) {
  applyVisibilityForRole(role);
  const badge = document.getElementById('userBadge');
  const loginBtn = document.getElementById('navLoginBtn');
  const av = document.getElementById('userAvatar');
  const name = document.getElementById('userBadgeName');
  badge.style.display = 'flex';
  loginBtn.style.display = 'none';
  if (role === 'admin') {
    av.textContent = 'A';
    av.style.background = 'var(--accentbg)';
    name.textContent = 'Admin';
    document.getElementById('navAdminLink').style.display = 'block';
    document.getElementById('navFreelancerLink').style.display = 'none';
    document.getElementById('navUserLink').style.display = 'none';
    const adminLink = document.querySelector('.nav-links a[href="#admin"]');
    if (adminLink) {
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      adminLink.classList.add('active');
    }
  } else if (role === 'freelancer') {
    av.textContent = 'F';
    av.style.background = 'var(--greenbg)';
    av.style.color = 'var(--green)';
    name.textContent = 'Freelancer';
    document.getElementById('navAdminLink').style.display = 'none';
    document.getElementById('navFreelancerLink').style.display = 'block';
    document.getElementById('navUserLink').style.display = 'none';
    const flLink = document.querySelector('.nav-links a[href="#freelancer"]');
    if (flLink) {
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      flLink.classList.add('active');
    }
  } else {
    av.textContent = 'U';
    av.style.background = 'var(--bluebg)';
    av.style.color = 'var(--blue)';
    name.textContent = 'Dashboard';
    document.getElementById('navUserLink').style.display = 'block';
    document.getElementById('navFreelancerLink').style.display = 'none';
    document.getElementById('navAdminLink').style.display = 'none';
    const homeLink = document.querySelector('.nav-links a[href="#hero"]');
    if (homeLink) {
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      homeLink.classList.add('active');
    }
  }
  window.scrollTo({ top: 0, behavior: 'instant' });
}

function doLogout() {
  currentRole = null;
  document.getElementById('userBadge').style.display = 'none';
  document.getElementById('navLoginBtn').style.display = '';
  document.getElementById('navAdminLink').style.display = 'none';
  document.getElementById('navFreelancerLink').style.display = 'none';
  document.getElementById('navUserLink').style.display = 'none';
  document.getElementById('loginOverlay').classList.remove('hidden');
  document.getElementById('loginUsername').value = '';
  document.getElementById('loginPassword').value = '';
  applyVisibilityForRole(null);
  const homeLink = document.querySelector('.nav-links a[href="#hero"]');
  if (homeLink) {
    document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
    homeLink.classList.add('active');
  }
  window.scrollTo({ top: 0, behavior: 'instant' });
}

function switchFreelancerTab(el, id) {
  el.parentElement.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  ['ft1', 'ft2', 'ft3'].forEach(i => {
    const x = document.getElementById(i);
    if (x) x.style.display = i === id ? 'block' : 'none';
  });
}
function renderFreelancerDashboard() {
  const dt1 = document.getElementById('jobBoardList');
  const dt2 = document.getElementById('myTasksList');
  if (!dt1 || !dt2) return;

  // Tab 1: Job Board (Orders in queue)
  const qOrders = orders.filter(o => o.status === 'queue');
  if (qOrders.length === 0) {
    dt1.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">Belum ada pesanan baru.</div>`;
  } else {
    dt1.innerHTML = qOrders.map(o => {
      const isKilat = o.speed.includes('Kilat');
      const priLabel = isKilat ? 'Kilat' : 'Reguler';
      return `
        <div class="cms-row" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); padding:16px; margin-bottom:12px">
          <div>
            <div style="font-size:12px;color:var(--text3);margin-bottom:4px">Klien: ${o.client} • ID: ${o.id.slice(-4)} (${priLabel})</div>
            <div class="cms-row-name">${o.title}</div>
            <div style="font-size:12px;color:var(--text2);margin-top:4px">Paket: ${o.pkg}</div>
            <div style="margin-top:10px">
              <button class="btn-outline" style="padding:4px 8px;font-size:11px;border-radius:6px;border-color:var(--border2);color:var(--text2)" onclick="alert('Download aset.zip')">📁 Download Aset</button>
            </div>
          </div>
          <div style="text-align:right">
            <div style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:8px">+ Rp ${(o.price * 0.8).toLocaleString('id-ID')}</div>
            <button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="takeJob('${o.id}')">Ambil Pekerjaan</button>
          </div>
        </div>
      `;
    }).join('');
  }

  // Tab 2: My Tasks (process, review, client-review)
  const pOrders = orders.filter(o => ['process', 'review', 'client-review'].includes(o.status));
  if (pOrders.length === 0) {
    // Keep the file input
    dt2.innerHTML = `<input type="file" id="freelancerUpload" style="display:none" accept="image/*,video/*,.pdf,.zip" onchange="handleUploadWork(this)">
                     <div style="text-align:center; padding:40px; color:var(--text3)">Anda belum mengambil pekerjaan apapun.</div>`;
  } else {
    dt2.innerHTML = `<input type="file" id="freelancerUpload" style="display:none" accept="image/*,video/*,.pdf,.zip" onchange="handleUploadWork(this)">` + 
    pOrders.map(o => {
      let statusColor = "var(--blue)";
      let statusText = "STATUS: DIPROSES";
      let btnHtml = `<button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="uploadWork('${o.id}')">Upload Hasil</button>`;

      if (o.status === 'review' || o.status === 'client-review') {
        statusColor = "var(--green)";
        statusText = "STATUS: MENUNGGU REVIEW";
        btnHtml = `<span style="font-size:12px; font-style:italic; color:var(--text2);">Terkirim. Menunggu konfirmasi...</span>`;
      }
      
      if (o.revisionNotes) {
        statusColor = "var(--accent)";
        statusText = "🚨 STATUS: REVISI DARI KLIEN";
        btnHtml = `<button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="uploadWork('${o.id}')">Upload Hasil Revisi</button>`;
      }

      return `
        <div class="cms-row" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); padding:16px; margin-bottom:12px; border-left:4px solid ${statusColor}">
          <div>
            <div style="font-size:12px;color:${statusColor};font-weight:bold;margin-bottom:4px">${statusText} • ID: ${o.id.slice(-4)}</div>
            <div class="cms-row-name">${o.title}</div>
            
            ${o.revisionNotes ? `
              <div style="background:rgba(239, 68, 68, 0.1); padding:10px; border-radius:6px; margin-top:12px; font-size:12px; border:1px solid rgba(239, 68, 68, 0.3);">
                <strong style="color:var(--accent)">Catatan Revisi Klien:</strong><br><i>"${o.revisionNotes}"</i>
              </div>
            ` : ''}

            <div style="margin-top:10px">
              <button class="btn-outline" style="padding:4px 8px;font-size:11px;border-radius:6px;border-color:var(--border2);color:var(--text2)" onclick="alert('Download aset.zip')">📁 Download Aset</button>
            </div>
          </div>
          <div style="text-align:right; display:flex; flex-direction:column; align-items:flex-end">
            <div style="font-size:16px;font-weight:700;color:var(--green);margin-bottom:8px">+ Rp ${(o.price * 0.8).toLocaleString('id-ID')}</div>
            ${btnHtml}
          </div>
        </div>
      `;
    }).join('');
  }
}

function takeJob(orderId) {
  if (confirm("Apakah Anda yakin ingin mengambil pekerjaan ini?")) {
    const order = orders.find(o => o.id === orderId);
    if (order) {
      order.status = 'process';
      alert('✓ Pekerjaan berhasil diambil! Silakan cek tab "Pekerjaan Saya".');
      renderAllDynamicData();
      switchFreelancerTab(document.querySelectorAll('#freelancer .tab')[1], 'ft2');
    }
  }
}

let currentUploadOrderId = null;
function uploadWork(orderId) {
  currentUploadOrderId = orderId;
  document.getElementById('freelancerUpload').click();
}

function handleUploadWork(input) {
  if (input.files && input.files[0]) {
    alert("File " + input.files[0].name + " berhasil diunggah dan dikirim ke Klien!");
    const order = orders.find(o => o.id === currentUploadOrderId);
    if (order) {
      order.status = 'client-review';
      order.revisionNotes = ''; // Bersihkan catatan revisi sebelumnya jika ada
      renderAllDynamicData();
    }
  }
}

function withdrawFunds(btn) {
  if (confirm("Tarik semua saldo sebesar Rp 450.000 ke rekening Anda?")) {
    document.getElementById('freelancerBalance').innerText = "Rp 0";
    btn.innerText = "Dana Sedang Diproses";
    btn.disabled = true;
    btn.style.opacity = "0.5";
    btn.style.cursor = "not-allowed";
    alert("Permintaan penarikan dana berhasil diajukan. Saldo Anda akan segera ditransfer!");
  }
}

function showLogin() {
  document.getElementById('loginOverlay').classList.remove('hidden');
  return false;
}

/* ===== LOGO UPLOAD ===== */
function uploadLogo(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const src = e.target.result;
    ['navLogoImg', 'loginLogoImg', 'footerLogoImg'].forEach(id => {
      const img = document.getElementById(id);
      if (img) { img.src = src; img.style.display = 'block'; }
    });
    ['navLogoPh', 'loginLogoTxt', 'footerLogoPh'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });
  };
  reader.readAsDataURL(file);
}

/* ===== DARK / LIGHT MODE ===== */
function toggleTheme() {
  const html = document.documentElement;
  const btn = document.getElementById('themeToggle');
  if (html.getAttribute('data-theme') === 'dark') {
    html.setAttribute('data-theme', 'light');
    btn.textContent = '☀️';
  } else {
    html.setAttribute('data-theme', 'dark');
    btn.textContent = '🌙';
  }
}

/* ===== BEFORE/AFTER SLIDER ===== */
const baSlider = document.getElementById('baSlider'), baBefore = document.getElementById('baBefore'), baDiv = document.getElementById('baDivider'), baBtn = document.getElementById('baHandle');
let baDrag = false;
function baSet(x) { const r = baSlider.getBoundingClientRect(); const p = Math.max(5, Math.min(95, (x - r.left) / r.width * 100)); baBefore.style.clipPath = `inset(0 ${100 - p}% 0 0)`; baDiv.style.left = baBtn.style.left = p + '%'; }
baSlider.addEventListener('mousedown', e => { baDrag = true; baSet(e.clientX) });
baSlider.addEventListener('touchstart', e => { baDrag = true; baSet(e.touches[0].clientX) }, { passive: true });
document.addEventListener('mousemove', e => { if (baDrag) baSet(e.clientX) });
document.addEventListener('touchmove', e => { if (baDrag) baSet(e.touches[0].clientX) }, { passive: true });
document.addEventListener('mouseup', () => baDrag = false);
document.addEventListener('touchend', () => baDrag = false);

/* ===== ORDER FORM ===== */
let orderState = { pkg: 'Edit Foto', price: 45000, multi: 1, speedLbl: 'Reguler (3 hari)', discount: 0 };
function selectPkg(el, price, name) {
  document.querySelectorAll('.pkg-card').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  orderState.pkg = name;
  orderState.price = price;
  updatePrice();
  document.getElementById('sumPkg').textContent = name;
}
function setDeadline(el, multi, lbl, days) {
  document.querySelectorAll('.deadline-opt').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  orderState.multi = multi;
  orderState.speedLbl = lbl + ' (' + days + ' hari)';
  updatePrice();
  document.getElementById('sumSpeed').textContent = orderState.speedLbl;
}
function updatePrice() {
  const raw = Math.round(orderState.price * orderState.multi);
  const final = Math.round(raw * (1 - orderState.discount));
  const el = document.getElementById('priceDisplay');
  el.textContent = 'Rp ' + final.toLocaleString('id-ID');
  el.classList.add('pop');
  setTimeout(() => el.classList.remove('pop'), 350);
  document.getElementById('sumTotal').textContent = 'Rp ' + final.toLocaleString('id-ID');
}
function goStep(n) {
  document.querySelectorAll('.form-step').forEach((s, i) => { s.classList.remove('active', 'completed'); if (i + 1 < n) s.classList.add('completed'); if (i + 1 === n) s.classList.add('active'); });
  document.querySelectorAll('.form-panel').forEach((p, i) => p.classList.toggle('active', i + 1 === n));
  document.getElementById('order').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function handleDrop(e) { e.preventDefault(); document.getElementById('uploadZone').classList.remove('drag'); handleFiles(e.dataTransfer.files); }
function handleFiles(files) { const list = document.getElementById('fileList');[...files].forEach(f => { const p = document.createElement('div'); p.className = 'file-pill'; p.innerHTML = `📎 ${f.name} <span class="remove" onclick="this.parentElement.remove()">×</span>`; list.appendChild(p); }); }

/* ===== CALENDAR ===== */
let calY = new Date().getFullYear(), calM = new Date().getMonth();
const calFullDays = [3, 7, 12, 17, 20, 24];
const calMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const calDayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
function renderCal() {
  document.getElementById('calLabel').textContent = calMonths[calM] + ' ' + calY;
  const grid = document.getElementById('calGrid');
  while (grid.children.length > 7) grid.removeChild(grid.lastChild);
  const first = new Date(calY, calM, 1).getDay();
  const total = new Date(calY, calM + 1, 0).getDate();
  const now = new Date();
  for (let i = 0; i < first; i++) {
    const d = document.createElement('div');
    d.className = 'cal-cell cal-empty';
    grid.appendChild(d);
  }
  for (let d = 1; d <= total; d++) {
    const el = document.createElement('div');
    const isToday = calY === now.getFullYear() && calM === now.getMonth() && d === now.getDate();
    const isPast = new Date(calY, calM, d) < new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const isFull = calFullDays.includes(d);
    el.className = 'cal-cell' + (isToday ? ' cal-today' : '') + (isPast ? ' cal-disabled' : '') + (isFull ? ' cal-full' : '');
    el.textContent = d;
    if (!isPast && !isFull) el.onclick = () => calSelect(el, d);
    grid.appendChild(el);
  }
}
function calSelect(el, d) {
  document.querySelectorAll('.cal-cell').forEach(e => e.classList.remove('cal-selected'));
  el.classList.add('cal-selected');
  const dn = calDayNames[new Date(calY, calM, d).getDay()];
  const txt = `${dn}, ${d} ${calMonths[calM]} ${calY}`;
  document.getElementById('selectedDateTxt').textContent = txt;
  document.getElementById('selectedDateBox').style.display = 'block';
  document.getElementById('sumDate').textContent = txt;
}
function calChangeMonth(dir) { calM += dir; if (calM > 11) { calM = 0; calY++; } if (calM < 0) { calM = 11; calY--; } renderCal(); }
renderCal();

/* ===== VOUCHER ===== */
function applyVoucher() {
  const code = document.getElementById('voucherInput').value.trim().toUpperCase();
  const msg = document.getElementById('voucherMsg');
  msg.style.display = 'block';
  const found = vouchers.find(v => v.code === code);
  if (found) {
    orderState.discount = found.discount;
    const raw = Math.round(orderState.price * orderState.multi);
    const disc = Math.round(raw * found.discount);
    msg.style.color = 'var(--green)';
    msg.textContent = `✓ Voucher aktif! Hemat ${found.discount * 100}% (−Rp ${disc.toLocaleString('id-ID')})`;
    document.getElementById('sumDiscount').textContent = '−Rp ' + disc.toLocaleString('id-ID');
    updatePrice();
  } else {
    orderState.discount = 0;
    msg.style.color = 'var(--accent)';
    msg.textContent = '✗ Kode tidak valid. Coba: CONTIFY20, UMKM10, atau SATSET15';
  }
}
function selectPay(el) { el.closest('.pay-method-grid').querySelectorAll('.pay-method').forEach(e => e.classList.remove('selected')); el.classList.add('selected'); }

function submitOrder(clickedBtn) {
  const nameInput = document.querySelector('#fp2 input').value || 'Mie Ayam Bu Sari';
  const btn = clickedBtn || document.querySelector('#fp4 .btn-primary') || document.body;
  
  // Prevent replacing body text if somehow button is not found
  if (btn === document.body) {
    alert("Tombol tidak ditemukan!");
    return;
  }
  
  const originalText = btn.textContent;
  btn.textContent = 'Memproses Pembayaran...';
  btn.style.opacity = '0.7';
  btn.style.pointerEvents = 'none';

  setTimeout(() => {
    const newOrder = {
      id: '#CNT-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(100 + Math.random() * 900),
      client: 'Saya (UMKM)',
      pkg: orderState.pkg,
      title: orderState.pkg + ' — Produk ' + nameInput,
      price: Math.round(orderState.price * orderState.multi * (1 - orderState.discount)),
      status: 'queue', // <-- Langsung masuk antrean
      date: document.getElementById('selectedDateTxt').textContent || '3 Jun 2026',
      method: document.querySelector('.pay-method-grid .pay-method.selected .pay-method-name').textContent,
      time: 'Hari ini',
      initials: 'UM',
      speed: orderState.speedLbl,
      revisions: 2,
      files: []
    };
    orders.push(newOrder);
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();

    alert('🎉 Pembayaran Berhasil!\n\nSistem payment gateway telah memverifikasi pembayaran Anda secara otomatis.\nPesanan Anda kini langsung masuk ke dalam Antrean Pengerjaan.');

    btn.textContent = originalText;
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';

    goStep(1);
    switchTab(document.querySelector('.tab-bar .tab'), 'dt1');
    const dashLink = document.querySelector('.nav-links a[href="#dashboard"]');
    if (dashLink) {
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      dashLink.classList.add('active');
      switchRole('user');
    }
    document.getElementById('dashboard').scrollIntoView({ behavior: 'smooth' });
  }, 2000); // Simulasi delay payment gateway 2 detik
}

/* ===== TABS ===== */
function switchTab(el, id) { el.parentElement.querySelectorAll('.tab').forEach(t => t.classList.remove('active')); el.classList.add('active');['dt1', 'dt2', 'dt3'].forEach(i => { const x = document.getElementById(i); if (x) x.style.display = i === id ? 'block' : 'none'; }); }
function switchAdminTab(el, id) { el.parentElement.querySelectorAll('.tab').forEach(t => t.classList.remove('active')); el.classList.add('active');['at1', 'at2', 'at3', 'at4'].forEach(i => { const x = document.getElementById(i); if (x) x.style.display = i === id ? 'block' : 'none'; }); }
function setCmsActive(el, panelId) { el.parentElement.querySelectorAll('.cms-nav-item').forEach(e => e.classList.remove('active')); el.classList.add('active'); document.querySelectorAll('.cms-panel').forEach(p => p.style.display = 'none'); const activePanel = document.getElementById(panelId); if (activePanel) activePanel.style.display = 'block'; };
function toggleMobile() { }

/* ===== NAV SCROLL & ACTIVE LINK ===== */
window.addEventListener('scroll', () => {
  const nav = document.getElementById('mainNav');
  nav.style.background = window.scrollY > 40 ? 'var(--nav-bg-scroll)' : 'var(--nav-bg)';

  // Track active section based on scroll position
  const sections = Array.from(document.querySelectorAll('section[id]'))
    .filter(sec => !sec.classList.contains('hidden-role-section'));

  let currentSectionId = '';
  for (const sec of sections) {
    const rect = sec.getBoundingClientRect();
    // A section is considered active if its top is above 35% of the viewport height
    if (rect.top <= window.innerHeight * 0.35) {
      currentSectionId = sec.getAttribute('id');
    }
  }

  // Handle the bottom of the page edge case (e.g. dashboard/admin at bottom)
  if ((window.innerHeight + window.scrollY) >= document.documentElement.scrollHeight - 20) {
    const visibleLinks = Array.from(document.querySelectorAll('.nav-links li'))
      .filter(li => li.style.display !== 'none')
      .map(li => li.querySelector('a'))
      .filter(Boolean);
    if (visibleLinks.length > 0) {
      currentSectionId = visibleLinks[visibleLinks.length - 1].getAttribute('href').substring(1);
    }
  }

  if (currentSectionId) {
    const navLink = document.querySelector(`.nav-links a[href="#${currentSectionId}"]`);
    if (navLink && !navLink.classList.contains('active')) {
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      navLink.classList.add('active');
    }
  }
});

// Track active navbar link on click (immediate update)
document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('click', () => {
    document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
    link.classList.add('active');
  });
});


/* ===== INTERSECTION OBSERVER ===== */
const observer = new IntersectionObserver(entries => { entries.forEach(e => { if (e.isIntersecting) e.target.style.animationPlayState = 'running'; }); }, { threshold: .1 });
document.querySelectorAll('.anim').forEach(el => { el.style.animationPlayState = 'paused'; observer.observe(el); });

/* ===== DYNAMIC FRONTEND RENDERING ===== */
function renderOrderPackages() {
  const container = document.getElementById('orderPkgGrid');
  if (!container) return;
  container.innerHTML = packages.map((p, index) => {
    const isSelected = orderState.pkg === p.name;
    return `
      <div class="pkg-card ${isSelected ? 'selected' : ''}" onclick="selectPkg(this, ${p.price}, '${p.name}')">
        <div class="pkg-icon">${p.id === 'pkg-foto' ? '📷' : p.id === 'pkg-video' ? '🎬' : p.id === 'pkg-copy' ? '✍️' : '🗺️'}</div>
        <div class="pkg-name">${p.name}</div>
        <div class="pkg-price">Rp ${p.price.toLocaleString('id-ID')}</div>
      </div>
    `;
  }).join('');
}

function renderUserDashboard() {
  const dt1 = document.getElementById('dt1');
  const dt2 = document.getElementById('dt2');
  const dt3 = document.getElementById('dt3');

  if (dt1) {
    const activeOrders = orders.filter(o => o.status !== 'done' && o.client === 'Saya (UMKM)');
    if (activeOrders.length === 0) {
      dt1.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">Tidak ada pesanan aktif saat ini.</div>`;
    } else {
      dt1.innerHTML = activeOrders.map(o => {
        let statusText = '';
        let statusClass = '';
        let stepIndex = 1;

        if (o.status === 'pending') {
          statusText = '⏳ Menunggu Verifikasi';
          statusClass = 'sp-queue';
          stepIndex = 1;
        } else if (o.status === 'queue') {
          statusText = '⏳ Dalam Antrean';
          statusClass = 'sp-queue';
          stepIndex = 2;
        } else if (o.status === 'process') {
          statusText = '⚡ Sedang Diproses';
          statusClass = 'sp-process';
          stepIndex = 3;
        } else if (o.status === 'review') {
          statusText = '🔍 Review Internal';
          statusClass = 'sp-process';
          stepIndex = 4;
        } else if (o.status === 'client-review') {
          statusText = '👀 Menunggu Keputusan Anda';
          statusClass = 'sp-process';
          stepIndex = 4;
        }

        return `
          <div class="order-card">
            <div class="order-hdr">
              <div>
                <div class="order-id-lbl">${o.id}</div>
                <div class="order-card-title">${o.title}</div>
              </div>
              <span class="status-pill ${statusClass}">${statusText}</span>
            </div>
            <div class="progress-track">
              <div class="prog-step ${stepIndex >= 1 ? 'done' : ''}"><div class="prog-dot">${stepIndex > 1 ? '✓' : '1'}</div><div class="prog-lbl">Pembayaran<br>Diterima</div></div>
              <div class="prog-step ${stepIndex >= 2 ? 'done' : (stepIndex === 1 ? 'active' : '')}"><div class="prog-dot">${stepIndex > 2 ? '✓' : '2'}</div><div class="prog-lbl">Dalam<br>Antrean</div></div>
              <div class="prog-step ${stepIndex >= 3 ? 'done' : (stepIndex === 2 ? 'active' : '')}"><div class="prog-dot">${stepIndex > 3 ? '✓' : '3'}</div><div class="prog-lbl">Sedang<br>Diproses</div></div>
              <div class="prog-step ${stepIndex >= 4 ? 'done' : (stepIndex === 3 ? 'active' : '')}"><div class="prog-dot">${stepIndex > 4 ? '✓' : '4'}</div><div class="prog-lbl">Review<br>Internal</div></div>
              <div class="prog-step ${stepIndex === 5 ? 'done' : (stepIndex === 4 ? 'active' : '')}"><div class="prog-dot">5</div><div class="prog-lbl">Selesai &<br>Dikirim</div></div>
            </div>
            <div class="order-footer">
              <span>Estimasi selesai: <strong style="color:var(--text)">${o.date}</strong></span>
              ${o.status === 'client-review' ? `
                <div style="display:flex;gap:8px">
                  <button class="btn-outline" style="padding:6px 12px;font-size:12px;color:var(--text)" onclick="userMintaRevisi('${o.id}')">Minta Revisi</button>
                  <button class="btn-primary" style="padding:6px 12px;font-size:12px" onclick="if(confirm('Terima hasil dan selesaikan pesanan ini?')) { userTerimaHasil('${o.id}'); }">Terima Hasil</button>
                </div>
              ` : `<span style="color:var(--accent);font-weight:600">Rp ${o.price.toLocaleString('id-ID')}</span>`}
            </div>
          </div>
        `;
      }).join('');
    }
  }

  if (dt2) {
    const doneOrders = orders.filter(o => o.status === 'done');
    if (doneOrders.length === 0) {
      dt2.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">Belum ada riwayat pesanan selesai.</div>`;
    } else {
      dt2.innerHTML = doneOrders.map(o => `
        <div class="order-card">
          <div class="order-hdr">
            <div>
              <div class="order-id-lbl">${o.id}</div>
              <div class="order-card-title">${o.title}</div>
            </div>
            <span class="status-pill sp-done">✓ Selesai</span>
          </div>
          <p style="font-size:14px;color:var(--text2);margin-bottom:14px">Selesai: ${o.date} · Total: Rp ${o.price.toLocaleString('id-ID')}</p>
          <div style="display:flex;gap:10px">
            <button class="btn-xs bx-pri" onclick="switchTab(document.querySelectorAll('.tab-bar .tab')[2],'dt3')">⬇ Unduh Hasil</button>
            <button class="btn-xs bx-sec" onclick="reorderPackage('${o.pkg}', ${o.price})">⟳ Pesan Ulang</button>
          </div>
        </div>
      `).join('');
    }
  }

  if (dt3) {
    const doneOrders = orders.filter(o => o.status === 'done');
    if (doneOrders.length === 0) {
      dt3.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text3)">Belum ada hasil konten yang siap diunduh.</div>`;
    } else {
      dt3.innerHTML = doneOrders.map(o => {
        const filesHTML = o.files.map(f => `
          <div class="content-card">
            <div class="content-thumb">${f.type}</div>
            <div class="content-meta">
              <div class="content-type-lbl">${f.label}</div>
              <div class="content-fname">${f.name}</div>
              <div class="content-btns">
                <button class="btn-xs bx-pri" onclick="alert('Mengunduh ${f.name}...')">⬇ Unduh</button>
                <button class="btn-xs bx-sec" onclick="requestRevisionPrompt('${o.id}')">✏ Revisi</button>
              </div>
            </div>
          </div>
        `).join('');

        return `
          <div class="order-card">
            <div class="order-hdr">
              <div>
                <div class="order-id-lbl">Pesanan ${o.id}</div>
                <div class="order-card-title">Hasil Konten Siap Diunduh</div>
              </div>
              <span class="status-pill sp-done">✓ Selesai</span>
            </div>
            <div class="content-grid">
              ${filesHTML || '<div style="color:var(--text3);font-size:13px;grid-column:1/-1">Mengunggah file ke cloud...</div>'}
            </div>
            <div class="revision-notice">⚠️ Sisa revisi: <strong>${o.revisions}x</strong> · Ajukan sebelum 10 Juni 2026</div>
            <div class="order-footer" style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;width:100%">
              <span>Total Pembayaran: <strong style="color:var(--text)">Rp ${o.price.toLocaleString('id-ID')}</strong> (Lunas via ${o.method})</span>
              <span style="color:var(--green);font-weight:600">✓ Pembayaran Terverifikasi</span>
            </div>
          </div>
        `;
      }).join('');
    }
  }
}

function advanceOrderStatus(orderId, nextStatus) {
  const order = orders.find(o => o.id === orderId);
  if (order) {
    order.status = nextStatus;
    if (nextStatus === 'done') {
      order.files = [
        { type: '🎬', label: 'Video TikTok', name: 'kopi_final.mp4' },
        { type: '📷', label: 'Foto Produk', name: 'kopi_thumb.jpg' },
        { type: '✍️', label: 'Caption', name: 'caption.txt' }
      ];
    }
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function renderAdminKanban() {
  const colQueue = document.getElementById('kanbanQueue');
  const colProcess = document.getElementById('kanbanProcess');
  const colReview = document.getElementById('kanbanReview');
  const colDone = document.getElementById('kanbanDone');

  if (!colQueue) return;

  const qOrders = orders.filter(o => o.status === 'queue');
  const pOrders = orders.filter(o => o.status === 'process');
  const rOrders = orders.filter(o => o.status === 'review');
  const dOrders = orders.filter(o => o.status === 'done');

  document.getElementById('kanbanCountQueue').textContent = qOrders.length;
  document.getElementById('kanbanCountProcess').textContent = pOrders.length;
  document.getElementById('kanbanCountReview').textContent = rOrders.length;
  document.getElementById('kanbanCountDone').textContent = dOrders.length;

  const getCardHTML = (o, nextStatus, nextLabel) => {
    const isKilat = o.speed.includes('Kilat');
    const isCepat = o.speed.includes('Cepat');
    const priClass = isKilat ? 'kp-hot' : (isCepat ? 'kp-med' : 'kp-low');
    const priLabel = isKilat ? 'KILAT' : (isCepat ? 'CEPAT' : 'REGULER');

    return `
      <div class="kitem">
        <div class="kitem-hdr">
          <span class="kitem-id">${o.id.slice(-4)}</span>
          <span class="kitem-pri ${priClass}">${priLabel}</span>
        </div>
        <div class="kitem-name">${o.title}</div>
        
        ${o.revisionNotes ? `
          <div style="background:rgba(239, 68, 68, 0.1); border-left:3px solid var(--accent); padding:8px; border-radius:4px; margin-top:8px; font-size:11px; color:var(--text)">
            <strong style="color:var(--accent)">🚨 REVISI KLIEN:</strong><br/>
            <i>"${o.revisionNotes}"</i>
          </div>
        ` : ''}

        <div class="kitem-pkg">${o.pkg} · <strong style="color:var(--accent)">Rp ${o.price.toLocaleString('id-ID')}</strong></div>
        <div style="margin-top:8px;margin-bottom:12px">
          <button class="btn-outline" style="padding:4px 8px;font-size:11px;border-radius:6px;border-color:var(--border2);color:var(--text2);cursor:pointer;background:transparent" onclick="alert('Download Aset dari Klien (ZIP)')">📁 Download Aset Klien</button>
        </div>
        <div class="kitem-footer" style="margin-bottom:8px">
          <span class="kitem-date">📅 ${o.date.split(',')[1] || o.date}</span>
          <div class="kitem-av">${o.initials}</div>
        </div>
        ${nextStatus ? `<button class="btn-xs bx-pri" style="width:100%; border-radius:4px; margin-top:4px" onclick="advanceOrderStatus('${o.id}', '${nextStatus}')">${nextLabel}</button>` : ''}
      </div>
    `;
  };

  colQueue.innerHTML = qOrders.map(o => getCardHTML(o, 'process', 'Proses Pengerjaan →')).join('');
  colProcess.innerHTML = pOrders.map(o => getCardHTML(o, 'review', 'Kirim ke Review →')).join('');
  colReview.innerHTML = rOrders.map(o => getCardHTML(o, 'done', 'Selesaikan & Kirim →')).join('');
  colDone.innerHTML = dOrders.map(o => getCardHTML(o, null, null)).join('');
}

function renderAdminVerification() {
  const container = document.getElementById('verificationTableBody');
  if (!container) return;

  const pendingOrders = orders.filter(o => o.status === 'problem'); // Hanya tampilkan yang bermasalah

  if (pendingOrders.length === 0) {
    container.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text3)">Semua pembayaran otomatis berhasil (Payment Gateway). Tidak ada kendala.</td></tr>`;
    return;
  }

  container.innerHTML = pendingOrders.map(o => `
    <tr style="background: rgba(239, 68, 68, 0.05)">
      <td><strong style="color:var(--text)">${o.id.slice(-4)}</strong></td>
      <td>${o.client}</td>
      <td><span style="background:var(--accentbg);color:var(--accent);padding:3px 10px;border-radius:50px;font-size:11px;font-weight:600">${o.pkg.split(' ')[0]}</span></td>
      <td><strong style="color:var(--accent)">Rp ${o.price.toLocaleString('id-ID')}</strong></td>
      <td>${o.method}</td>
      <td><span style="color:var(--accent); font-weight:bold; font-size:11px">GAGAL OTOMATIS</span></td>
      <td>
        <div style="display:flex;gap:6px">
          <button class="btn-verify" onclick="verifyOrderPayment('${o.id}', true)">Tandai Lunas</button>
          <button class="btn-reject" onclick="verifyOrderPayment('${o.id}', false)">Hubungi Klien</button>
        </div>
      </td>
    </tr>
  `).join('');
}

function verifyOrderPayment(orderId, approve) {
  const idx = orders.findIndex(o => o.id === orderId);
  if (idx !== -1) {
    if (approve) {
      orders[idx].status = 'queue';
      alert('✓ Pembayaran terverifikasi! Pesanan masuk antrean produksi.');
    } else {
      orders.splice(idx, 1);
      alert('✗ Pembayaran ditolak. Pesanan dibatalkan.');
    }
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function renderAdminCMSPackages() {
  const container = document.getElementById('cmsPackageRows');
  if (!container) return;
  container.innerHTML = packages.map(p => `
    <div class="cms-row">
      <div>
        <div class="cms-row-name">${p.name}</div>
        <div class="cms-row-meta">Pengerjaan: ${p.time} · Revisi: ${p.revision}</div>
        <div class="quota-track"><div class="quota-fill ${p.usedSlot < p.totalSlot ? 'safe' : ''}" style="width:${(p.usedSlot / p.totalSlot) * 100}%"></div></div>
        <div style="font-size:11px;color:${p.usedSlot === p.totalSlot ? 'var(--accent)' : 'var(--text3)'};margin-top:3px;font-weight:${p.usedSlot === p.totalSlot ? '600' : 'normal'}">
          ${p.usedSlot === p.totalSlot ? `⚠ Kuota penuh: ${p.usedSlot}/${p.totalSlot} slot` : `Kuota hari ini: ${p.usedSlot}/${p.totalSlot} slot`}
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:16px">
        <div class="cms-price">Rp ${(p.price / 1000)}K</div>
        <div style="display:flex;gap:6px">
          <button class="btn-edit" onclick="editPackagePrompt('${p.id}')">Edit</button>
          <button class="btn-delete" onclick="deletePackage('${p.id}')">Hapus</button>
        </div>
      </div>
    </div>
  `).join('');
}

function deletePackage(id) {
  if (confirm('Hapus paket ini?')) {
    packages = packages.filter(p => p.id !== id);
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function editPackagePrompt(id) {
  const p = packages.find(pkg => pkg.id === id);
  if (!p) return;
  const newPrice = prompt(`Edit harga untuk ${p.name}:`, p.price);
  if (newPrice !== null && !isNaN(newPrice)) {
    p.price = parseInt(newPrice);
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function addNewPackagePrompt() {
  const name = prompt('Nama Paket Baru:');
  if (!name) return;
  const price = prompt('Harga Paket (Rp):');
  if (!price || isNaN(price)) return;
  const id = 'pkg-' + name.toLowerCase().replace(/\s+/g, '-');
  packages.push({
    id: id,
    name: name,
    desc: 'Layanan baru dari tim Contify.',
    price: parseInt(price),
    time: '2–3 hari',
    revision: '1x',
    totalSlot: 10,
    usedSlot: 0
  });
  renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
}

function renderAdminCMSVouchers() {
  const container = document.getElementById('cmsVoucherRows');
  if (!container) return;
  container.innerHTML = vouchers.map(v => `
    <div class="cms-row">
      <div>
        <div class="cms-row-name">${v.code}</div>
        <div class="cms-row-meta">Potongan: ${v.discount * 100}% · Digunakan: ${v.used}x</div>
        <div style="font-size:11px;color:var(--green);font-weight:600;margin-top:3px">Status: Aktif (Selamanya)</div>
      </div>
      <div style="display:flex;align-items:center;gap:16px">
        <div class="cms-price" style="font-size:15px;color:var(--green)">Disk. ${v.discount * 100}%</div>
        <div style="display:flex;gap:6px">
          <button class="btn-delete" onclick="deleteVoucher('${v.code}')">Hapus</button>
        </div>
      </div>
    </div>
  `).join('');
}

function deleteVoucher(code) {
  if (confirm('Hapus voucher ini?')) {
    vouchers = vouchers.filter(v => v.code !== code);
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function addNewVoucherPrompt() {
  const code = prompt('Kode Voucher Baru:');
  if (!code) return;
  const disc = prompt('Diskon (0.01 - 0.99):');
  if (!disc || isNaN(disc)) return;
  vouchers.push({
    code: code.toUpperCase(),
    discount: parseFloat(disc),
    used: 0
  });
  renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
}

function renderAdminCMSTeam() {
  const container = document.getElementById('cmsTeamRows');
  if (!container) return;
  container.innerHTML = teamMembers.map(t => {
    let loadLabel = 'Senggang';
    let loadColor = 'var(--green)';
    if (t.active >= 8) { loadLabel = 'Overload'; loadColor = 'var(--accent)'; }
    else if (t.active >= 5) { loadLabel = 'Cukup Padat'; loadColor = 'var(--amber)'; }

    return `
      <div class="cms-row">
        <div>
          <div class="cms-row-name">${t.name}</div>
          <div class="cms-row-meta">Keahlian: ${t.skills} · Selesai: ${t.done} konten</div>
          <div style="font-size:11px;color:${loadColor};font-weight:600;margin-top:3px">Beban Aktif: ${t.active} Pesanan (${loadLabel})</div>
        </div>
        <div style="display:flex;align-items:center;gap:16px">
          <div class="cms-price" style="font-size:13px;color:var(--text2)">Status: ${t.status}</div>
          <div style="display:flex;gap:6px">
            <button class="btn-edit" onclick="openAturTugasModal()">Atur Tugas</button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function addNewTeamPrompt() {
  const name = prompt('Nama Anggota Tim Baru:');
  if (!name) return;
  teamMembers.push({
    name: name,
    skills: 'Generalist Editor',
    done: 0,
    active: 0,
    status: 'Online'
  });
  renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
}

function renderAdminStats() {
  const countToday = orders.length;
  const countPending = orders.filter(o => o.status === 'pending').length;
  const countProd = orders.filter(o => o.status === 'queue' || o.status === 'process' || o.status === 'review').length;
  const countDone = orders.filter(o => o.status === 'done').length;
  const sumRevenue = orders.reduce((acc, o) => acc + o.price, 0);

  document.getElementById('statPesananCount').textContent = countToday;
  document.getElementById('statVerifikasiCount').textContent = countPending;
  document.getElementById('statProduksiCount').textContent = countProd;
  document.getElementById('statSelesaiCount').textContent = countDone;

  let revText = 'Rp 0';
  if (sumRevenue >= 1000000) {
    revText = (sumRevenue / 1000000).toFixed(1).replace('.', ',') + ' Jt';
  } else {
    revText = 'Rp ' + (sumRevenue / 1000).toFixed(0) + 'K';
  }
  document.getElementById('statRevenueVal').textContent = revText;
}

function openAdminStatDetail(type, candidateName = 'Budi Santoso', candidateRole = 'Video Editor (TikTok/Reels)') {
  const modal = document.getElementById('adminDetailModal');
  const title = document.getElementById('modalTitle');
  const body = document.getElementById('modalBody');

  if (type === 'pesanan') {
    const breakdown = {};
    orders.forEach(o => {
      breakdown[o.pkg] = (breakdown[o.pkg] || 0) + 1;
    });

    let listHTML = Object.entries(breakdown).map(([pkg, count]) => `
      <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border)">
        <span style="font-weight:600">${pkg}</span>
        <span style="font-weight:700; color:var(--accent)">${count} Pesanan</span>
      </div>
    `).join('');

    if (!listHTML) listHTML = `<div style="text-align:center; padding:20px; color:var(--text3)">Belum ada pesanan masuk hari ini.</div>`;

    title.textContent = `Detail Pesanan Hari Ini (${orders.length})`;
    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Breakdown berdasarkan paket yang dipesan saat ini:</div>
      <div style="display:flex; flex-direction:column; gap:12px">
        ${listHTML}
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-sec" onclick="closeAdminModal(); switchAdminTab(document.querySelectorAll('.tab-bar .tab')[0], 'at1');">Buka Kanban Board</button>
      </div>
    `;
  } else if (type === 'verifikasi') {
    const pendingOrders = orders.filter(o => o.status === 'pending');
    let listHTML = pendingOrders.map(o => `
      <div style="padding:8px; background:var(--bg3); border-radius:var(--r-sm); font-size:13px; display:flex; justify-content:space-between; margin-bottom:6px">
        <span>${o.id.slice(-4)} ${o.client} (${o.pkg.split(' ')[0]})</span>
        <strong style="color:var(--accent)">Rp ${o.price.toLocaleString('id-ID')}</strong>
      </div>
    `).join('');

    if (!listHTML) listHTML = `<div style="text-align:center; padding:20px; color:var(--text3)">Tidak ada pesanan pending pembayaran.</div>`;

    title.textContent = `Pesanan Menunggu Verifikasi (${pendingOrders.length})`;
    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Ada ${pendingOrders.length} pembayaran menunggu tindakan verifikasi Anda:</div>
      <div style="display:flex; flex-direction:column;">
        ${listHTML}
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-pri" onclick="closeAdminModal(); switchAdminTab(document.querySelectorAll('.tab-bar .tab')[1], 'at2');">Buka Tab Verifikasi</button>
      </div>
    `;
  } else if (type === 'produksi') {
    const qCount = orders.filter(o => o.status === 'queue').length;
    const pCount = orders.filter(o => o.status === 'process').length;
    const rCount = orders.filter(o => o.status === 'review').length;
    const totalProd = qCount + pCount + rCount;

    title.textContent = `Dalam Produksi (${totalProd})`;
    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Status pengerjaan konten yang berjalan oleh tim saat ini:</div>
      <div style="display:flex; flex-direction:column; gap:12px">
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border)">
          <span style="font-weight:600">Dalam Antrean (Queue)</span>
          <span style="font-weight:700; color:var(--amber)">${qCount} Konten</span>
        </div>
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border)">
          <span style="font-weight:600">Sedang Diproses (Editing/Writing)</span>
          <span style="font-weight:700; color:var(--blue)">${pCount} Konten</span>
        </div>
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border)">
          <span style="font-weight:600">Dalam Review Internal QC</span>
          <span style="font-weight:700; color:var(--accent)">${rCount} Konten</span>
        </div>
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-sec" onclick="closeAdminModal(); switchAdminTab(document.querySelectorAll('.tab-bar .tab')[0], 'at1');">Buka Kanban Board</button>
      </div>
    `;
  } else if (type === 'selesai') {
    const doneOrders = orders.filter(o => o.status === 'done');
    let listHTML = doneOrders.map(o => `
      <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px dashed var(--border)">
        <span>${o.id.slice(-4)} ${o.client} (${o.pkg})</span>
        <span style="color:var(--green)">✓ Selesai</span>
      </div>
    `).join('');

    if (!listHTML) listHTML = `<div style="text-align:center; padding:20px; color:var(--text3)">Belum ada pesanan selesai hari ini.</div>`;

    title.textContent = `Selesai Hari Ini (${doneOrders.length})`;
    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Berikut adalah pesanan yang berhasil diselesaikan & dikirim:</div>
      <div style="display:flex; flex-direction:column; font-size:13px">
        ${listHTML}
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-sec" onclick="closeAdminModal();">Tutup</button>
      </div>
    `;
  } else if (type === 'revenue') {
    const sumRevenue = orders.reduce((acc, o) => acc + o.price, 0);
    let listHTML = orders.map(o => `
      <tr style="border-bottom:1px dashed var(--border)">
        <td style="padding:8px 0">${o.id.slice(-4)} (${o.client.split(' ')[0]})</td>
        <td style="padding:8px 0">${o.method}</td>
        <td style="padding:8px 0; text-align:right; font-weight:600">Rp ${o.price.toLocaleString('id-ID')}</td>
      </tr>
    `).join('');

    if (!listHTML) listHTML = `<tr><td colspan="3" style="text-align:center; padding:20px; color:var(--text3)">Belum ada pendapatan masuk.</td></tr>`;

    title.textContent = `Pendapatan Terverifikasi`;
    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Rincian pendapatan terverifikasi dari semua pesanan:</div>
      <table style="width:100%; border-collapse:collapse; font-size:13px">
        <thead>
          <tr style="border-bottom:1px solid var(--border); text-align:left; color:var(--text3)">
            <th style="padding:8px 0">Pesanan</th>
            <th style="padding:8px 0">Metode</th>
            <th style="padding:8px 0; text-align:right">Jumlah</th>
          </tr>
        </thead>
        <tbody>
          ${listHTML}
        </tbody>
      </table>
      <div style="margin-top:20px; padding-top:12px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
        <span style="font-size:14px; font-weight:700; color:var(--text)">Total Bersih:</span>
        <strong style="font-size:16px; color:var(--green)">Rp ${sumRevenue.toLocaleString('id-ID')}</strong>
      </div>
    `;

  } else if (type === 'division') {
    title.textContent = `Daftar Freelancer Aktif: ${candidateName}`;
    const div = divisions.find(d => d.name === candidateName);
    let listHTML = '';
    if (div && div.members.length > 0) {
      listHTML = div.members.map((m, i) => `
        <div class="cms-row">
          <div class="cms-row-name">${i + 1}. ${m.name}</div>
          <div style="color:${m.color}">${m.status}</div>
        </div>
      `).join('');
    } else {
      listHTML = '<div style="color:var(--text3);font-size:13px">Belum ada freelancer aktif di bidang ini.</div>';
    }

    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Menampilkan Freelancer yang tergabung dalam bidang ini:</div>
      <div style="display:flex; flex-direction:column; gap:8px;">
        ${listHTML}
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-sec" onclick="closeAdminModal();">Tutup</button>
      </div>
    `;
  } else if (type === 'freelancer') {
    title.textContent = `Detail Kandidat Freelancer`;

    // Find candidate full info in pendingFreelancers
    const candidate = pendingFreelancers.find(f => f.name === candidateName) || {};
    const exp = candidate.exp ? `${candidate.exp} Tahun` : 'Belum diisi';
    const portoLink = candidate.porto ? `<a href="${candidate.porto}" target="_blank" style="color:var(--blue);text-decoration:underline">Lihat Portofolio</a>` : 'Tidak tersedia';

    body.innerHTML = `
      <div style="margin-bottom:16px; font-size:14px; color:var(--text2)">Profil Freelancer Terdaftar (Menunggu Persetujuan):</div>
      <div style="display:flex; flex-direction:column; gap:12px; font-size:13px">
        <div style="padding:12px; border:1px solid var(--border); border-radius:var(--r-md)">
          <div style="display:flex; justify-content:space-between; margin-bottom:8px">
            <strong style="color:var(--text); font-size:14px">${candidateName}</strong>
            <span style="color:var(--amber)">Menunggu</span>
          </div>
          <div style="color:var(--text2); margin-bottom:4px">Keahlian: ${candidateRole}</div>
          <div style="color:var(--text3)">Pengalaman: ${exp} / Portofolio: ${portoLink}</div>
          <div style="margin-top:12px; display:flex; gap:8px">
            <button class="btn-primary" style="padding:6px 12px; font-size:11px" onclick="approveFreelancer('${candidateName}')">Terima (Approve)</button>
            <button class="btn-outline" style="padding:6px 12px; font-size:11px" onclick="rejectFreelancer('${candidateName}')">Tolak</button>
          </div>
        </div>
      </div>
      <div style="margin-top:20px; text-align:right">
        <button class="btn-xs bx-sec" onclick="closeAdminModal();">Tutup</button>
      </div>
    `;
  }

  modal.classList.remove('hidden');
}

function closeAdminModal() {
  document.getElementById('adminDetailModal').classList.add('hidden');
}

function reorderPackage(pkgName, price) {
  orderState.pkg = pkgName;
  orderState.price = price;
  orderState.multi = 1;
  orderState.speedLbl = 'Reguler (3 hari)';
  orderState.discount = 0;

  document.querySelectorAll('.pkg-card').forEach(card => {
    const name = card.querySelector('.pkg-name').textContent;
    card.classList.toggle('selected', name === pkgName);
  });

  document.querySelectorAll('.deadline-opt').forEach(opt => {
    const lbl = opt.querySelector('.deadline-lbl').textContent;
    opt.classList.toggle('selected', lbl === 'Reguler');
  });

  updatePrice();
  goStep(1);
  document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
}

function requestRevisionPrompt(orderId) {
  const order = orders.find(o => o.id === orderId);
  if (!order) return;
  if (order.revisions <= 0) {
    alert('Sisa revisi Anda telah habis untuk pesanan ini.');
    return;
  }
  const revBrief = prompt('Masukkan instruksi revisi Anda:');
  if (revBrief) {
    order.revisions--;
    order.status = 'process';
    alert('✓ Permintaan revisi dikirim. Status pesanan dikembalikan ke "Sedang Diproses" untuk dikerjakan ulang.');
    renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();
  }
}

function renderAllDynamicData() {
  renderOrderPackages();
  renderUserDashboard();
  renderAdminKanban();
  renderAdminVerification();
  renderAdminCMSPackages();
  renderAdminCMSVouchers();
  renderAdminCMSTeam();
  renderAdminStats();
  renderFreelancerDashboard();
}

// Initial render
renderAllDynamicData(); renderPendingFreelancers(); renderKuotaBidang();

function renderPendingFreelancers() {
  const container = document.getElementById('pendingFreelancersContainer');
  if (!container) return;

  if (pendingFreelancers.length === 0) {
    container.innerHTML = '<p style="color:var(--text2)">Belum ada pendaftar baru.</p>';
    return;
  }

  container.innerHTML = pendingFreelancers.map(pf => `
    <div class="cms-row" style="background:var(--bg3); border-radius:var(--r-md); padding:16px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-weight:700; color:var(--text); margin-bottom:4px">${pf.name}</div>
        <div style="font-size:12px; color:var(--text2)">${pf.role}</div>
      </div>
      <button class="btn-primary" style="padding:6px 12px; font-size:12px" onclick="openAdminStatDetail('freelancer', '${pf.name}', '${pf.role}')">Review Kandidat</button>
    </div>
  `).join('');
}

function approveFreelancer(name) {
  if (!confirm('Anda yakin ingin menerima kandidat ini menjadi Freelancer aktif?')) return;

  const idx = pendingFreelancers.findIndex(f => f.name === name);
  if (idx !== -1) {
    const candidate = pendingFreelancers[idx];
    pendingFreelancers.splice(idx, 1);

    let shortRole = 'Editor';
    if (candidate.role.includes('Video')) shortRole = 'Videographer';
    if (candidate.role.includes('Foto')) shortRole = 'Editor Foto';
    if (candidate.role.includes('Copy')) shortRole = 'Copywriter';
    if (candidate.role.includes('Strateg')) shortRole = 'Strategist';

    teamMembers.push({
      name: `${candidate.name} (${shortRole})`,
      skills: candidate.role,
      done: 0,
      active: 0,
      status: 'Online'
    });

    const div = divisions.find(d => d.name === candidate.role);
    if (div) {
      div.members.push({ name: candidate.name, status: 'Baru Bergabung (0 Pesanan)', color: 'var(--green)' });
    }

    renderPendingFreelancers();
    renderAllDynamicData();
    renderKuotaBidang();

    alert(`Memproses penerimaan & menyiapkan pesan otomatis (WhatsApp API) untuk ${candidate.name}...`);
    setTimeout(() => {
      alert(`[NOTIFIKASI SISTEM]\n\nPesan WhatsApp berhasil terkirim ke nomor pelamar!\nIsi: "Selamat! Anda diterima sebagai Freelancer di Contify. Berikut akses login Anda..."\n\n${candidate.name} telah resmi menjadi Freelancer aktif.`);
      closeAdminModal();
    }, 1500);
  }
}

function rejectFreelancer(name) {
  if (!confirm('Tolak kandidat ini?')) return;

  const idx = pendingFreelancers.findIndex(f => f.name === name);
  if (idx !== -1) {
    const candidate = pendingFreelancers[idx];
    pendingFreelancers.splice(idx, 1);
    renderPendingFreelancers();

    alert(`Memproses penolakan & menyiapkan pesan otomatis (WhatsApp API) untuk ${candidate.name}...`);
    setTimeout(() => {
      alert(`[NOTIFIKASI SISTEM]\n\nPesan WhatsApp berhasil terkirim ke nomor pelamar!\nIsi: "Mohon maaf, saat ini portofolio Anda belum memenuhi kualifikasi kami..."\n\nLamaran ${candidate.name} telah resmi ditolak.`);
      closeAdminModal();
    }, 1500);
  }
}

function userMintaRevisi(orderId) {
  const order = orders.find(o => o.id === orderId);
  if (order && order.revisions > 0) {
    const feedback = prompt("Silakan masukkan detail bagian mana saja yang ingin direvisi oleh freelancer:\n(Contoh: Tolong ganti lagu latarnya jadi lebih ceria)");
    
    if (feedback !== null && feedback.trim() !== "") {
      order.revisions -= 1;
      order.status = 'process';
      order.revisionNotes = feedback; // Simpan catatan revisi agar bisa dibaca freelancer nanti
      alert('✓ Catatan revisi berhasil dikirim ke tim! Status pesanan dikembalikan ke "Sedang Diproses". Sisa kuota revisi Anda: ' + order.revisions + 'x');
      renderAllDynamicData();
    } else if (feedback !== null) {
      alert('Permintaan revisi dibatalkan. Catatan revisi tidak boleh kosong.');
    }
  } else if (order) {
    alert('Maaf, kuota revisi gratis Anda untuk paket ini telah habis.');
  }
}

function userTerimaHasil(orderId) {
  const order = orders.find(o => o.id === orderId);
  if (order) {
    order.status = 'done';
    alert('🎉 Hasil diterima! Anda dapat mengunduhnya kapan saja di menu Riwayat.');
    renderAllDynamicData();
  }
}


function openAturTugasModal() {
  document.getElementById('aturTugasModal').classList.remove('hidden');
}
function closeAturTugasModal() {
  document.getElementById('aturTugasModal').classList.add('hidden');
}
function submitAturTugas() {
  const ord = document.getElementById('assignOrderSelect').value;
  const free = document.getElementById('assignFreelancerSelect').value;
  if (!ord || !free) {
    alert('Harap pilih pesanan dan freelancer!');
    return;
  }
  if (confirm('Tugaskan pesanan ini kepada freelancer terpilih?')) {
    // Find a queue order and change to process as a dummy effect
    const qOrder = orders.find(o => o.status === 'queue');
    if (qOrder) {
      qOrder.status = 'process';
      renderAllDynamicData();
    }
    alert('Tugas berhasil diberikan! Pesanan kini masuk tahap "Sedang Diproses".');
    closeAturTugasModal();
  }
}
function handleOrderBtn(pkgName) {
  if (!currentRole) {
    document.getElementById('loginOverlay').classList.remove('hidden');
    return;
  }
  if (pkgName) {
    orderState.pkg = pkgName;
    const p = packages.find(x => x.name === pkgName);
    if (p) orderState.price = p.price;

    if (typeof renderOrderPackages === 'function') renderOrderPackages();
  }

  if (typeof goStep === 'function') {
    goStep(1);
  }

  const orderSection = document.getElementById('order');
  if (orderSection) {
    window.location.hash = 'order';
    try {
      orderSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {
      console.error(e);
    }
  } else {
    window.location.hash = 'order';
  }
}

// ==========================================
// FREELANCER REGISTRATION (PUBLIC)
// ==========================================
function openFreelancerReg() {
  document.getElementById('freelancerRegModal').classList.remove('hidden');
}

function submitFreelancerReg(event) {
  event.preventDefault();
  const name = document.getElementById('regName').value;
  const role = document.getElementById('regRole').value;
  const exp = document.getElementById('regExperience').value;
  const porto = document.getElementById('regPorto').value;

  // Masukkan pendaftar ke dalam antrean dashboard admin
  pendingFreelancers.push({ name: name, role: role, exp: exp, porto: porto });

  // Reset form dan tutup modal
  document.getElementById('freelancerRegForm').reset();
  document.getElementById('freelancerRegModal').classList.add('hidden');

  alert('Pendaftaran berhasil dikirim! Tim kami akan meninjau lamaran dan portofolio Anda dalam waktu 1x24 jam.');

  // Refresh daftar antrean jika kebetulan admin sedang membuka dashboard
  if (typeof renderPendingFreelancers === 'function') {
    renderPendingFreelancers();
  }
}
