import $ from 'jquery';

/**
 * PrintSection
 * ------------
 * Prints ONLY a target section (e.g. #glossary or #faqs)
 * using a hidden iframe.
 *
 * NO popups
 * NO new tabs
 * NO popup blockers
 *
 * Expected markup:
 * <a href="#" class="js-print-section" data-print="#glossary">Print this section</a>
 * <a href="#" class="js-print-section" data-print="#faqs">Print this section</a>
 */
export default function initPrintSection() {
  $(document).on('click', '.js-print-section', function (e) {
    e.preventDefault();

    const selector = $(this).data('print'); // "#glossary" or "#faqs"
    if (!selector) return;

    const $section = $(selector);
    if (!$section.length) {
      console.warn('[PrintSection] Target not found:', selector);
      return;
    }

    const html = buildPrintHtml($section);
    printViaIframe(html);
  });
}

/**
 * Build the full printable HTML document
 */
function buildPrintHtml($section) {
  const $clone = $section.clone(true, true);

  // FAQs: force all answers open
  $clone.find('details').attr('open', true);

  // Hide print links inside printed content
  $clone.find('.js-print-section').attr('data-print-hide', 'true');

  // Optional: hide glossary letter bar
  $clone.find('.glossary__letters, .glossary__letter-bar')
    .attr('data-print-hide', 'true');

  // Grab WP logo if present
  const $logo = $('.custom-logo-link').first();
  const logoHtml = $logo.length
    ? $logo.clone(true, true).prop('outerHTML')
    : '';

  const pageTitle = document.title || 'NSVSP';
  const printedOn = new Date().toLocaleDateString();

  return `<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>${escapeHtml(pageTitle)} — Print</title>
  <style>${getPrintCss()}</style>
</head>
<body>
  <header class="print-header">
    <div class="print-header__left">
      ${logoHtml || `<div class="print-header__title">${escapeHtml(pageTitle)}</div>`}
    </div>
    <div class="print-header__right">
      <div class="print-header__meta">Printed on ${escapeHtml(printedOn)}</div>
    </div>
  </header>

  <main class="print-content">
    ${$clone.prop('outerHTML') || ''}
  </main>
</body>
</html>`;
}

/**
 * Print using a hidden iframe (popup-proof)
 */
function printViaIframe(html) {
  // Remove any existing print iframe
  const existing = document.getElementById('print-section-iframe');
  if (existing) existing.remove();

  const iframe = document.createElement('iframe');
  iframe.id = 'print-section-iframe';
  iframe.style.position = 'fixed';
  iframe.style.right = '0';
  iframe.style.bottom = '0';
  iframe.style.width = '0';
  iframe.style.height = '0';
  iframe.style.border = '0';
  iframe.style.visibility = 'hidden';

  // Write full document via srcdoc
  iframe.srcdoc = html;

  document.body.appendChild(iframe);

  iframe.onload = () => {
    try {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    } catch (err) {
      console.error('[PrintSection] iframe print failed:', err);
    }
  };
}

/**
 * Print-only CSS (clean handout style)
 */
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
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
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