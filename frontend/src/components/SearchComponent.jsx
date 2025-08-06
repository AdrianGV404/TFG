import { useState, useEffect } from "react";
import {
  search_by_title,
  search_by_keyword,
  search_by_spatial,
  search_by_category,  // Importa la nueva funcion
} from "../api/backendService";

const ITEMS_PER_PAGE = 10;

const getValue = (field) => {
  if (!field) return "";
  if (typeof field === "string") return field;
  if (Array.isArray(field)) {
    const spanish = field.find((item) => item._lang === "es");
    const english = field.find((item) => item._lang === "en");
    return spanish?._value || english?._value || field[0]?._value || "";
  }
  return field._value || "";
};

function SearchComponent({ onResults, onError, categoria }) {
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
  const [hasSearched, setHasSearched] = useState(false);

  // Cada vez que cambia la categoría, se reinicia la búsqueda y página,
  // pero NO se cambia searchType ni valores del formulario.
  useEffect(() => {
    if (categoria) {
      setValidationMessage(null);
      setHasSearched(true);
      setSearchStatus(null);
      onResults([]);
      setCurrentPage(0);
      fetchSearchResults(0, categoria);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [categoria]);

  // Buscar cuando cambia la página
  useEffect(() => {
    if (hasSearched) {
      fetchSearchResults(currentPage, categoria);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [currentPage]);

  const fetchSearchResults = async (page, categoriaParam) => {
    setValidationMessage(null);
    setSearchStatus("Buscando...");
    setIsSearching(true);

    try {
      let resultados;

      // Si hay categoría, buscar por categoría usando la nueva API
      if (categoriaParam) {
        resultados = await search_by_category(categoriaParam, page);
      } else {
        // Si no hay categoría, usar el tipo de búsqueda y formulario habituales
        if (searchType === "title" && !formValues.title.trim()) {
          throw new Error("Por favor, introduce un título para la búsqueda.");
        }
        if (searchType === "keyword" && !formValues.keyword.trim()) {
          throw new Error("Por favor, introduce una keyword para la búsqueda.");
        }
        if (searchType === "spatial" && !formValues.spatial2.trim()) {
          throw new Error("Por favor, introduce el nombre del espacio geográfico.");
        }

        if (searchType === "title") {
          resultados = await search_by_title(formValues.title.trim(), page);
        } else if (searchType === "keyword") {
          resultados = await search_by_keyword(formValues.keyword.trim(), page);
        } else if (searchType === "spatial") {
          resultados = await search_by_spatial(
            formValues.spatial1,
            formValues.spatial2.trim(),
            page
          );
        } else {
          throw new Error("Tipo de búsqueda no implementado");
        }
      }

      if (!resultados || typeof resultados !== "object") {
        throw new Error("Respuesta inválida del servidor");
      }

      const items = Array.isArray(resultados.result?.items)
        ? resultados.result.items
        : [];

      const processedItems = items.map((item) => ({
        ...item,
        processedTitle: getValue(item.title),
        processedDescription: getValue(item.description),
        processedLink:
          getValue(item.distribution)?.accessURL ||
          (Array.isArray(item.distribution)
            ? getValue(item.distribution[0]?.accessURL)
            : ""),
      }));

      onResults(processedItems);
      setSearchStatus(`✅ Búsqueda completada (${items.length} resultados)`);

      if (typeof resultados.items_count === "number" && resultados.items_count > 0) {
        setTotalPages(Math.ceil(resultados.items_count / ITEMS_PER_PAGE));
      } else if (
        typeof resultados.items_count === "number" &&
        resultados.items_count === 0
      ) {
        setTotalPages(1);
      }
      // No cambiar totalPages en otros casos para evitar romper paginación
    } catch (error) {
      console.error("Error en la búsqueda:", error);
      setSearchStatus(`❌ ${error.message}`);
      setValidationMessage(error.message);
      if (onError) onError(error.message);
      onResults([]);
      // No cambiar totalPages aquí para no bloquear navegación
    } finally {
      setIsSearching(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues((prev) => ({ ...prev, [name]: value }));
    setValidationMessage(null);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setHasSearched(true);
    setCurrentPage(0);
    fetchSearchResults(0, categoria);
  };

  const handlePageChange = (newPage) => {
    if (newPage < 0) return;
    if (totalPages !== null && newPage >= totalPages) return;
    setCurrentPage(newPage);
    setHasSearched(true);
  };

  // Deshabilitar inputs cuando se usa categoria para evitar editar tipo/inputs
  const isCategorySearch = Boolean(categoria);

  return (
    <div style={{ textAlign: "center" }}>
      <form onSubmit={handleSubmit}>
        <label style={{ color: "white" }}>
          Tipo de búsqueda:&nbsp;
          <select
            value={searchType}
            onChange={(e) => {
              setSearchType(e.target.value);
              setValidationMessage(null);
              setHasSearched(false);
              onResults([]);
            }}
            style={{ padding: "6px", borderRadius: "4px" }}
            disabled={isSearching || isCategorySearch}
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
              disabled={isSearching || isCategorySearch}
              style={{ padding: "6px", borderRadius: "4px", width: "60%" }}
              autoComplete="off"
            />
          )}

          {searchType === "keyword" && (
            <input
              type="text"
              name="keyword"
              placeholder="Keyword"
              value={formValues.keyword}
              onChange={handleInputChange}
              disabled={isSearching || isCategorySearch}
              style={{ padding: "6px", borderRadius: "4px", width: "60%" }}
              autoComplete="off"
            />
          )}

          {searchType === "spatial" && (
            <>
              <select
                name="spatial1"
                value={formValues.spatial1}
                onChange={handleInputChange}
                disabled={isSearching || isCategorySearch}
                style={{ padding: "6px", borderRadius: "4px", marginRight: "10px" }}
              >
                <option value="Autonomia">Autonomía</option>
                <option value="Provincia">Provincia</option>
                <option value="Municipio">Municipio</option>
              </select>
              <input
                type="text"
                name="spatial2"
                placeholder="Nombre espacio geográfico"
                value={formValues.spatial2}
                onChange={handleInputChange}
                disabled={isSearching || isCategorySearch}
                style={{ padding: "6px", borderRadius: "4px", width: "50%" }}
                autoComplete="off"
              />
            </>
          )}
        </div>

        <div style={{ marginTop: "15px" }}>
          <button
            type="submit"
            disabled={isSearching}
            style={{
              backgroundColor: "#646cff",
              color: "white",
              border: "none",
              padding: "10px 20px",
              borderRadius: "5px",
              cursor: isSearching ? "not-allowed" : "pointer",
              fontWeight: "bold",
            }}
          >
            {isSearching ? "Buscando..." : "Buscar datasets"}
          </button>
        </div>

        {validationMessage && (
          <p style={{ color: "orange", marginTop: "10px" }}>{validationMessage}</p>
        )}

        {searchStatus && (
          <p
            style={{
              marginTop: "10px",
              fontWeight: "bold",
              color: searchStatus.includes("✅") ? "#4CAF50" : "#f44336",
            }}
          >
            {searchStatus}
          </p>
        )}
      </form>

      {hasSearched && (
        <div style={{ marginTop: "20px" }}>
          <button
            onClick={() => handlePageChange(currentPage - 1)}
            disabled={currentPage === 0 || isSearching}
            style={{
              marginRight: "10px",
              padding: "6px 12px",
              backgroundColor: currentPage === 0 ? "#cccccc" : "#646cff",
              color: "white",
              border: "none",
              borderRadius: "4px",
              cursor: currentPage === 0 ? "not-allowed" : "pointer",
            }}
          >
            Anterior
          </button>
          <span>
            Página {currentPage + 1}
            {totalPages ? ` de ${totalPages}` : ""}
          </span>
          <button
            onClick={() => handlePageChange(currentPage + 1)}
            disabled={isSearching || (totalPages !== null && currentPage + 1 >= totalPages)}
            style={{
              marginLeft: "10px",
              padding: "6px 12px",
              backgroundColor:
                isSearching || (totalPages !== null && currentPage + 1 >= totalPages)
                  ? "#cccccc"
                  : "#646cff",
              color: "white",
              border: "none",
              borderRadius: "4px",
              cursor:
                isSearching || (totalPages !== null && currentPage + 1 >= totalPages)
                  ? "not-allowed"
                  : "pointer",
            }}
          >
            Siguiente
          </button>
        </div>
      )}
    </div>
  );
}

export default SearchComponent;
