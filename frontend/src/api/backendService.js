export async function testBackendConnection() {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/test/`);
  if (!response.ok) throw new Error("Error al conectar con el backend");
  return await response.json();
}

export async function downloadDatasets() {
  const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/datasets/`);
  if (!response.ok) {
    throw new Error("Error al descargar datasets desde el backend");
  }
  return response.json();
}