import { useState } from "react";
import SearchComponent from "../components/SearchComponent";

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

  return (
    <div
      style={{
        display: "flex",
        minHeight: "100vh",
        backgroundColor: "#242424",
        color: "white",
      }}
    >
      {/* Barra lateral con categorías */}
      <nav
        style={{
          width: "250px",
          borderRight: "1px solid #444",
          padding: "20px",
          boxSizing: "border-box",
          overflowY: "auto",
          height: "100vh",
          position: "sticky",
          top: 0,
        }}
      >
        <h2 style={{ marginTop: 0, fontSize: "1.2rem", marginBottom: "1rem" }}>
          Categorías
        </h2>
        <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
          {categorias.map((cat) => (
            <li key={cat} style={{ marginBottom: "0.5rem" }}>
              <button
                onClick={() => setCategoriaSeleccionada(cat)}
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
                  transition: "background-color 0.3s",
                }}
              >
                {cat.replace(/-/g, " ")}
              </button>
            </li>
          ))}
        </ul>
      </nav>

      {/* Contenido principal */}
      <main style={{ flexGrow: 1, padding: "40px", textAlign: "center" }}>
        <h1>📊 Plataforma de Análisis de Datos Públicos</h1>
        <p>Selecciona una categoría o busca datasets para ver su información.</p>

        <SearchComponent
          onResults={setSearchResults}
          categoriaFiltro={categoriaSeleccionada}
        />

        {categoriaSeleccionada && (
          <div
            style={{
              marginTop: "30px",
              backgroundColor: "#333",
              borderRadius: "6px",
              padding: "20px",
            }}
          >
            <h2 style={{ textTransform: "capitalize" }}>
              {categoriaSeleccionada.replace(/-/g, " ")}
            </h2>
            <p>
              (Aquí mostrarás datos, gráficos o títulos provenientes de la API,
              filtrados por la categoría seleccionada.)
            </p>
          </div>
        )}

        <div style={{ marginTop: "40px", textAlign: "left" }}>
          {searchResults.length > 0 ? (
            <ul style={{ listStyle: "none", padding: 0 }}>
              {searchResults.map((item, i) => (
                <li
                  key={i}
                  style={{
                    marginBottom: "15px",
                    backgroundColor: "#444",
                    padding: "10px",
                    borderRadius: "6px",
                  }}
                >
                  {item.title || "Dataset sin título"}
                </li>
              ))}
            </ul>
          ) : (
            <p>No hay resultados para mostrar.</p>
          )}
        </div>
      </main>
    </div>
  );
}

export default Home;
