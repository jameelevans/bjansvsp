export default function initPrintSection() {
  document.addEventListener('click', event => {
    const link = event.target.closest('.js-print-section');
    if (!link) return;
    const section = document.querySelector(link.dataset.print);
    if (!section) return;
    event.preventDefault();
    const clone = section.cloneNode(true);
    clone.querySelectorAll('details').forEach(item => item.open = true);
    clone.querySelectorAll('.js-print-section,.glossary__letters,.glossary__letter-bar').forEach(item => item.remove());
    const iframe = document.createElement('iframe');
    iframe.id = 'print-section-iframe';
    iframe.title = 'Printable section';
    iframe.setAttribute('aria-hidden', 'true');
    iframe.style.cssText = 'position:fixed;width:0;height:0;border:0;visibility:hidden';
    const logo = document.querySelector('.custom-logo-link');
    const stylesheet = document.querySelector('#bjansvsp_main_styles-css');
    const base = stylesheet ? new URL('.', stylesheet.href).href : location.href;
    const fontCss = [...document.styleSheets].flatMap(sheet => {
      try { return [...sheet.cssRules].filter(rule => rule.type === CSSRule.FONT_FACE_RULE).map(rule => rule.cssText); }
      catch (_) { return []; }
    }).join('\n');
    iframe.onload = async () => {
      await iframe.contentDocument.fonts.ready;
      await Promise.all([...iframe.contentDocument.images].map(img => img.decode().catch(() => {})));
      iframe.contentWindow.addEventListener('afterprint', () => iframe.remove(), {once: true});
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    };
    iframe.srcdoc = `<!doctype html><html lang="${escapeHtml(document.documentElement.lang)}"><head><meta charset="utf-8"><base href="${escapeHtml(base)}"><title>${escapeHtml(document.title)} — Print</title><style>${fontCss}\n${getPrintCss()}</style></head><body><header class="print-header">${logo?.outerHTML || escapeHtml(document.title)}</header><main class="print-content">${clone.outerHTML}</main></body></html>`;
    document.getElementById('print-section-iframe')?.remove();
    document.body.appendChild(iframe);
  });
}

function getPrintCss() {
  return `
    :root {
      --print-text: #111;
      --print-muted: #444;
      --print-border: #ddd;
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0;
      padding: 0;
      color: var(--print-text);
      font-family: 'Roboto', Arial, sans-serif;
    }

    .h2__icon{
      display: none;    
    }

    a { color: inherit; text-decoration: none; }

    .print-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      padding: 18px 22px;
      border-bottom: 1px solid var(--print-border);
    }

    .print-header__left a {
      display: inline-flex;
      align-items: center;
    }

    .print-header img {
      max-height: 42px;
      width: auto;
      display: block;
    }

    .print-header__title {
      font-weight: 700;
      font-size: 18px;
    }

    .print-header__meta {
      font-size: 12px;
      color: var(--print-muted);
    }

    .print-content {
      padding: 22px;
    }

    /* Hide elements flagged for print */
    [data-print-hide="true"] {
      display: none !important;
    }

    /* Typography */
    h1, h2, h3, h4 { font-family: 'Open Sans', Arial, sans-serif; }
    .faq__answer { display: block; max-height: none; opacity: 1; }
    h1 { font-size: 22px; margin: 0 0 10px; }
    h2 { font-size: 18px; margin: 18px 0 10px; }
    h3 { font-size: 15px; margin: 14px 0 6px; }
    p  { margin: 0 0 10px; line-height: 1.45; color: var(--print-muted); }

    /* FAQ formatting */
    details {
      border-bottom: 1px solid var(--print-border);
      padding: 10px 0;
    }

    summary {
      font-weight: 700;
      list-style: none;
      color: var(--print-text);
    }

    summary::-webkit-details-marker {
      display: none;
    }

    @media print {
      @page { margin: 14mm; }
    }
  `;
}

/**
 * Escape HTML for safe injection
 */
function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}