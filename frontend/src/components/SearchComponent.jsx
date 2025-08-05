// src/components/SearchComponent.jsx
import { useState } from "react";
import {
  search_by_title,
  search_by_keyword,
  search_by_spatial,
} from "../api/backendService";

const ITEMS_PER_PAGE = 10;

function SearchComponent({ onResults }) {
  const [searchType, setSearchType] = useState("title");
  const [formValues, setFormValues] = useState({
    title: "",
    keyword: "",
    spatial1: "Autonomia",
    spatial2: "",
  });
  const [validationMessage, setValidationMessage] = useState(null);
  const [searchStatus, setSearchStatus] = useState(null);
  const [isSearching, setIsSearching] = useState(false);
  const [currentPage, setCurrentPage] = useState(0);
  const [totalPages, setTotalPages] = useState(null);
  const [pageInputValue, setPageInputValue] = useState("1");
  const [hasSearched, setHasSearched] = useState(false);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues((prev) => ({ ...prev, [name]: value }));
    setValidationMessage(null);
  };

  const fetchSearchResults = async (page) => {
    setValidationMessage(null);
    setSearchStatus("Buscando...");
    setIsSearching(true);

    try {
      if (searchType === "title" && !formValues.title.trim())
        throw new Error("Por favor, introduce un título para la búsqueda.");
      if (searchType === "keyword" && !formValues.keyword.trim())
        throw new Error("Por favor, introduce una keyword para la búsqueda.");
      if (searchType === "spatial" && !formValues.spatial2.trim())
        throw new Error("Por favor, introduce el nombre del espacio geográfico.");

      let resultados;
      if (searchType === "title") {
        resultados = await search_by_title(formValues.title.trim(), page);
      } else if (searchType === "keyword") {
        resultados = await search_by_keyword(formValues.keyword.trim(), page);
      } else if (searchType === "spatial") {
        resultados = await search_by_spatial(formValues.spatial1, formValues.spatial2.trim(), page);
      } else {
        throw new Error("Tipo de búsqueda no implementado");
      }

      if (!resultados || typeof resultados !== "object")
        throw new Error("Respuesta inválida del servidor");

      const items = Array.isArray(resultados.result?.items) ? resultados.result.items : [];
      onResults(items);

      setSearchStatus(`✅ Búsqueda completada (${items.length} resultados)`);

      if (typeof resultados.items_count === "number") {
        setTotalPages(Math.ceil(resultados.items_count / ITEMS_PER_PAGE));
      } else {
        setTotalPages(null);
      }
    } catch (error) {
      setSearchStatus(`❌ ${error.message}`);
      setValidationMessage(error.message);
      onResults([]);
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
    setCurrentPage(newPage);
    fetchSearchResults(newPage);
  };

  return (
    <div style={{ textAlign: "center" }}>
      <form onSubmit={handleSubmit}>
        <label style={{ color: "white" }}>
          Tipo de búsqueda:&nbsp;
          <select
            value={searchType}
            onChange={(e) => { setSearchType(e.target.value); setValidationMessage(null); }}
            style={{ padding: "6px", borderRadius: "4px" }}
            disabled={isSearching}
          >
            <option value="title">Por título</option>
            <option value="keyword">Por keyword</option>
            <option value="spatial">Por dos SpatialWords</option>
          </select>
        </label>

        <div style={{ marginTop: "10px" }}>
          {searchType === "title" && (
            <input
              type="text"
              name="title"
              placeholder="Título del dataset"
              value={formValues.title}
              onChange={handleInputChange}
              disabled={isSearching}
              style={{ padding: "6px", borderRadius: "4px", width: "60%" }}
            />
          )}

          {searchType === "keyword" && (
            <input
              type="text"
              name="keyword"
              placeholder="Keyword"
              value={formValues.keyword}
              onChange={handleInputChange}
              disabled={isSearching}
              style={{ padding: "6px", borderRadius: "4px", width: "60%" }}
            />
          )}

          {searchType === "spatial" && (
            <>
              <select
                name="spatial1"
                value={formValues.spatial1}
                onChange={handleInputChange}
                disabled={isSearching}
                style={{ padding: "6px", borderRadius: "4px", marginRight: "10px" }}
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
                disabled={isSearching}
                spellCheck={true}
                autoCorrect="on"
                autoComplete="on"
                style={{ padding: "6px", borderRadius: "4px", width: "40%" }}
              />
            </>
          )}
        </div>

        {validationMessage && (
          <p style={{ color: "orange", marginTop: "10px" }}>{validationMessage}</p>
        )}

        <button
          type="submit"
          disabled={isSearching}
          style={{
            marginTop: "10px",
            padding: "8px 16px",
            backgroundColor: isSearching ? "#cccccc" : "#1976d2",
            color: "white",
            border: "none",
            borderRadius: "4px",
            cursor: isSearching ? "not-allowed" : "pointer",
          }}
        >
          {isSearching ? "Buscando..." : "Buscar datasets"}
        </button>
      </form>
      {/* Paginación simple */}
      {hasSearched && totalPages > 1 && (
        <div style={{marginTop: "10px"}}>
          <button
            onClick={() => handlePageChange(Math.max(0, currentPage - 1))}
            disabled={currentPage === 0 || isSearching}
          >
            Página anterior
          </button>
          <span style={{margin: "0 10px"}}>Página {currentPage + 1} de {totalPages}</span>
          <button
            onClick={() => handlePageChange(Math.min(totalPages - 1, currentPage + 1))}
            disabled={currentPage >= totalPages - 1 || isSearching}
          >
            Página siguiente
          </button>
        </div>
      )}
    </div>
  );
}

export default SearchComponent;
