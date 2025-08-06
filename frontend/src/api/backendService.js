export async function testBackendConnection() {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/test/`);
  if (!response.ok) throw new Error("Error al conectar con el backend");
  return await response.json();
}

export async function search_by_title(titulo, page = 0) {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/title/?title=${encodeURIComponent(titulo)}&_page=${page}`);
  if (!response.ok) throw new Error("Error al buscar datasets por título");
  return await response.json();
}

export async function search_by_keyword(keyword, page = 0) {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/keyword/?keyword=${encodeURIComponent(keyword)}&_page=${page}`);
  if (!response.ok) throw new Error("Error al buscar datasets por keyword");
  return await response.json();
}

export async function search_by_spatial(spatial_type, spatial_value, page = 0) {
  const encodedType = encodeURIComponent(spatial_type);
  const encodedValue = encodeURIComponent(spatial_value);    
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/spatial/?spatial_type=${encodedType}&spatial_value=${encodedValue}&_page=${page}`);
  if (!response.ok) throw new Error("Error al buscar datasets por spatial");
  return await response.json();
}

export async function search_by_category(category, page = 0) {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/category/?category=${category}&page=${page}`);
  if (!response.ok) throw new Error("Error al buscar por categoría");
  return await response.json();
}