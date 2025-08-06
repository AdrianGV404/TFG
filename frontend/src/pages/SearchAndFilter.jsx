import { useState } from "react";
import SearchComponent from "../components/SearchComponent";

function SearchAndFilter() {
  const [searchResults, setSearchResults] = useState([]);
  const [searchError, setSearchError] = useState(null);

  return (
    <div style={{ 
      padding: "20px", 
      backgroundColor: "#242424", 
      minHeight: "100vh", 
      color: "white" 
    }}>
      <h1>🔍 Buscar datasets desde datos.gob.es</h1>
      
      <div style={{ 
        maxWidth: "800px", 
        margin: "0 auto",
        backgroundColor: "#333",
        padding: "20px",
        borderRadius: "8px"
      }}>
        <SearchComponent 
          onResults={(results) => {
            setSearchResults(results || []);
            setSearchError(null);
          }} 
          onError={setSearchError}
        />
      </div>

      {searchError && (
        <div style={{ 
          color: "orange",
          backgroundColor: "#333",
          padding: "15px",
          borderRadius: "6px",
          margin: "20px auto",
          maxWidth: "800px"
        }}>
          {searchError}
        </div>
      )}

      <div style={{ 
        marginTop: "30px", 
        maxWidth: "800px", 
        margin: "0 auto" 
      }}>
        {searchResults.length > 0 ? (
          <div>
            <h3>Resultados de la búsqueda:</h3>
            <ul style={{ listStyle: "none", padding: 0 }}>
              {searchResults.map((item, i) => (
                <li
                  key={i}
                  style={{
                    marginBottom: "15px",
                    backgroundColor: "#444",
                    padding: "15px",
                    borderRadius: "6px",
                  }}
                >
                  <h4 style={{ margin: "0 0 10px 0" }}>
                    {item.processedTitle || "Dataset sin título"}
                  </h4>
                  {item.processedDescription && (
                    <p style={{ margin: "0 0 10px 0" }}>
                      {item.processedDescription.length > 200
                        ? `${item.processedDescription.substring(0, 200)}...`
                        : item.processedDescription}
                    </p>
                  )}
                  {item.processedLink && (
                    <a
                      href={item.processedLink}
                      target="_blank"
                      rel="noopener noreferrer"
                      style={{
                        color: "#646cff",
                        textDecoration: "none",
                        fontWeight: "bold",
                      }}
                    >
                      🔗 Ver dataset
                    </a>
                  )}
                </li>
              ))}
            </ul>
          </div>
        ) : (
          <p style={{ textAlign: "center" }}>
            {searchError ? "Error en la búsqueda" : "No hay resultados para mostrar"}
          </p>
        )}
      </div>
    </div>
  );
}

export default SearchAndFilter;