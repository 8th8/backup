(function () {
  "use strict";

  const $ = (sel) => document.querySelector(sel);
  const yen = (n) => "¥" + n.toLocaleString("ja-JP");

  const state = {
    lang: "ja",
    table: new URLSearchParams(location.search).get("table") || "",
    menu: { categories: [], items: [] },
    cart: {}, // itemId -> qty
    pollTimer: null,
  };

  const t = (key) => (I18N.strings[state.lang] || I18N.strings.ja)[key] || key;

  // ---------- Views ----------
  function show(viewId) {
    ["view-lang", "view-menu", "view-thanks"].forEach((id) => {
      $("#" + id).hidden = id !== viewId;
    });
    window.scrollTo(0, 0);
  }

  function applyI18n() {
    document.documentElement.lang = state.lang;
    document.querySelectorAll("[data-t]").forEach((el) => {
      el.textContent = t(el.dataset.t);
    });
    $("#btn-change-lang").textContent = "🌐 " + t("changeLang");
    $("#table-input").placeholder = t("tablePlaceholder");
    $("#note-input").placeholder = t("notePlaceholder");
    $("#btn-send").textContent = t("send");
    $("#btn-more").textContent = t("orderMore");
    $("#table-badge").textContent = state.table || "-";
  }

  // ---------- Language selection ----------
  function buildLangButtons() {
    const box = $("#lang-buttons");
    I18N.languages.forEach((l) => {
      const b = document.createElement("button");
      b.type = "button";
      b.className = "lang-btn";
      b.textContent = l.label;
      b.addEventListener("click", () => {
        state.lang = l.code;
        applyI18n();
        renderMenu();
        updateCartBar();
        show("view-menu");
      });
      box.appendChild(b);
    });
  }

  // ---------- Menu ----------
  function itemName(item) { return item.names[state.lang] || item.names.ja; }
  function itemDesc(item) { return (item.desc && (item.desc[state.lang] || item.desc.ja)) || ""; }

  function renderMenu() {
    const tabs = $("#cat-tabs");
    const list = $("#menu-list");
    tabs.textContent = "";
    list.textContent = "";

    state.menu.categories.forEach((cat, idx) => {
      const label = cat.names[state.lang] || cat.names.ja;

      const tab = document.createElement("button");
      tab.type = "button";
      tab.className = "cat-tab" + (idx === 0 ? " active" : "");
      tab.textContent = label;
      tab.addEventListener("click", () => {
        const target = document.getElementById("cat-" + cat.id);
        if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
        tabs.querySelectorAll(".cat-tab").forEach((x) => x.classList.remove("active"));
        tab.classList.add("active");
      });
      tabs.appendChild(tab);

      const h = document.createElement("h2");
      h.className = "cat-heading";
      h.id = "cat-" + cat.id;
      h.textContent = label;
      list.appendChild(h);

      state.menu.items.filter((i) => i.category === cat.id).forEach((item) => {
        list.appendChild(buildCard(item));
      });
    });
  }

  function buildCard(item) {
    const card = document.createElement("article");
    card.className = "card" + (item.soldOut ? " sold-out" : "");
    card.dataset.id = item.id;

    const imgBox = document.createElement("div");
    imgBox.className = "card-img";
    imgBox.textContent = item.emoji || "🍣";
    if (item.image) {
      const img = document.createElement("img");
      img.alt = itemName(item);
      img.loading = "lazy";
      img.src = item.image;
      img.addEventListener("error", () => img.remove()); // fall back to emoji
      imgBox.appendChild(img);
    }

    const body = document.createElement("div");
    body.className = "card-body";

    const name = document.createElement("h3");
    name.className = "card-name";
    name.textContent = itemName(item);

    const desc = document.createElement("p");
    desc.className = "card-desc";
    desc.textContent = itemDesc(item);

    const foot = document.createElement("div");
    foot.className = "card-foot";

    const price = document.createElement("div");
    price.className = "price";
    price.textContent = yen(item.price);
    const small = document.createElement("small");
    small.textContent = t("taxIncl");
    price.appendChild(small);

    const action = document.createElement("div");
    action.className = "card-action";
    renderAction(action, item);

    foot.append(price, action);
    body.append(name, desc, foot);
    card.append(imgBox, body);
    return card;
  }

  function renderAction(container, item) {
    container.textContent = "";
    const qty = state.cart[item.id] || 0;

    if (item.soldOut) {
      const b = document.createElement("button");
      b.className = "btn-add";
      b.disabled = true;
      b.textContent = t("soldOut");
      container.appendChild(b);
      return;
    }
    if (qty === 0) {
      const b = document.createElement("button");
      b.type = "button";
      b.className = "btn-add";
      b.textContent = t("add");
      b.addEventListener("click", () => changeQty(item.id, 1));
      container.appendChild(b);
    } else {
      container.appendChild(buildStepper(item.id, qty));
    }
  }

  function buildStepper(id, qty) {
    const wrap = document.createElement("div");
    wrap.className = "stepper";
    const minus = document.createElement("button");
    minus.type = "button";
    minus.textContent = "−";
    minus.setAttribute("aria-label", "-1");
    minus.addEventListener("click", () => changeQty(id, -1));
    const num = document.createElement("span");
    num.textContent = qty;
    const plus = document.createElement("button");
    plus.type = "button";
    plus.textContent = "+";
    plus.setAttribute("aria-label", "+1");
    plus.addEventListener("click", () => changeQty(id, 1));
    wrap.append(minus, num, plus);
    return wrap;
  }

  // ---------- Cart ----------
  function changeQty(id, delta) {
    const next = Math.max(0, Math.min(20, (state.cart[id] || 0) + delta));
    if (next === 0) delete state.cart[id];
    else state.cart[id] = next;

    const item = state.menu.items.find((i) => i.id === id);
    const action = document.querySelector('.card[data-id="' + id + '"] .card-action');
    if (item && action) renderAction(action, item);
    updateCartBar();
    if (!$("#sheet").hidden) renderCartSheet();
  }

  function cartSummary() {
    let count = 0, total = 0;
    for (const [id, qty] of Object.entries(state.cart)) {
      const item = state.menu.items.find((i) => i.id === id);
      if (!item) continue;
      count += qty;
      total += item.price * qty;
    }
    return { count, total };
  }

  function updateCartBar() {
    const { count, total } = cartSummary();
    $("#cart-bar").hidden = count === 0;
    $("#cart-count").textContent = count;
    $("#cart-bar-total").textContent = yen(total);
  }

  function renderCartSheet() {
    const box = $("#cart-lines");
    box.textContent = "";
    const ids = Object.keys(state.cart);

    if (ids.length === 0) {
      const p = document.createElement("p");
      p.className = "cart-empty";
      p.textContent = t("cartEmpty");
      box.appendChild(p);
    }

    ids.forEach((id) => {
      const item = state.menu.items.find((i) => i.id === id);
      if (!item) return;
      const qty = state.cart[id];

      const row = document.createElement("div");
      row.className = "cart-line";
      const name = document.createElement("div");
      name.className = "cart-line-name";
      name.textContent = itemName(item);
      const price = document.createElement("div");
      price.className = "cart-line-price";
      price.textContent = yen(item.price) + " × " + qty + " = " + yen(item.price * qty);
      row.append(name, price, buildStepper(id, qty));
      box.appendChild(row);
    });

    $("#cart-total").textContent = yen(cartSummary().total);
    $("#table-field").hidden = !!state.table;
    $("#btn-send").disabled = ids.length === 0;
  }

  function openSheet() {
    $("#cart-error").hidden = true;
    renderCartSheet();
    $("#sheet").hidden = false;
  }
  function closeSheet() { $("#sheet").hidden = true; }

  // ---------- Sending the order ----------
  async function sendOrder() {
    const err = $("#cart-error");
    err.hidden = true;

    let table = state.table;
    if (!table) {
      table = $("#table-input").value.trim();
      if (!/^[A-Za-z0-9\-]{1,10}$/.test(table)) {
        err.textContent = t("tableNeeded");
        err.hidden = false;
        return;
      }
    }

    const btn = $("#btn-send");
    btn.disabled = true;
    btn.textContent = t("sending");

    try {
      const res = await fetch("/api/orders", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          table,
          lang: state.lang,
          note: $("#note-input").value,
          items: Object.entries(state.cart).map(([id, qty]) => ({ id, qty })),
        }),
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 409) {
        err.textContent = t("errSoldOut");
        err.hidden = false;
        await loadMenu(); // refresh sold-out flags
        return;
      }
      if (!res.ok) throw new Error(data.error || "error");

      state.table = table;
      state.cart = {};
      $("#note-input").value = "";
      closeSheet();
      showThanks(data.id, data.status);
    } catch (e) {
      err.textContent = t("errGeneric");
      err.hidden = false;
    } finally {
      btn.disabled = Object.keys(state.cart).length === 0;
      btn.textContent = t("send");
    }
  }

  // ---------- Thanks + status polling ----------
  function setStatus(status) {
    const pill = $("#order-status");
    pill.className = "status-pill " + status;
    pill.textContent = t("status_" + status);
  }

  function showThanks(orderId, status) {
    $("#order-no").textContent = "#" + orderId;
    setStatus(status);
    applyI18n();
    show("view-thanks");

    clearInterval(state.pollTimer);
    state.pollTimer = setInterval(async () => {
      try {
        const res = await fetch("/api/orders/" + orderId + "/status");
        if (!res.ok) return;
        const d = await res.json();
        setStatus(d.status);
        if (d.status === "served" || d.status === "cancelled") clearInterval(state.pollTimer);
      } catch { /* try again next tick */ }
    }, 10000);
  }

  // ---------- Boot ----------
  async function loadMenu() {
    const res = await fetch("/api/menu");
    state.menu = await res.json();
    renderMenu();
    updateCartBar();
  }

  function init() {
    buildLangButtons();
    applyI18n();

    $("#btn-change-lang").addEventListener("click", () => show("view-lang"));
    $("#cart-bar").addEventListener("click", openSheet);
    $("#sheet-close").addEventListener("click", closeSheet);
    $("#sheet-backdrop").addEventListener("click", closeSheet);
    $("#btn-send").addEventListener("click", sendOrder);
    $("#btn-more").addEventListener("click", () => {
      clearInterval(state.pollTimer);
      loadMenu();
      show("view-menu");
    });

    loadMenu().catch(() => {
      // Menu failed to load; the language screen still shows, retry on next tap.
    });
  }

  init();
})();