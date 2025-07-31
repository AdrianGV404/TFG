import { useState } from 'react';
import { store_datasets_as_json, search_by_title, testBackendConnection } from "./api/backendService";

function App() {
  const [apiResponse, setApiResponse] = useState(null);
  const [downloadStatus, setDownloadStatus] = useState(null);
  const [searchType, setSearchType] = useState("title");
  const [formValues, setFormValues] = useState({
    title: "",
    keyword: "",
    spatial1: "",
    spatial2: "" 
  });
  const [validationMessage, setValidationMessage] = useState(null);
  const [searchResults, setSearchResults] = useState([]);
  const [searchStatus, setSearchStatus] = useState(null);
  const [isSearching, setIsSearching] = useState(false);

  const inputStyle = {
    padding: '10px 15px',
    fontSize: '16px',
    borderRadius: '4px',
    border: '1px solid #ccc',
    width: 'calc(100% - 30px)',
    maxWidth: '300px', 
    boxSizing: 'border-box',
  };

  const spatialInputStyle = {
    ...inputStyle,
    width: 'auto', 
  };

  // Función para extraer el valor de un campo que puede ser string o objeto con _value
  const getValue = (field) => {
    if (!field) return '';
    if (typeof field === 'string') return field;
    if (Array.isArray(field)) {
      // Buscar la versión en español primero, luego inglés, luego el primero disponible
      const spanish = field.find(item => item._lang === 'es');
      const english = field.find(item => item._lang === 'en');
      return spanish?._value || english?._value || field[0]?._value || '';
    }
    return field._value || '';
  };

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

  const downloadData = async () => {
    setDownloadStatus("Descargando...");
    try {
      const result = await store_datasets_as_json();
      setDownloadStatus(result.message || "✅ Descarga completada");
    } catch (error) {
      setDownloadStatus(`❌ Error: ${error.message}`);
    }
  };

  const handleSearchTypeChange = (e) => {
    setSearchType(e.target.value);
    setValidationMessage(null);
    setFormValues({
      title: "",
      keyword: "",
      spatial1: "",
      spatial2: ""
    });
    setSearchResults([]);
    setSearchStatus(null);
  };

  const handleInputChange = (e) => {
    setFormValues({
      ...formValues,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setValidationMessage(null);
    setSearchStatus("Buscando...");
    setIsSearching(true);
    setSearchResults([]);

    try {
      if (searchType === "title" && !formValues.title.trim()) {
        throw new Error("Por favor, introduce un título para la búsqueda.");
      }

      let resultados;
      if (searchType === "title") {
        resultados = await search_by_title(formValues.title.trim());
        
        if (!resultados || typeof resultados !== 'object') {
          throw new Error("Respuesta inválida del servidor");
        }

        const items = Array.isArray(resultados.result?.items) ? resultados.result.items : [];
        setSearchResults(items);
        setSearchStatus(`✅ Búsqueda completada (${items.length} resultados)`);
      } else {
        throw new Error("Tipo de búsqueda no implementado");
      }
      
    } catch (error) {
      console.error("Error en la búsqueda:", error);
      setSearchStatus(`❌ ${error.message}`);
      setValidationMessage(error.message);
    } finally {
      setIsSearching(false);
    }
  };

  return (
    <div style={{ 
      textAlign: 'center', 
      marginTop: '40px', 
      minHeight: '100vh',
      backgroundColor: '#242424',
      padding: '20px'
    }}>
      <h1>Frontend React funcionando!</h1>
      <p>Conectado al backend Django.</p>

      <button 
        onClick={fetchDataFromDjango}
        style={{
          margin: '10px',
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
        onClick={downloadData}
        style={{
          margin: '10px',
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
          marginTop: '10px',
          fontWeight: 'bold',
          color: downloadStatus.includes("✅") ? "#4CAF50" : downloadStatus.includes("❌") ? "#f44336" :"#555",
          backgroundColor: downloadStatus === "Descargando..." ? "#e0e0e0" : "transparent",
          padding: downloadStatus === "Descargando..." ? '5px 10px' : '0',
          borderRadius: '4px',
          display: 'inline-block',
          minWidth: '200px',
          transition: 'color 0.3s ease'
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

      <hr style={{ margin: '40px 0', borderColor: '#ddd' }} />
      <h2>🔍 Buscar datasets desde datos.gob.es</h2>

      <form onSubmit={handleSubmit}>
        <label>
          Tipo de búsqueda:&nbsp;
          <select 
            value={searchType} 
            onChange={handleSearchTypeChange} 
            style={{ ...inputStyle, width: 'auto' }}
            disabled={isSearching}
          >
            <option value="title">Por título</option>
            <option value="keyword">Por keyword</option>
            <option value="spatial">Por dos SpatialWords</option>
          </select>
        </label>

        <div style={{ marginTop: '20px' }}>
          {searchType === "title" && (
            <input
              type="text"
              name="title"
              placeholder="Título del dataset"
              value={formValues.title}
              onChange={handleInputChange}
              style={inputStyle}
              disabled={isSearching}
            />
          )}

          {searchType === "keyword" && (
            <input
              type="text"
              name="keyword"
              placeholder="Keyword"
              value={formValues.keyword}
              onChange={handleInputChange}
              style={inputStyle}
              disabled={isSearching}
            />
          )}

          {searchType === "spatial" && (
            <>
              <select
                name="spatial1"
                value={formValues.spatial1}
                onChange={handleInputChange}
                style={{ ...spatialInputStyle, marginRight: "10px" }}
                disabled={isSearching}
              >
                <option value="Selecciona tipo">Selecciona tipo</option>
                <option value="Autonomia">Autonomía</option>
                <option value="Pais">País</option>
                <option value="Provincia">Provincia</option>
              </select>
              <input
                type="text"
                name="spatial2"
                placeholder="ej. Tarragona"
                value={formValues.spatial2}
                onChange={handleInputChange}
                style={spatialInputStyle}
                disabled={isSearching}
              />
            </>
          )}
        </div>

        {validationMessage && (
          <p style={{ color: 'orange', marginTop: '10px' }}>
            ⚠️ {validationMessage}
          </p>
        )}

        <button 
          type="submit" 
          style={{
            marginTop: '20px',
            padding: '10px 20px',
            backgroundColor: isSearching ? '#cccccc' : '#1976d2',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: isSearching ? 'not-allowed' : 'pointer'
          }}
          disabled={isSearching}
        >
          {isSearching ? "Buscando..." : "Buscar datasets"}
        </button>

        {searchStatus && (
          <p style={{
            marginTop: '10px',
            fontWeight: 'bold',
            color: searchStatus.includes("✅") ? "#4CAF50" : 
                  searchStatus.includes("❌") ? "#f44336" : "#555",
            backgroundColor: searchStatus === "Buscando..." ? "#e0e0e0" : "transparent",
            padding: '5px 10px',
            borderRadius: '4px',
            display: 'inline-block',
            minWidth: '200px'
          }}>
            {searchStatus}
          </p>
        )}
      </form>

      {isSearching && (
        <div style={{ margin: '20px auto', width: '50px' }}>
          <div style={{ 
            border: '4px solid #f3f3f3', 
            borderTop: '4px solid #3498db', 
            borderRadius: '50%', 
            width: '40px', 
            height: '40px', 
            animation: 'spin 1s linear infinite' 
          }}></div>
        </div>
      )}

      {searchResults.length > 0 && (
        <div style={{ 
          marginTop: '30px',
          textAlign: 'left', 
          maxWidth: '900px', 
          margin: '0 auto',
          backgroundColor: 'white',
          padding: '20px',
          borderRadius: '8px',
          boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
        }}>
          <h3 style={{ 
            color: '#333',
            borderBottom: '1px solid #eee',
            paddingBottom: '10px'
          }}>
            Resultados encontrados: {searchResults.length}
          </h3>
          <ul style={{ listStyle: 'none', padding: 0 }}>
            {searchResults.map((item, index) => {
              const title = getValue(item.title);
              const description = getValue(item.description);
              const link = getValue(item.distribution)?.accessURL || 
                          (Array.isArray(item.distribution) ? getValue(item.distribution[0]?.accessURL) : '');

              return (
                <li key={index} style={{ 
                  margin: '15px 0', 
                  padding: '20px', 
                  border: '1px solid #e0e0e0', 
                  borderRadius: '6px',
                  backgroundColor: '#fafafa',
                  transition: 'all 0.3s ease',
                  ':hover': {
                    boxShadow: '0 4px 8px rgba(0,0,0,0.1)',
                    transform: 'translateY(-2px)'
                  }
                }}>
                  <h4 style={{ 
                    marginTop: 0,
                    color: '#1976d2',
                    fontSize: '18px'
                  }}>
                    {title || 'Dataset sin título'}
                  </h4>
                  {description && (
                    <p style={{
                      color: '#555',
                      lineHeight: '1.5',
                      margin: '10px 0'
                    }}>
                      {description.length > 200 
                        ? `${description.substring(0, 200)}...` 
                        : description}
                    </p>
                  )}
                  {link && (
                    <a 
                      href={link} 
                      target="_blank" 
                      rel="noopener noreferrer"
                      style={{ 
                        color: '#1976d2', 
                        textDecoration: 'none',
                        fontWeight: 'bold',
                        display: 'inline-block',
                        marginTop: '10px'
                      }}
                    >
                      🔗 Ver dataset
                    </a>
                  )}
                </li>
              );
            })}
          </ul>
        </div>
      )}

      <style>{`
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        li:hover {
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
          transform: translateY(-2px);
        }
      `}</style>
    </div>
  );
}

export default App;