(() => {
  const root = document.documentElement;

  const setTheme = (theme) => {
    const value = theme === 'dark' ? 'dark' : 'light';
    root.dataset.theme = value;
    localStorage.setItem('awi-theme', value);
    document.querySelectorAll('[data-theme-label]').forEach(el => el.textContent = value === 'dark' ? 'Dark' : 'Light');
  };
  setTheme(localStorage.getItem('awi-theme') || root.dataset.theme || 'light');
  document.querySelectorAll('[data-theme-toggle]').forEach(btn => btn.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark')));

  const createEl = (tag, className, text) => {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (text !== undefined && text !== null) el.textContent = text;
    return el;
  };

  const toast = (message, tone = 'success') => {
    let stack = document.querySelector('[data-awi-toast-stack]');
    if (!stack) {
      stack = createEl('div', 'awi-toast-stack');
      stack.dataset.awiToastStack = '1';
      stack.setAttribute('aria-live', 'polite');
      document.body.appendChild(stack);
    }

    const item = createEl('div', `awi-toast awi-toast-${tone}`);
    const mark = createEl('span', 'awi-toast-mark', tone === 'success' ? '✓' : tone === 'danger' ? '!' : 'i');
    const text = createEl('span', 'awi-toast-text', message);
    item.append(mark, text);
    stack.appendChild(item);
    requestAnimationFrame(() => item.classList.add('show'));
    setTimeout(() => {
      item.classList.remove('show');
      setTimeout(() => item.remove(), 220);
    }, 2400);
  };

  const dialog = ({
    title = 'Konfirmasi',
    message = '',
    tone = 'info',
    confirmText = 'OK',
    cancelText = null,
    inputValue = null,
  } = {}) => new Promise(resolve => {
    const overlay = createEl('div', 'awi-modal-backdrop');
    overlay.dataset.tone = tone;

    const panel = createEl('section', 'awi-modal');
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-modal', 'true');
    panel.setAttribute('aria-label', title);

    const top = createEl('div', 'awi-modal-top');
    const icon = createEl('div', `awi-modal-icon awi-modal-icon-${tone}`, tone === 'danger' ? '!' : tone === 'warning' ? '!' : tone === 'success' ? '✓' : 'i');
    const copy = createEl('div', 'awi-modal-copy');
    copy.append(createEl('h3', 'awi-modal-title', title), createEl('p', 'awi-modal-message', message));
    top.append(icon, copy);
    panel.appendChild(top);

    let input = null;
    if (inputValue !== null) {
      const inputWrap = createEl('div', 'awi-modal-input-wrap');
      input = createEl('input', 'awi-modal-copy-input');
      input.type = 'text';
      input.readOnly = true;
      input.value = inputValue;
      input.setAttribute('aria-label', 'Teks yang akan disalin');
      input.addEventListener('focus', () => input.select());
      input.addEventListener('click', () => input.select());
      inputWrap.appendChild(input);
      panel.appendChild(inputWrap);
    }

    const actions = createEl('div', 'awi-modal-actions');
    let cancelButton = null;
    if (cancelText) {
      cancelButton = createEl('button', 'btn btn-ghost', cancelText);
      cancelButton.type = 'button';
      actions.appendChild(cancelButton);
    }
    const confirmButton = createEl('button', tone === 'danger' ? 'btn btn-danger' : 'btn btn-primary', confirmText);
    confirmButton.type = 'button';
    actions.appendChild(confirmButton);
    panel.appendChild(actions);
    overlay.appendChild(panel);
    document.body.appendChild(overlay);
    document.body.classList.add('awi-modal-open');

    let settled = false;
    const finish = (value) => {
      if (settled) return;
      settled = true;
      document.removeEventListener('keydown', onKeydown);
      overlay.classList.remove('show');
      setTimeout(() => overlay.remove(), 170);
      document.body.classList.remove('awi-modal-open');
      resolve(value);
    };
    const onKeydown = (event) => {
      if (event.key === 'Escape' && cancelText) finish(false);
    };

    cancelButton?.addEventListener('click', () => finish(false));
    confirmButton.addEventListener('click', () => finish(true));
    overlay.addEventListener('click', event => {
      if (event.target === overlay && cancelText) finish(false);
    });
    document.addEventListener('keydown', onKeydown);

    requestAnimationFrame(() => {
      overlay.classList.add('show');
      if (input) {
        input.focus();
        input.select();
      } else {
        confirmButton.focus();
      }
    });
  });

  const uiAlert = (message, options = {}) => dialog({
    title: options.title || 'Informasi',
    message,
    tone: options.tone || 'info',
    confirmText: options.confirmText || 'Mengerti',
  });

  const uiConfirm = (message, options = {}) => dialog({
    title: options.title || 'Konfirmasi Tindakan',
    message,
    tone: options.tone || 'warning',
    confirmText: options.confirmText || 'Ya, Lanjutkan',
    cancelText: options.cancelText || 'Batal',
  });

  window.AwiUI = { alert: uiAlert, confirm: uiConfirm, toast };

  const sidebar = document.getElementById('sidebar');
  const backdrop = document.querySelector('[data-sidebar-close]');

  const menuButton = document.querySelector('[data-sidebar-open]');
  const closeSidebar = () => {
    sidebar?.classList.remove('open');
    backdrop?.classList.remove('show');
    menuButton?.setAttribute('aria-expanded', 'false');
  };
  const openSidebar = () => {
    sidebar?.classList.add('open');
    backdrop?.classList.add('show');
    menuButton?.setAttribute('aria-expanded', 'true');
  };
  menuButton?.addEventListener('click', openSidebar);
  backdrop?.addEventListener('click', closeSidebar);
  document.querySelector('[data-sidebar-close-button]')?.addEventListener('click', closeSidebar);
  sidebar?.querySelectorAll('a[href]')?.forEach(link => link.addEventListener('click', closeSidebar));
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeSidebar(); });
  window.addEventListener('resize', () => { if (window.innerWidth > 1100) closeSidebar(); });

  const reportMode = document.querySelectorAll('[data-report-mode]');
  const reportMonth = document.querySelector('[data-report-month]');
  const syncReportMode = () => {
    const isMonthly = document.querySelector('[data-report-mode]:checked')?.value !== 'year';
    if (reportMonth) {
      reportMonth.hidden = !isMonthly;
      reportMonth.querySelectorAll('select').forEach(select => { select.disabled = !isMonthly; });
    }
  };
  reportMode.forEach(radio => radio.addEventListener('change', syncReportMode));
  syncReportMode();
  document.querySelector('[data-print-report]')?.addEventListener('click', () => window.print());

  document.querySelectorAll('form[data-submit-lock]').forEach(form => {
    form.addEventListener('submit', (event) => {
      const submitter = event.submitter;
      if (submitter?.name) {
        let hidden = form.querySelector(`input[type="hidden"][data-submitter="${submitter.name}"]`);
        if (!hidden) {
          hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = submitter.name;
          hidden.dataset.submitter = submitter.name;
          form.appendChild(hidden);
        }
        hidden.value = submitter.value;
      }
      form.querySelectorAll('button[type="submit"], button:not([type])').forEach(button => {
        if (button.dataset.noLock !== '1') {
          button.disabled = true;
          button.dataset.originalText = button.textContent;
          button.textContent = 'Menyimpan…';
        }
      });
    });
  });

  document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', async event => {
    event.preventDefault();

    const message = el.getAttribute('data-confirm') || 'Lanjutkan tindakan ini?';
    const tone = el.getAttribute('data-confirm-tone') || (el.classList.contains('btn-danger') ? 'danger' : 'warning');
    const approved = await uiConfirm(message, {
      title: el.getAttribute('data-confirm-title') || (tone === 'danger' ? 'Konfirmasi Aksi Sensitif' : 'Konfirmasi Tindakan'),
      tone,
      confirmText: el.getAttribute('data-confirm-text') || (tone === 'danger' ? 'Ya, Lanjutkan' : 'Ya, Lanjutkan'),
      cancelText: el.getAttribute('data-cancel-text') || 'Batal',
    });

    if (!approved) return;

    const form = el.closest('form');
    if (form) {
      if (typeof form.requestSubmit === 'function') form.requestSubmit(el);
      else form.submit();
      return;
    }

    if (el.matches('a[href]')) window.location.assign(el.href);
  }));

  const legacyCopy = (text) => {
    const area = document.createElement('textarea');
    area.value = text;
    area.setAttribute('readonly', '');
    area.style.position = 'fixed';
    area.style.opacity = '0';
    area.style.pointerEvents = 'none';
    document.body.appendChild(area);
    area.select();
    let copied = false;
    try { copied = document.execCommand('copy'); } catch { copied = false; }
    area.remove();
    return copied;
  };

  document.querySelectorAll('[data-copy]').forEach(btn => btn.addEventListener('click', async () => {
    const text = btn.getAttribute('data-copy') || '';
    let copied = false;
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(text);
        copied = true;
      }
    } catch { copied = false; }

    if (!copied) copied = legacyCopy(text);

    if (copied) {
      toast('Link berhasil disalin.', 'success');
      return;
    }

    await dialog({
      title: 'Salin Link',
      message: 'Browser tidak mengizinkan salin otomatis. Pilih teks berikut lalu tekan Ctrl+C.',
      tone: 'info',
      confirmText: 'Tutup',
      inputValue: text,
    });
  }));

  document.querySelectorAll('[data-slider]').forEach(input => {
    const output = document.querySelector(input.dataset.slider);
    const sync = () => { if (output) output.textContent = `${input.value}%`; };
    input.addEventListener('input', sync); sync();
  });

  const toggleConditionalBlock = (wrap, visible) => {
    if (!wrap) return;
    wrap.hidden = !visible;
    wrap.querySelectorAll('input, select, textarea').forEach(field => {
      field.disabled = !visible;
      if (field.hasAttribute('data-required-when-visible')) field.required = visible;
    });
  };

  const serviceSelect = document.querySelector('[data-service-select]');
  const otherServiceWrap = document.querySelector('[data-other-service]');
  const syncService = () => {
    if (!serviceSelect || !otherServiceWrap) return;
    const text = serviceSelect.options[serviceSelect.selectedIndex]?.text?.trim().toLowerCase();
    toggleConditionalBlock(otherServiceWrap, text === 'lainnya');
  };
  serviceSelect?.addEventListener('change', syncService); syncService();

  const paymentMethod = document.querySelector('[data-payment-method]');
  const otherPayment = document.querySelector('[data-other-payment]');
  const syncPaymentMethod = () => {
    if (!paymentMethod || !otherPayment) return;
    toggleConditionalBlock(otherPayment, paymentMethod.value === 'Lainnya');
  };
  paymentMethod?.addEventListener('change', syncPaymentMethod); syncPaymentMethod();

  document.querySelectorAll('[data-status-stepper]').forEach(stepper => {
    const input = stepper.querySelector('[data-status-input]');
    const label = stepper.querySelector('[data-status-label]');
    const prev = stepper.querySelector('[data-status-prev]');
    const next = stepper.querySelector('[data-status-next]');
    const statuses = [
      ['menunggu', 'Menunggu'],
      ['dikerjakan', 'Dikerjakan'],
      ['revisi', 'Revisi'],
      ['selesai', 'Selesai'],
      ['dibatalkan', 'Dibatalkan'],
    ];
    let index = Math.max(0, statuses.findIndex(([value]) => value === input?.value));
    const sync = () => {
      if (!input || !label) return;
      input.value = statuses[index][0];
      label.textContent = statuses[index][1];
      if (prev) prev.disabled = index === 0;
      if (next) next.disabled = index === statuses.length - 1;
    };
    prev?.addEventListener('click', () => { if (index > 0) { index -= 1; sync(); } });
    next?.addEventListener('click', () => { if (index < statuses.length - 1) { index += 1; sync(); } });
    sync();
  });

  const percentInput = document.querySelector('[data-payment-percent]');
  const amountInput = document.querySelector('[data-payment-amount]');
  if (percentInput && amountInput) percentInput.addEventListener('input', () => {
    const total = Number(percentInput.dataset.total || 0), pct = Math.max(0, Math.min(100, Number(percentInput.value || 0)));
    amountInput.value = Math.round(total * pct / 100);
  });

  const itemContainer = document.querySelector('[data-item-container]');
  const itemTemplate = document.querySelector('#item-template');
  const addItem = document.querySelector('[data-add-item]');
  const discountType = document.querySelector('[data-discount-type]');
  const discountValue = document.querySelector('[data-discount-value]');
  const discountValueWrap = document.querySelector('[data-discount-value-wrap]');
  const subtotalOut = document.querySelector('[data-summary-subtotal]');
  const discountOut = document.querySelector('[data-summary-discount]');
  const discountRow = document.querySelector('[data-summary-discount-row]');
  const totalOut = document.querySelector('[data-summary-total]');
  const money = n => 'Rp' + Math.round(Number(n || 0)).toLocaleString('id-ID');

  const recalcItems = () => {
    if (!itemContainer) return;
    let subtotal=0;
    itemContainer.querySelectorAll('.item-row').forEach((row,index) => {
      row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`));
      const qty=Number(row.querySelector('[data-item-qty]')?.value||0), price=Number(row.querySelector('[data-item-price]')?.value||0), line=qty*price;
      subtotal += line;
      const lineOut=row.querySelector('[data-item-total]'); if(lineOut) lineOut.textContent=money(line);
      const label=row.querySelector('[data-item-number]'); if(label) label.textContent=`Item ${index+1}`;
    });
    const type=discountType?.value||'', value=Number(discountValue?.value||0);
    const hasDiscount = type === 'percent' || type === 'nominal';
    if (discountValueWrap) discountValueWrap.hidden = !hasDiscount;
    if (discountValue) discountValue.disabled = !hasDiscount;
    if (discountRow) discountRow.hidden = !hasDiscount;
    let discount=0;
    if(type==='percent') discount=subtotal*Math.min(100,Math.max(0,value))/100;
    if(type==='nominal') discount=Math.min(subtotal,Math.max(0,value));
    if(subtotalOut) subtotalOut.textContent=money(subtotal);
    if(discountOut) discountOut.textContent='- '+money(discount);
    if(totalOut) totalOut.textContent=money(Math.max(0,subtotal-discount));
  };

  addItem?.addEventListener('click', () => {
    if (!itemTemplate || !itemContainer) return;
    const index=itemContainer.querySelectorAll('.item-row').length;
    const html=itemTemplate.innerHTML.replaceAll('__INDEX__',String(index));
    itemContainer.insertAdjacentHTML('beforeend',html); recalcItems();
  });
  itemContainer?.addEventListener('click', async event => {
    const button=event.target.closest('[data-remove-item]'); if(!button) return;
    if(itemContainer.querySelectorAll('.item-row').length<=1){
      await uiAlert('Transaksi minimal harus memiliki satu item jasa.', { title: 'Item Tidak Dapat Dihapus', tone: 'warning' });
      return;
    }
    button.closest('.item-row')?.remove(); recalcItems();
  });
  itemContainer?.addEventListener('input', recalcItems);
  discountType?.addEventListener('change', recalcItems); discountValue?.addEventListener('input', recalcItems); recalcItems();
})();
