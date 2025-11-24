export function initGTM() {
  const gtmId = import.meta.env.VITE_GTM_ID;

  if (!gtmId) {
    console.warn('GTM ID no está definida.');
    return;
  }

  // Inicializar dataLayer
  window.dataLayer = window.dataLayer || [];

  // Insertar script de GTM
  const script = document.createElement('script');
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtm.js?id=${gtmId}`;
  document.head.appendChild(script);

  // Insertar noscript
  const noscript = document.createElement('noscript');
  const iframe = document.createElement('iframe');
  iframe.src = `https://www.googletagmanager.com/ns.html?id=${gtmId}`;
  iframe.height = '0';
  iframe.width = '0';
  iframe.style.display = 'none';
  iframe.style.visibility = 'hidden';
  noscript.appendChild(iframe);
  document.body.insertBefore(noscript, document.body.firstChild);
}

export function pushDataLayer(data) {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push(data);
}

export function trackPageView(pageName, pageUrl) {
  pushDataLayer({
    event: 'page_view_custom',
    page_name: pageName,
    page_url: pageUrl
  });
}

export function trackCTAClick(ctaName, ctaLocation) {
  pushDataLayer({
    event: 'cta_click',
    cta_name: ctaName,
    cta_location: ctaLocation
  });
}

export function trackSuccessRedeem(itemName, itemType) {
  pushDataLayer({
    event: 'success_redeem',
    item_name: itemName,
    item_type: itemType
  });
}

export function trackErrorRedeem(itemName, itemType, errorMessage) {
  pushDataLayer({
    event: 'error_redeem',
    item_name: itemName,
    item_type: itemType,
    error_message: errorMessage
  });
}
