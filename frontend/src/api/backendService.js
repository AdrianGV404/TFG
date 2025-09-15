// src/api/backendService.js
const API_BASE = import.meta.env.VITE_API_BASE_URL || ""; // si usas proxy, puedes dejar "" y usar rutas relativas

async function apiGet(path) {
  const url = `${API_BASE}${path}`;
  const response = await fetch(url, { credentials: "include" });
  if (!response.ok) {
    // intenta leer cuerpo para mensaje del backend
    let text;
    try {
      text = await response.text();
    } catch (e) {
      text = "";
    }
    const snippet = text ? ` - ${text}` : "";
    throw new Error(`Error ${response.status}${snippet}`);
  }
  return response.json();
}

/* ---------- Funciones de búsqueda (GET) ---------- */
export async function testBackendConnection() {
  return apiGet(`/api/test/`);
}

export async function search_by_title(titulo, page = 0) {
  // usa 'page' (la vista acepta también _page por compatibilidad)
  return apiGet(`/api/search/title/?title=${encodeURIComponent(titulo)}&page=${page}`);
}

export async function search_by_keyword(keyword, page = 0) {
  return apiGet(`/api/search/keyword/?keyword=${encodeURIComponent(keyword)}&page=${page}`);
}

export async function search_by_spatial(spatial_type, spatial_value, page = 0) {
  const encodedType = encodeURIComponent(spatial_type);
  const encodedValue = encodeURIComponent(spatial_value);
  return apiGet(`/api/search/spatial/?spatial_type=${encodedType}&spatial_value=${encodedValue}&page=${page}`);
}

export async function search_by_category(category, page = 0) {
  return apiGet(`/api/search/category/?category=${encodeURIComponent(category)}&page=${page}`);
}

/* ---------- Analizar dataset (GET) ---------- */
export async function analyze_dataset(datasetUrl, format = "", rows) {
  const url = new URL(`${API_BASE}/api/dataset/analyze/`, window.location.origin);
  url.searchParams.set("url", datasetUrl);
  if (format) url.searchParams.set("format", format);
  if (rows !== undefined) url.searchParams.set("rows", String(rows));
  const response = await fetch(url.toString(), { credentials: "include" });
  if (!response.ok) {
    let text = "";
    try { text = await response.text(); } catch (e) {}
    throw new Error(`Error ${response.status}${text ? ` - ${text}` : ""}`);
  }
  return response.json();
}

export async function resolve_distribution(url) {
  return apiGet(`/api/distribution/resolve/?url=${encodeURIComponent(url)}`);
}