import { useState } from "react";
import SearchComponent from "../components/SearchComponent";

function SearchAndFilter() {
  const [searchResults, setSearchResults] = useState([]);

  return (
    <div style={{ padding: "20px", backgroundColor: "#242424", minHeight: "100vh", color: "white" }}>
      <h1>🔍 Buscar datasets desde datos.gob.es</h1>
      <SearchComponent onResults={setSearchResults} />
      {/* Mostrar lista similar */}
      <div>
        {searchResults.length > 0 ? (
          <ul>
            {searchResults.map((item, i) => (
              <li key={i}>{item.title || "Dataset sin título"}</li>
            ))}
          </ul>
        ) : (
          <p style={{ marginTop: "20px" }}>No hay resultados</p>
        )}
      </div>
    </div>
  );
}

export default SearchAndFilter;
