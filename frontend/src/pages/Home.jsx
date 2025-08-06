import { useState } from "react";
import SearchComponent from "../components/SearchComponent";
import FuncionalidadesPanel from "../components/FuncionalidadesPanel";

const categorias = [
  "sector-publico",
  "empleo",
  "demografia",
  "sociedad-bienestar",
  "educacion",
  "medio-ambiente",
  "economia",
  "salud",
  "hacienda",
  "legislacion-justicia",
  "turismo",
  "medio-rural-pesca",
  "vivienda",
  "transporte",
  "ciencia-tecnologia",
  "urbanismo-infraestructuras",
  "cultura-ocio",
  "comercio",
  "seguridad",
  "industria",
  "energia",
  "deporte",
];

function Home() {
  const [categoriaSeleccionada, setCategoriaSeleccionada] = useState(null);
  const [searchResults, setSearchResults] = useState([]);
  const [searchError, setSearchError] = useState(null);
  const [hasSearched, setHasSearched] = useState(false);

  const handleResults = (results) => {
    setSearchResults(results || []);
    setSearchError(null);
    setHasSearched(true);
  };

  const handleError = (error) => {
    setSearchError(error);
    setSearchResults([]);
    setHasSearched(true);
  };

  const handleCategoriaClick = (cat) => {
    setCategoriaSeleccionada(cat);
    setSearchResults([]);
    setSearchError(null);
    setHasSearched(false);
  };

  return (
    <div
      style={{
        display: "flex",
        minHeight: "100vh",
        backgroundColor: "#242424",
        color: "white",
      }}
    >
      {/* Sidebar categorías con sticky */}
      <div
        style={{
          width: 250,
          borderRight: "1px solid #444",
          padding: "20px",
          backgroundColor: "#1a1a1a",

          position: "sticky",
          top: 0,
          height: "100vh",
          overflowY: "auto", // para scroll interno si contenido se pasa
          boxSizing: "border-box",
        }}
      >
        <h2 style={{ marginTop: 0, fontSize: "1.2rem", marginBottom: "1rem" }}>
          Categorías
        </h2>
        <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
          {categorias.map((cat) => (
            <li key={cat} style={{ marginBottom: "0.5rem" }}>
              <button
                onClick={() => handleCategoriaClick(cat)}
                style={{
                  width: "100%",
                  backgroundColor:
                    categoriaSeleccionada === cat ? "#646cff" : "transparent",
                  color: categoriaSeleccionada === cat ? "white" : "#ccc",
                  border: "none",
                  padding: "8px 12px",
                  borderRadius: "4px",
                  textTransform: "capitalize",
                  cursor: "pointer",
                  textAlign: "left",
                  fontSize: "1rem",
                  transition: "background-color 0.3s ease",
                }}
              >
                {cat.replace(/-/g, " ")}
              </button>
            </li>
          ))}
        </ul>
      </div>

      {/* Contenido principal */}
      <main
        style={{
          flex: 1,
          padding: "40px",
          backgroundColor: "#242424",
          overflowY: "auto",
          maxWidth: 800,
        }}
      >
        <h1 style={{ marginTop: 0 }}>📊 Plataforma de Análisis de Datos Públicos</h1>
        <p>Selecciona una categoría o busca datasets para ver su información.</p>

        <div
          style={{
            margin: "30px auto",
            backgroundColor: "#333",
            padding: "20px",
            borderRadius: "8px",
          }}
        >
          <SearchComponent
            categoria={categoriaSeleccionada}
            onResults={handleResults}
            onError={handleError}
          />
        </div>

        {searchError && (
          <div
            style={{
              color: "orange",
              backgroundColor: "#333",
              padding: "15px",
              borderRadius: "6px",
              margin: "20px auto",
              maxWidth: 800,
            }}
          >
            {searchError}
          </div>
        )}

        <div style={{ marginTop: "40px" }}>
          {hasSearched ? (
            searchResults.length > 0 ? (
              <div style={{ margin: "0 auto" }}>
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
              <p style={{ textAlign: "center" }}>No se encontraron resultados</p>
            )
          ) : categoriaSeleccionada ? (
            <div
              style={{
                margin: "30px auto",
                maxWidth: 800,
                backgroundColor: "#333",
                padding: "20px",
                borderRadius: "8px",
              }}
            >
              <h2 style={{ textTransform: "capitalize", marginTop: 0 }}>
                {categoriaSeleccionada.replace(/-/g, " ")}
              </h2>
              <p>Selecciona "Buscar datasets" para encontrar información en esta categoría.</p>
            </div>
          ) : (
            <p style={{ textAlign: "center" }}>
              Selecciona una categoría o realiza una búsqueda
            </p>
          )}
        </div>
      </main>

      {/* Panel funcionalidades derecho con sticky */}
      <div
        style={{
          width: 250,
          borderLeft: "1px solid #444",
          padding: "24px 20px",
          backgroundColor: "#1a1a1a",
          display: "flex",
          flexDirection: "column",
          justifyContent: "space-between",
          position: "sticky",
          top: 0,
          height: "100vh",
          overflowY: "auto",
          boxSizing: "border-box",
          fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
        }}
      >
        <FuncionalidadesPanel />
      </div>
    </div>
  );
}

export default Home;
