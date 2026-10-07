"use strict";
// Zero-dependency server (Node.js 18+). No `npm install` needed.
const http = require("http");
const fs = require("fs");
const path = require("path");

const PORT = process.env.PORT || 3000;
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || "changeme";

const DATA_DIR = path.join(__dirname, "data");
const PUBLIC_DIR = path.join(__dirname, "public");
const MENU_FILE = path.join(DATA_DIR, "menu.json");
const ORDERS_FILE = path.join(DATA_DIR, "orders.json");
const SOLDOUT_FILE = path.join(DATA_DIR, "soldout.json");

const STATUSES = ["new", "preparing", "served", "cancelled"];
const MAX_QTY_PER_ITEM = 20;
const MAX_LINES = 40;
const MAX_BODY = 50 * 1024;

const MIME = {
  ".html": "text/html; charset=utf-8",
  ".js": "text/javascript; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".png": "image/png",
  ".jpg": "image/jpeg",
  ".jpeg": "image/jpeg",
  ".webp": "image/webp",
  ".svg": "image/svg+xml",
  ".ico": "image/x-icon",
};

function readJson(file, fallback) {
  try {
    return JSON.parse(fs.readFileSync(file, "utf8"));
  } catch {
    return fallback;
  }
}

// Write atomically so a crash never leaves a half-written file.
function writeJson(file, data) {
  const tmp = file + ".tmp";
  fs.writeFileSync(tmp, JSON.stringify(data, null, 2));
  fs.renameSync(tmp, file);
}

const menu = readJson(MENU_FILE, { categories: [], items: [] });
const orders = readJson(ORDERS_FILE, []);
const soldOut = new Set(readJson(SOLDOUT_FILE, []));
let nextId = orders.reduce((m, o) => Math.max(m, o.id), 0) + 1;

// ---------- helpers ----------

function send(res, status, body) {
  const json = JSON.stringify(body);
  res.writeHead(status, {
    "Content-Type": "application/json; charset=utf-8",
    "Cache-Control": "no-store",
  });
  res.end(json);
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    let size = 0;
    const chunks = [];
    req.on("data", (c) => {
      size += c.length;
      if (size > MAX_BODY) {
        reject(new Error("too_large"));
        req.destroy();
        return;
      }
      chunks.push(c);
    });
    req.on("end", () => {
      if (chunks.length === 0) return resolve({});
      try {
        resolve(JSON.parse(Buffer.concat(chunks).toString("utf8")));
      } catch {
        reject(new Error("bad_json"));
      }
    });
    req.on("error", reject);
  });
}

function isAdmin(req) {
  const pw = req.headers["x-admin-password"];
  return typeof pw === "string" && pw === ADMIN_PASSWORD;
}

function serveStatic(req, res, pathname) {
  let rel = pathname === "/" ? "/index.html" : pathname;
  let filePath;
  try {
    filePath = path.normalize(path.join(PUBLIC_DIR, decodeURIComponent(rel)));
  } catch {
    res.writeHead(400);
    return res.end();
  }
  // Block path traversal outside /public.
  if (!filePath.startsWith(PUBLIC_DIR + path.sep)) {
    res.writeHead(403);
    return res.end();
  }
  fs.readFile(filePath, (err, data) => {
    if (err) {
      res.writeHead(404, { "Content-Type": "text/plain; charset=utf-8" });
      return res.end("Not found");
    }
    const type = MIME[path.extname(filePath).toLowerCase()] || "application/octet-stream";
    res.writeHead(200, { "Content-Type": type });
    res.end(data);
  });
}

// ---------- API handlers ----------

function getMenu(req, res) {
  send(res, 200, {
    categories: menu.categories,
    items: menu.items.map((i) => ({ ...i, soldOut: soldOut.has(i.id) })),
  });
}

async function createOrder(req, res) {
  const { table, lang, items, note } = await readBody(req);

  const tableStr = String(table || "").trim();
  if (!/^[A-Za-z0-9\-]{1,10}$/.test(tableStr)) {
    return send(res, 400, { error: "invalid_table" });
  }
  if (!Array.isArray(items) || items.length === 0 || items.length > MAX_LINES) {
    return send(res, 400, { error: "invalid_items" });
  }

  const lines = [];
  let total = 0;
  for (const raw of items) {
    const item = menu.items.find((i) => i.id === (raw && raw.id));
    const qty = Number(raw && raw.qty);
    if (!item || !Number.isInteger(qty) || qty < 1 || qty > MAX_QTY_PER_ITEM) {
      return send(res, 400, { error: "invalid_items" });
    }
    if (soldOut.has(item.id)) {
      return send(res, 409, { error: "sold_out", id: item.id });
    }
    // Price always comes from the server, never from the client.
    lines.push({ id: item.id, name: item.names.ja, qty, price: item.price });
    total += item.price * qty;
  }

  const order = {
    id: nextId++,
    table: tableStr,
    lang: String(lang || "ja").slice(0, 8),
    lines,
    total,
    note: String(note || "").slice(0, 200),
    status: "new",
    createdAt: new Date().toISOString(),
  };
  orders.push(order);
  writeJson(ORDERS_FILE, orders);
  send(res, 201, { id: order.id, total: order.total, status: order.status });
}

function orderStatus(req, res, id) {
  const order = orders.find((o) => o.id === id);
  if (!order) return send(res, 404, { error: "not_found" });
  send(res, 200, { id: order.id, status: order.status });
}

function adminListOrders(req, res, query) {
  let list = orders;
  const status = query.get("status");
  const date = query.get("date");
  if (status) list = list.filter((o) => o.status === status);
  if (date) list = list.filter((o) => o.createdAt.startsWith(date));
  send(res, 200, list.slice().reverse());
}

async function adminUpdateOrder(req, res, id) {
  const order = orders.find((o) => o.id === id);
  if (!order) return send(res, 404, { error: "not_found" });
  const { status } = await readBody(req);
  if (!STATUSES.includes(status)) return send(res, 400, { error: "invalid_status" });
  order.status = status;
  writeJson(ORDERS_FILE, orders);
  send(res, 200, order);
}

function adminMenu(req, res) {
  send(res, 200, menu.items.map((i) => ({ id: i.id, name: i.names.ja, soldOut: soldOut.has(i.id) })));
}

async function adminSoldOut(req, res, itemId) {
  const item = menu.items.find((i) => i.id === itemId);
  if (!item) return send(res, 404, { error: "not_found" });
  const body = await readBody(req);
  if (body.soldOut) soldOut.add(item.id);
  else soldOut.delete(item.id);
  writeJson(SOLDOUT_FILE, [...soldOut]);
  send(res, 200, { id: item.id, soldOut: soldOut.has(item.id) });
}

// ---------- router ----------

const server = http.createServer(async (req, res) => {
  const url = new URL(req.url, "http://localhost");
  const p = url.pathname;
  const m = req.method;

  try {
    if (p.startsWith("/api/")) {
      let match;
      if (m === "GET" && p === "/api/menu") return getMenu(req, res);
      if (m === "POST" && p === "/api/orders") return await createOrder(req, res);
      if (m === "GET" && (match = p.match(/^\/api\/orders\/(\d+)\/status$/))) {
        return orderStatus(req, res, Number(match[1]));
      }

      if (p.startsWith("/api/admin/")) {
        if (!isAdmin(req)) return send(res, 401, { error: "unauthorized" });
        if (m === "GET" && p === "/api/admin/orders") return adminListOrders(req, res, url.searchParams);
        if (m === "PATCH" && (match = p.match(/^\/api\/admin\/orders\/(\d+)$/))) {
          return await adminUpdateOrder(req, res, Number(match[1]));
        }
        if (m === "GET" && p === "/api/admin/menu") return adminMenu(req, res);
        if (m === "PATCH" && (match = p.match(/^\/api\/admin\/menu\/([\w\-]+)\/soldout$/))) {
          return await adminSoldOut(req, res, match[1]);
        }
      }
      return send(res, 404, { error: "not_found" });
    }

    if (m === "GET" || m === "HEAD") return serveStatic(req, res, p);
    res.writeHead(405);
    res.end();
  } catch (e) {
    if (e.message === "too_large") return send(res, 413, { error: "too_large" });
    if (e.message === "bad_json") return send(res, 400, { error: "bad_json" });
    console.error(e);
    send(res, 500, { error: "server_error" });
  }
});

server.listen(PORT, () => {
  console.log(`寿司 博多魚がし order server: http://localhost:${PORT}`);
  if (ADMIN_PASSWORD === "changeme") {
    console.warn("WARNING: set ADMIN_PASSWORD before going live (default is 'changeme').");
  }
});