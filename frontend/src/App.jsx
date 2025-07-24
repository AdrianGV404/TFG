import { useState } from 'react';
import { downloadDatasets, testBackendConnection } from "./api/backendService";

function App() {
  const [apiResponse, setApiResponse] = useState(null);
  const [downloadStatus, setDownloadStatus] = useState(null);

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

  const downloadGovernmentData = async () => {
    setDownloadStatus("Descargando...");
    try {
      const result = await downloadDatasets();
      setDownloadStatus(result.message || "✅ Descarga completada");
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
    </div>
  );
}

export default App;