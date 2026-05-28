/**
 * wa-bridge.js — WhatsApp Web Bridge untuk Desa
 * 
 * Jalankan: node wa-bridge.js
 * Port    : 3001
 * 
 * Endpoint:
 *   GET  /status       → status koneksi + QR jika belum scan
 *   GET  /qr           → QR code base64 (untuk polling)
 *   POST /send         → kirim pesan { nomor, pesan }
 *   POST /disconnect   → logout WA
 */

const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode  = require('qrcode');
const express = require('express');

const PORT   = 3001;
const SECRET = process.env.WA_BRIDGE_SECRET || 'desa-bridge-secret';

const app = express();
app.use(express.json());

// ── State ─────────────────────────────────────────────────────────────────────
let qrBase64   = null;
let isReady    = false;
let clientInfo = null;
let waClient   = null;

// ── Auth middleware ───────────────────────────────────────────────────────────
app.use((req, res, next) => {
    const token = req.headers['x-bridge-token'] || req.query.token;
    if (token !== SECRET) return res.status(401).json({ ok: false, error: 'Unauthorized' });
    next();
});

// ── Init WA Client ────────────────────────────────────────────────────────────
function initClient() {
    if (waClient) return;

    waClient = new Client({
        authStrategy: new LocalAuth({ dataPath: './.wa-session' }),
        puppeteer: {
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
        },
    });

    waClient.on('qr', async (qr) => {
        isReady  = false;
        qrBase64 = await qrcode.toDataURL(qr);
        console.log('[WA] QR baru digenerate, silakan scan.');
    });

    waClient.on('ready', () => {
        isReady    = true;
        qrBase64   = null;
        clientInfo = waClient.info;
        console.log('[WA] Terhubung sebagai', clientInfo?.pushname, clientInfo?.wid?.user);
    });

    waClient.on('auth_failure', () => {
        isReady  = false;
        qrBase64 = null;
        console.error('[WA] Auth gagal, restart diperlukan.');
    });

    waClient.on('disconnected', (reason) => {
        isReady    = false;
        qrBase64   = null;
        clientInfo = null;
        waClient   = null;
        console.warn('[WA] Disconnect:', reason, '— akan restart dalam 5 detik...');
        setTimeout(initClient, 5000);
    });

    waClient.initialize();
    console.log('[WA] Inisialisasi client...');
}

// ── Routes ────────────────────────────────────────────────────────────────────

// Status & QR
app.get('/status', (req, res) => {
    res.json({
        ok    : true,
        ready : isReady,
        qr    : qrBase64,
        name  : clientInfo?.pushname || null,
        nomor : clientInfo?.wid?.user || null,
    });
});

// QR saja (untuk polling ringan)
app.get('/qr', (req, res) => {
    if (isReady)   return res.json({ ok: true, ready: true,  qr: null });
    if (!qrBase64) return res.json({ ok: true, ready: false, qr: null, msg: 'Generating QR...' });
    res.json({ ok: true, ready: false, qr: qrBase64 });
});

// Kirim pesan
app.post('/send', async (req, res) => {
    const { nomor, pesan } = req.body;

    if (!isReady) return res.status(503).json({ ok: false, error: 'WA belum terhubung.' });
    if (!nomor)   return res.status(400).json({ ok: false, error: 'Nomor kosong.' });
    if (!pesan)   return res.status(400).json({ ok: false, error: 'Pesan kosong.' });

    // Normalisasi nomor → format 62xxx
    let n = nomor.replace(/[^0-9]/g, '');
    if (n.startsWith('0')) n = '62' + n.slice(1);

    console.log('[WA] Mencoba kirim ke nomor:', n);

    try {
        // Resolve ID yang benar — handle LID baru WA
        let chatId;
        try {
            const numberId = await waClient.getNumberId(n);
            console.log('[WA] getNumberId result:', JSON.stringify(numberId));
            chatId = numberId ? numberId._serialized : (n + '@c.us');
        } catch (lidErr) {
            console.error('[WA] getNumberId error:', lidErr.message);
            chatId = n + '@c.us';
        }

        console.log('[WA] Kirim ke chatId:', chatId);
        await waClient.sendMessage(chatId, pesan);
        console.log('[WA] Pesan terkirim ke', n, '(' + chatId + ')');
        res.json({ ok: true, msg: 'Terkirim ke ' + n });
    } catch (e) {
        console.error('[WA] Gagal kirim final:', e.message);
        res.status(500).json({ ok: false, error: e.message });
    }
});

// Disconnect / logout
app.post('/disconnect', async (req, res) => {
    if (!waClient) return res.json({ ok: true, msg: 'Tidak ada sesi aktif.' });
    try {
        await waClient.logout();
        waClient   = null;
        isReady    = false;
        clientInfo = null;
        console.log('[WA] Logout berhasil.');
        res.json({ ok: true, msg: 'Logout berhasil.' });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

// ── Start ─────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
    console.log(`[Bridge] Jalan di http://localhost:${PORT}`);
    console.log(`[Bridge] Secret: ${SECRET}`);
    initClient();
});