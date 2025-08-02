import { useState, useEffect } from 'react';
import { search_by_title, testBackendConnection, search_by_keyword, search_by_spatial } from "./api/backendService";

function App() {
  const [apiResponse, setApiResponse] = useState(null);
  const [searchType, setSearchType] = useState("title");
  const [formValues, setFormValues] = useState({
    title: "",
    keyword: "",
    spatial1: "Autonomia",
    spatial2: ""
  });
  const [validationMessage, setValidationMessage] = useState(null);
  const [searchResults, setSearchResults] = useState([]);
  const [searchStatus, setSearchStatus] = useState(null);
  const [isSearching, setIsSearching] = useState(false);
  const [currentPage, setCurrentPage] = useState(0);
  const [hasSearched, setHasSearched] = useState(false);
  const [totalPages, setTotalPages] = useState(null);  // Total páginas disponibles
  const [pageInputValue, setPageInputValue] = useState('1'); // Control para input numérico

  // Asumo que backend devuelve 10 items por página para el cálculo, ajusta si es necesario
  const ITEMS_PER_PAGE = 10;

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

  const getValue = (field) => {
    if (!field) return '';
    if (typeof field === 'string') return field;
    if (Array.isArray(field)) {
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

  const handleSearchTypeChange = (e) => {
    setSearchType(e.target.value);
    setValidationMessage(null);
    setFormValues({
      title: "",
      keyword: "",
      spatial1: "Autonomia",
      spatial2: ""
    });
    setSearchResults([]);
    setSearchStatus(null);
    setCurrentPage(0);
    setHasSearched(false);
    setTotalPages(null);
    setPageInputValue('1');
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;

    // Para spatial2, hacemos log para verificar el valor EXACTO que se captura (incluyendo ñ)
    if (name === "spatial2") {
      console.log("Spatial2 input:", value);
    }

    setFormValues(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const fetchSearchResults = async (page) => {
    if (!hasSearched && page !== 0) return;

    setValidationMessage(null);
    setSearchStatus("Buscando...");
    setIsSearching(true);

    try {
      if (searchType === "title" && !formValues.title.trim()) {
        throw new Error("Por favor, introduce un título para la búsqueda.");
      }
      if (searchType === "keyword" && !formValues.keyword.trim()) {
        throw new Error("Por favor, introduce una keyword para la búsqueda.");
      }
      if (searchType === "spatial" && !formValues.spatial2.trim()) {
        throw new Error("Por favor, introduce el nombre del espacio geográfico.");
      }

      let resultados;
      if (searchType === "title") {
        resultados = await search_by_title(formValues.title.trim(), page);
      } else if (searchType === "keyword") {
        resultados = await search_by_keyword(formValues.keyword.trim(), page);
      } else if (searchType === "spatial") {
          const spatialValue = formValues.spatial2.trim();
          resultados = await search_by_spatial(formValues.spatial1, spatialValue, page);
        }
        else {
        throw new Error("Tipo de búsqueda no implementado");
      }

      if (!resultados || typeof resultados !== 'object') {
        throw new Error("Respuesta inválida del servidor");
      }

      const items = Array.isArray(resultados.result?.items) ? resultados.result.items : [];
      setSearchResults(items);
      setSearchStatus(`✅ Búsqueda completada (${items.length} resultados)`);

      if (typeof resultados.items_count === 'number') {
        const total = Math.ceil(resultados.items_count / ITEMS_PER_PAGE);
        setTotalPages(total);
      } else {
        setTotalPages(null);
      }

    } catch (error) {
      console.error("Error en la búsqueda:", error);
      setSearchStatus(`❌ ${error.message}`);
      setValidationMessage(error.message);
      setSearchResults([]);
      setTotalPages(null);
    } finally {
      setIsSearching(false);
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setCurrentPage(0);
    setHasSearched(true);
    fetchSearchResults(0);
  };

  const handlePageChange = (newPage) => {
    if (newPage < 0) return;
    if (totalPages !== null && newPage >= totalPages) return;
    setCurrentPage(newPage);
  };

  useEffect(() => {
    setPageInputValue((currentPage + 1).toString());
  }, [currentPage]);

  useEffect(() => {
    if (hasSearched) {
      fetchSearchResults(currentPage);
    }
  }, [currentPage, hasSearched]);

  return (
    <div style={{
      textAlign: 'center',
      marginTop: '40px',
      minHeight: '100vh',
      backgroundColor: '#242424',
      padding: '20px'
    }}>
      <h1 style={{ color: 'white' }}>Frontend React funcionando!</h1>
      <p style={{ color: 'white' }}>Conectado al backend Django.</p>

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

      {apiResponse && (
        <div style={{ marginTop: '20px' }}>
          {apiResponse.error ? (
            <p style={{ color: 'red' }}>❌ {apiResponse.error}: {apiResponse.details}</p>
          ) : (
            <>
              <p style={{ color: 'white' }}>✅ {apiResponse.message}</p>
              <p style={{ color: 'white' }}>👤 Usuarios en la base de datos: <strong>{apiResponse.user_count}</strong></p>
            </>
          )}
        </div>
      )}

      <hr style={{ margin: '40px 0', borderColor: '#ddd' }} />
      <h2 style={{ color: 'white' }}>🔍 Buscar datasets desde datos.gob.es</h2>

      <form onSubmit={handleSubmit}>
        <label style={{ color: 'white' }}>
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
                spellCheck={true}
                autoCorrect="on"
                autoComplete="on"
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
      </form>

      {/* Paginación */}
      <div style={{ marginTop: '20px', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' }}>
        <button
          onClick={() => handlePageChange(Math.max(0, currentPage - 1))}
          disabled={currentPage === 0 || isSearching || !hasSearched}
          style={{
            padding: '8px 16px',
            backgroundColor: currentPage === 0 || isSearching || !hasSearched ? '#cccccc' : '#646cff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: currentPage === 0 || isSearching || !hasSearched ? 'not-allowed' : 'pointer'
          }}
        >
          Página anterior
        </button>

        {/* Input numérico para página */}
        <label style={{ color: 'white', display: 'flex', alignItems: 'center', gap: '5px' }}>
          Página:
          <input
            type="number"
            min={1}
            max={totalPages !== null ? totalPages : 1000}
            value={pageInputValue}
            onChange={(e) => {
              let val = e.target.value;
              if (val === '') {
                setPageInputValue('');
                return;
              }
              const num = Number(val);
              if (!isNaN(num) && num >= 1 && (totalPages === null || num <= totalPages)) {
                setPageInputValue(num.toString());
              }
            }}
            onBlur={() => {
              if (pageInputValue === '') {
                setPageInputValue((currentPage + 1).toString());
                return;
              }
              const pageNum = Number(pageInputValue);
              if (!isNaN(pageNum) && pageNum >= 1 && (totalPages === null || pageNum <= totalPages)) {
                if (pageNum - 1 !== currentPage) {
                  handlePageChange(pageNum - 1);
                }
              } else {
                setPageInputValue((currentPage + 1).toString());
              }
            }}
            onKeyDown={(e) => {
              if (e.key === 'Enter') {
                e.target.blur();
              }
            }}
            disabled={isSearching || !hasSearched}
            style={{
              width: '60px',
              padding: '6px',
              borderRadius: '4px',
              border: '1px solid #ccc',
              fontSize: '16px',
              textAlign: 'center'
            }}
          />
        </label>

        <button
          onClick={() => handlePageChange(currentPage + 1)}
          disabled={
            isSearching ||
            !hasSearched ||
            searchResults.length === 0 ||
            (totalPages !== null && currentPage + 1 >= totalPages)
          }
          style={{
            padding: '8px 16px',
            backgroundColor: (isSearching || !hasSearched || searchResults.length === 0 || (totalPages !== null && currentPage + 1 >= totalPages)) ? '#cccccc' : '#646cff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: (isSearching || !hasSearched || searchResults.length === 0 || (totalPages !== null && currentPage + 1 >= totalPages)) ? 'not-allowed' : 'pointer'
          }}
        >
          Página siguiente
        </button>
      </div>

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

      {searchStatus && (
        <p style={{
          marginTop: '10px',
          fontWeight: 'bold',
          color: searchStatus.includes("✅") ? "#4CAF50" : "#f44336",
          backgroundColor: '#333',
          padding: '5px 10px',
          borderRadius: '4px',
          display: 'inline-block'
        }}>
          {searchStatus}
        </p>
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
                  cursor: 'pointer'
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
      `}</style>
    </div>
  );
}

export default App;
