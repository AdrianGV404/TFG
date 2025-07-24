//# Punto de entrada
import { useState } from 'react';
import { testBackendConnection } from "./api/backendService";

function App() {
  const [apiResponse, setApiResponse] = useState(null);
  const [downloadStatus, setDownloadStatus] = useState(null); // Estado faltante

const fetchDataFromDjango = async () => {
  try {
    const data = await testBackendConnection();
    setApiResponse(data);
  } catch (error) {
    setApiResponse({
      error: "Error al conectar con el backend",
      details: error.message
    });
  }
};

  // Función para descargar datos del gobierno (faltaba implementación)
  const downloadGovernmentData = async () => {
    setDownloadStatus("Descargando...");
    try {
      // Ejemplo con API de datos abiertos (reemplaza con tu API real)
      const response = await fetch('https://api.datos.gob.mx/v1/ejemplo-api');
      const data = await response.json();
      
      // Crear archivo descargable
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'datos-gobierno.json';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      setDownloadStatus("✅ Descarga completada");
    } catch (error) {
      setDownloadStatus(`❌ Error: ${error.message}`);
    }
  };

  return (
    <div style={{ textAlign: 'center', marginTop: '50px' }}>
      <h1>Frontend React funcionando!</h1>
      <p>Conectado al backend Django.</p>
      
      <button 
        onClick={fetchDataFromDjango} 
        style={{ 
          margin: '20px', 
          padding: '10px',
          backgroundColor: '#646cff',
          color: 'white',
          border: 'none',
          borderRadius: '4px',
          cursor: 'pointer'
        }}
      >
        Probar conexión con Django y PostgreSQL
      </button>

      <button 
        onClick={downloadGovernmentData}
        style={{
          margin: '20px',
          padding: '10px',
          backgroundColor: '#4CAF50',
          color: 'white',
          border: 'none',
          borderRadius: '4px',
          cursor: 'pointer'
        }}
        disabled={downloadStatus === "Descargando..."}
      >
        {downloadStatus === "Descargando..." ? "Descargando..." : "Descargar Datos Gobierno"}
      </button>

      {downloadStatus && (
        <p style={{
          color: downloadStatus.includes("✅") ? "green" : downloadStatus.includes("❌") ? "red" : "black"
        }}>
          {downloadStatus}
        </p>
      )}

      {apiResponse && (
        <div style={{ marginTop: '20px' }}>
          {apiResponse.error ? (
            <p style={{ color: 'red' }}>❌ {apiResponse.error}: {apiResponse.details}</p>
          ) : (
            <>
              <p>✅ {apiResponse.message}</p>
              <p>👤 Usuarios en la base de datos: <strong>{apiResponse.user_count}</strong></p>
            </>
          )}
        </div>
      )}

      <a 
        href={import.meta.env.VITE_API_BASE_URL} 
        target="_blank" 
        rel="noopener noreferrer"
        style={{ display: 'block', marginTop: '20px' }}
      >
        Ver panel de administración Django
      </a>
    </div>
  );
}

export default App;