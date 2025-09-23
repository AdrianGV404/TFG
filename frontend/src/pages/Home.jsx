import { useState, useEffect } from "react";
import SearchComponent from "../components/SearchComponent";
import FuncionalidadesPanel from "../components/FuncionalidadesPanel";
import ProcessDatasetPanel from "../components/ProcessDatasetPanel";

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

// Mensajes según funcionalidad
function getInfoMessage(func) {
  switch (func) {
    case "Predicción":
      return "Selecciona 1 conjunto de datos para realizar una predicción.";
    case "Correlación de variables":
      return "Selecciona 2 conjuntos de datos para calcular la correlación.";
    case "Ver datos en gráficos":
      return "Selecciona 1 conjunto para visualizar en gráficos.";
    default:
      return "";
  }
}

// Límite de selección según funcionalidad
function getSelectLimit(func) {
  switch (func) {
    case "Predicción":
      return 1;
    case "Correlación de variables":
      return 2;
    case "Ver datos en gráficos":
      return 1;
    default:
      return 0;
  }
}

function Home() {
  const [categoriaSeleccionada, setCategoriaSeleccionada] = useState(null);
  const [searchResults, setSearchResults] = useState([]);
  const [searchError, setSearchError] = useState(null);
  const [hasSearched, setHasSearched] = useState(false);
  const [totalGeneral, setTotalGeneral] = useState(null);
  const [chartType, setChartType] = useState("line");


const [funcionalidadSeleccionada, setFuncionalidadSeleccionada] = useState("Ver datos en gráficos");
  // Cambiado: array de objetos completos seleccionados
  const [selectedItems, setSelectedItems] = useState([]);

  // Cambiar funcionalidad y reset selección
  const handleSelectFunc = (func) => {
    setFuncionalidadSeleccionada(func);
    setSelectedItems([]);
  };

  const [totalDatasets, setTotalDatasets] = useState(null);
  const [conteosPorCategoria, setConteosPorCategoria] = useState([]);

  // Para obtener ID único de un item (asegura que coincida en todo el código)
  const getItemId = (item, index = 0) => {
    return (
      item.identifier ||
      item.id ||
      item["@id"] ||
      (Array.isArray(item.distribution)
        ? item.distribution[0]?.accessURL || item.distribution[0]
        : item.distribution?.accessURL || item.distribution) ||
      `${item.title?.[0]?._value || item.processedTitle || "dataset"}-${index}`
    );
  };



  const handleResultCheck = (item, i = 0) => {
    const itemId = getItemId(item, i);
    const limit = getSelectLimit(funcionalidadSeleccionada);

    const exists = selectedItems.some((sel) => getItemId(sel) === itemId);
    if (exists) {
      setSelectedItems(prev => prev.filter((sel) => getItemId(sel) !== itemId));
      return;
    }

    if (limit > 0 && selectedItems.length >= limit) return;

    // elegir formato por defecto si hay varios
    let defaultFormat = "";
    if (Array.isArray(item.distribution) && item.distribution.length > 0) {
      const uniqueFormats = [...new Set(item.distribution.map(d => d.format?.value).filter(Boolean))];
      defaultFormat = uniqueFormats.length > 0 ? uniqueFormats[0] : "";
    }

    setSelectedItems(prev => [...prev, { ...item, format: defaultFormat }]);
  };




  const formatDate = (dateStr) => {
    const dateObj = new Date(dateStr);
    if (isNaN(dateObj)) return dateStr; // fallback si no parsea

    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    const hours = String(dateObj.getHours()).padStart(2, '0');
    const minutes = String(dateObj.getMinutes()).padStart(2, '0');

    return `${day}/${month}/${year} ${hours}:${minutes} GMT+1`;
  };


  // Remover desde lista visual
  const handleRemoveSelected = (item) => {
    const itemId = getItemId(item);
    setSelectedItems(selectedItems.filter(sel => getItemId(sel) !== itemId));
  };

  const handleResults = (results) => {
    setSearchResults(results || []);
    setSearchError(null);
    setHasSearched(true);
    // No borrar selectedItems para mantener seleccionados en múltiples páginas
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
      setSelectedItems([]);

      if (cat) {
        const encontrado = conteosPorCategoria.find(c => c.slug === cat);
        if (encontrado) {
          setTotalDatasets(encontrado.count);
        } else {
          setTotalDatasets(null);
        }
      } else {
        setTotalDatasets(totalGeneral);
      }
    };

    const getAvailableFormats = async (item) => {
      if (!item.distribution) return [];
      const distUrls = Array.isArray(item.distribution) ? item.distribution : [item.distribution];

      let results = [];
      for (const url of distUrls) {
        try {
          const data = await resolve_distribution(url);
          results = results.concat(data.files);
        } catch (e) {
          console.error("Error resolviendo distribución", e);
        }
      }
      return results;
    };

    useEffect(() => {
    fetch("/api/stats/total-datasets/")
      .then(res => res.json())
      .then(data => {
        if (data.total_datasets !== undefined) {
          setTotalGeneral(data.total_datasets);
          setTotalDatasets(data.total_datasets);
        }
      })
      .catch(err => console.error("Error al obtener total general:", err));
    }, []);

    useEffect(() => {
      fetch("/api/stats/dataset-counts-by-theme/")
        .then(res => res.json())
        .then(data => {
          if (data.themes) {
            const categoriasConSlug = data.themes.map(t => {
              const slugFromTheme = t.theme.split("/").pop(); // ← parte final de la URI
              return {
                ...t,
                slug: slugFromTheme.toLowerCase()
              };
            });
            setConteosPorCategoria(categoriasConSlug);
          }
        })
        .catch(err => console.error("Error al obtener conteos por categoría:", err));
    }, []);

  const selectedIdsSet = new Set(selectedItems.map(item => getItemId(item)));

  const downloadFile = async (url, filename) => {
  try {
    const response = await fetch(url);
    const blob = await response.blob();
    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename || 'dataset-descargado';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(downloadUrl);
    document.body.removeChild(a);
  } catch (error) {
    console.error('Error al descargar:', error);
    alert('Error al descargar el archivo');
  }
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
      {/* Sidebar categorías */}
      <div
        style={{
          width: 250,
          borderRight: "1px solid #444",
          padding: "20px",
          backgroundColor: "#1a1a1a",
          position: "sticky",
          top: 0,
          height: "100vh",
          overflowY: "auto",
          boxSizing: "border-box",
        }}
      >
        <h2 style={{ marginTop: 0, fontSize: "1.2rem", marginBottom: "1rem" }}>
          Categorías
        </h2>
        <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
          {categorias.map((cat) => (
            <li key={cat} style={{ marginBottom: "0.5rem" }}>
              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  backgroundColor:
                    categoriaSeleccionada === cat ? "#646cff" : "transparent",
                  color: categoriaSeleccionada === cat ? "white" : "#ccc",
                  borderRadius: "4px",
                }}
              >
                <button
                  onClick={() => handleCategoriaClick(cat)}
                  style={{
                    flex: 1,
                    background: "transparent",
                    border: "none",
                    padding: "8px 12px",
                    textTransform: "capitalize",
                    cursor: "pointer",
                    textAlign: "left",
                    fontSize: "1rem",
                    color: "inherit",
                  }}
                >
                  {cat.replace(/-/g, " ")}
                </button>

                {categoriaSeleccionada === cat && (
                  <button
                    onClick={() => handleCategoriaClick(null)}
                    style={{
                      background: "transparent",
                      border: "none",
                      color: "white",
                      cursor: "pointer",
                      padding: "0 8px",
                      fontSize: "1.2rem",
                    }}
                    title="Deseleccionar categoría"
                  >
                    ✖
                  </button>
                )}
              </div>
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
        }}
      >
        <h1 style={{ marginTop: 0 }}>Plataforma de Análisis de Datos Públicos</h1>
        <p>Selecciona una categoría o busca datasets para ver su información.</p>

        {totalDatasets !== null && (
          <div style={{
            margin: "10px 0 20px 0",
            padding: "10px",
            background: "#333",
            borderRadius: "6px",
            fontSize: "1.1rem",
            color: "#fff",
            fontWeight: "500"
          }}>
            Total de conjuntos en la base de datos pública: {totalDatasets.toLocaleString()}
          </div>
        )}
        
        {/* Aquí se ha movido el panel de funcionalidades */}
        <div style={{
            margin: "30px auto",
            backgroundColor: "#333",
            padding: "20px",
            borderRadius: "8px",
        }}>
            <h3 style={{marginTop: 0, color: "#89da5c"}}>Funcionalidades</h3>
            <FuncionalidadesPanel
                funcionalidadSeleccionada={funcionalidadSeleccionada}
                setFuncionalidadSeleccionada={handleSelectFunc}
            />
        </div>
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

                {/* Mensaje informativo */}
                {funcionalidadSeleccionada && (
                  <div
                    style={{
                      color: "#89da5c",
                      background: "#222",
                      padding: "11px 15px",
                      margin: "13px 0 22px 0",
                      borderRadius: 7,
                      fontWeight: 500,
                      fontSize: "1.04rem",
                      border: "1px solid #303",
                      boxShadow: "0 2px 10px 0 rgba(60,255,100,0.06)",
                      letterSpacing: ".01em",
                    }}
                  >
                    {getInfoMessage(funcionalidadSeleccionada)}
                  </div>
                )}

                <ul style={{ listStyle: "none", padding: 0 }}>
                  {searchResults.map((item, i) => {
                    const itemId = getItemId(item, i);
                    const limit = getSelectLimit(funcionalidadSeleccionada);
                    const isChecked = selectedIdsSet.has(itemId);
                    const isDisabled = !isChecked && selectedItems.length >= limit && limit !== Number.POSITIVE_INFINITY;

                    return (
                      <li
                        key={`${itemId}-${i}`} 
                        onClick={() => !isDisabled && handleResultCheck(item)}
                        style={{
                          marginBottom: "15px",
                          backgroundColor: isChecked ? "#39d353" : "#444",
                          color: isChecked ? "#111" : "#fafafa",
                          padding: "12px 18px",
                          borderRadius: "6px",
                          display: "flex",
                          flexDirection: "column",
                          alignItems: "flex-start",
                          cursor: isDisabled ? "not-allowed" : "pointer",
                          outline: isChecked ? "2px solid #222" : "none",
                          boxShadow: isChecked
                            ? "0 2px 12px 0 rgba(60,255,60,0.11)"
                            : "0 1px 6px 0 rgba(0,0,0,0.06)",
                          border: isChecked ? "2px solid #52df67" : "2px solid transparent",
                          transition: "background-color 0.25s, box-shadow 0.25s, border 0.2s",
                          userSelect: "none",
                        }}
                        tabIndex={isDisabled ? -1 : 0}
                        onKeyPress={(e) => {
                          if (!isDisabled && (e.key === " " || e.key === "Enter")) {
                            handleResultCheck(item);
                          }
                        }}
                      >
                        <div style={{ display: "flex", width: "100%", alignItems: "flex-start" }}>
                          <input
                            type="checkbox"
                            checked={isChecked}
                            disabled={isDisabled}
                            onClick={(e) => e.stopPropagation()} // evita doble disparo si hay onClick en <li>
                            onChange={() => {
                              if (!isDisabled) {
                                handleResultCheck(item); // marcar o desmarcar
                              }
                            }}
                            tabIndex={-1}
                            style={{
                              marginRight: "16px",
                              width: "28px",
                              height: "28px",
                              accentColor: isChecked ? "#222" : "#646cff",
                              cursor: isDisabled ? "not-allowed" : "pointer",
                              outline: "none",
                            }}
                          />
                          <div style={{ flex: 1 }}>
                            <h4
                              style={{
                                margin: "0 0 8px 0",
                                color: isChecked ? "#0e4028" : "#fafafa",
                                textShadow: isChecked ? "0px 1px 0px #aaffc9" : "none",
                              }}
                            >
                              {item.processedTitle || "Dataset sin título"}
                            </h4>
                            {item.modified && (
                              <p style={{
                                margin: "0 0 6px 0",
                                fontSize: "0.85rem",
                                color: isChecked ? "#0e4028" : "#ccc"
                              }}>
                                Última actualización: {formatDate(item.modified)}
                              </p>
                            )}
                            {item.processedDescription && (
                              <p
                                style={{
                                  margin: "0 0 10px 0",
                                  color: isChecked ? "#185026" : "#d1ffd6",
                                }}
                              >
                                {item.processedDescription.length > 200
                                  ? `${item.processedDescription.substring(0, 200)}...`
                                  : item.processedDescription}
                              </p>
                            )}
                          </div>
                        </div>
                        {/* Mostrar formatos del JSON (únicos) */}
                        {(() => {
                          if (!Array.isArray(item.distribution) || item.distribution.length === 0) {
                            return (
                              <span style={{
                                fontSize: "0.85rem",
                                color: "#aaa",
                                paddingLeft: "44px",
                                marginTop: "10px"
                              }}>
                                Sin formatos detectables
                              </span>
                            );
                          }

                          const rawFormats = item.distribution.map(d => d.format?.value).filter(Boolean);
                          const uniqueFormats = [...new Set(rawFormats)];

                          return (
                            <div style={{
                              display: "flex",
                              flexWrap: "wrap",
                              gap: "8px",
                              marginTop: "10px",
                              width: "100%",
                              paddingLeft: "44px"
                            }}>
                              {uniqueFormats.map((rawFormat, idx) => {
                                const fmtLabel = rawFormat.includes("/") ? rawFormat.split("/").pop().toUpperCase() : rawFormat.toUpperCase();
                                return (
                                  <span
                                    key={`${itemId}-format-${idx}`}
                                    style={{
                                      backgroundColor:
                                        fmtLabel === "CSV" ? "#28a745" :
                                        fmtLabel === "JSON" ? "#6f42c1" :
                                        fmtLabel === "XML" ? "#fd7e14" : "#646cff",
                                      color: "white",
                                      padding: "4px 8px",
                                      borderRadius: "4px",
                                      fontSize: "0.8rem",
                                      textTransform: "uppercase",
                                      fontWeight: "bold"
                                    }}
                                  >
                                    {fmtLabel}
                                  </span>
                                );
                              })}
                            </div>
                          );
                        })()}
                      </li>
                    );
                  })}
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

      {/* Panel funcionalidades derecho con lista visual de seleccionados a la derecha */}
      {selectedItems.length > 0 && (
        <div
          style={{
            flex: "0 0 270px",  // <-- ancho fijo recomendado
            maxWidth: "300px",
            minWidth: "200px",
            borderLeft: "1px solid #444",
            padding: "16px 12px",
            backgroundColor: "#1a1a1a",
            position: "sticky",
            top: 0,
            height: "100vh",
            overflowY: "auto",
            boxSizing: "border-box",
            fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            display: "block"
          }}
        >  
          <div style={{
            backgroundColor: "#2a653d",
            padding: "16px 12px",
            borderRadius: 6,
            marginBottom: "15px"
          }}>
            <ProcessDatasetPanel
              selectedItems={selectedItems}
              chartType={chartType}
              setChartType={setChartType}
            />
          </div>
          <div
            style={{
              flex: "1 1 25%",
              maxHeight: "100%",
              overflowY: "auto",
              paddingLeft: 10,
              color: "#ccc",
            }}
          >
            <h3 style={{ marginTop: 0, marginBottom: "1rem", color: "#89da5c", fontSize: "1.35rem" }}>
              Seleccionados
            </h3>
            {selectedItems.length === 0 ? (
              <p style={{ fontStyle: "italic", color: "#666", fontSize: "1.1rem" }}>
                No hay conjuntos seleccionados
              </p>
            ) : (
              <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
                {selectedItems.map((item, i) => {
                  const itemId = getItemId(item, i);
                  return (
                    <li
                      key={`${itemId}-${i}`} 
                      style={{
                        backgroundColor: "#2a653d",
                        marginBottom: "10px",
                        padding: "14px 16px",
                        borderRadius: 6,
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        cursor: "default",
                      }}
                    >
                      <span
                        style={{
                          fontSize: "1.2rem",
                          fontWeight: "600",
                          color: "#b6f5a5",
                          wordBreak: "break-word", // parte palabras largas
                          whiteSpace: "normal",    // permite salto de línea
                        }}
                      >
                        {item.processedTitle || "Conjunto sin título"}
                        {item.format && (
                          <span style={{ fontSize: "0.9rem", marginLeft: "6px", color: "#eee" }}>
                            ({item.format.toUpperCase()})
                          </span>
                        )}
                      </span>
                      <button
                        onClick={() => handleRemoveSelected(item)}
                        style={{
                          background: "transparent",
                          border: "none",
                          color: "#eee",
                          fontWeight: "bold",
                          cursor: "pointer",
                          fontSize: "1.4rem",
                          lineHeight: "1",
                          padding: 0,
                          marginLeft: 8,
                          userSelect: "none",
                          transition: "color 0.25s ease",
                        }}
                        aria-label={`Quitar ${item.processedTitle || "conjunto"}`}
                        title={`Quitar ${item.processedTitle || "conjunto"}`}
                        onMouseEnter={(e) => (e.currentTarget.style.color = "#ff6b6b")}
                        onMouseLeave={(e) => (e.currentTarget.style.color = "#eee")}
                      >
                        &times;
                      </button>
                    </li>
                  );
                })}
              </ul>
            )}
          </div>
        </div>
      )}
    </div>
  );
}

export default Home;