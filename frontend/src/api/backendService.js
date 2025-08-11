// src/api/backendService.js

const API_BASE = import.meta.env.VITE_API_BASE_URL || ""; // si usas proxy, puedes dejar "" y usar rutas relativas

/* ---------- Funciones de búsqueda ---------- */
export async function testBackendConnection() {
  const response = await fetch(`${API_BASE}/api/test/`, { credentials: "include" });
  if (!response.ok) throw new Error("Error al conectar con el backend");
  return await response.json();
}

export async function search_by_title(titulo, page = 0) {
  const response = await fetch(
    `${API_BASE}/api/search/title/?title=${encodeURIComponent(titulo)}&_page=${page}`,
    { credentials: "include" }
  );
  if (!response.ok) throw new Error("Error al buscar datasets por título");
  return await response.json();
}

export async function search_by_keyword(keyword, page = 0) {
  const response = await fetch(
    `${API_BASE}/api/search/keyword/?keyword=${encodeURIComponent(keyword)}&_page=${page}`,
    { credentials: "include" }
  );
  if (!response.ok) throw new Error("Error al buscar datasets por keyword");
  return await response.json();
}

export async function search_by_spatial(spatial_type, spatial_value, page = 0) {
  const encodedType = encodeURIComponent(spatial_type);
  const encodedValue = encodeURIComponent(spatial_value);
  const response = await fetch(
    `${API_BASE}/api/search/spatial/?spatial_type=${encodedType}&spatial_value=${encodedValue}&_page=${page}`,
    { credentials: "include" }
  );
  if (!response.ok) throw new Error("Error al buscar datasets por spatial");
  return await response.json();
}

export async function search_by_category(category, page = 0) {
  const response = await fetch(
    `${API_BASE}/api/search/category/?category=${encodeURIComponent(category)}&page=${page}`,
    { credentials: "include" }
  );
  if (!response.ok) throw new Error("Error al buscar por categoría");
  return await response.json();
}

export async function analyze_dataset(datasetUrl, format, rows) {
  const url = new URL(`${API_BASE}/api/dataset/analyze/`);
  url.searchParams.set("url", datasetUrl);
  url.searchParams.set("format", format);
  if (rows !== undefined) {
    url.searchParams.set("rows", rows);
  }
  const response = await fetch(url.toString(), { credentials: "include" });
  if (!response.ok) throw new Error(`Error al analizar dataset: ${response.status}`);
  return await response.json();
}
