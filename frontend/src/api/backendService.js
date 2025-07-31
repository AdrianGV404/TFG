export async function testBackendConnection() {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/test/`);
  if (!response.ok) throw new Error("Error al conectar con el backend");
  return await response.json();
}

//---------------------------------------------------------------------------------------
export async function store_datasets_as_json() {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/`);
  if (!response.ok) {
    throw new Error("Error al descargar datasets desde el backend");
  }
  return response.json();
}
//---------------------------------------------------------------------------------------

export async function search_by_title(titulo) {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/search/title/?title=${encodeURIComponent(titulo)}`);
  if (!response.ok) throw new Error("Error al buscar datasets por título");
  return await response.json();
}