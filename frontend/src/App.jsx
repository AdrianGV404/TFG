import { useState } from 'react';

function App() {
  const [apiResponse, setApiResponse] = useState(null);

  const fetchDataFromDjango = async () => {
    try {
      const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/test/`);
      const data = await response.json();
      setApiResponse(data);
    } catch (error) {
      setApiResponse({
        error: "Error al conectar con el backend",
        details: error.message
      });
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